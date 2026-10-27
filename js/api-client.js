// API Configuration – dynamic base URL injected by PHP via window.API_BASE_URL
const API_BASE_URL = (typeof window !== 'undefined' && window.API_BASE_URL) ? window.API_BASE_URL : '/api';

// Produktions-Modus erkennen (keine console.logs auf Live-Site)
const IS_PRODUCTION = true;

// Debug-Logger (nur in Entwicklung aktiv)
const debugLog = (...args) => {
    if (!IS_PRODUCTION) {
        console.log('[API]', ...args);
    }
};

const debugError = (...args) => {
    // Fehler immer loggen, aber mit weniger Details in Produktion
    if (IS_PRODUCTION) {
        console.error('[API Error]', args[0]);
    } else {
        console.error('[API]', ...args);
    }
};

let authToken = localStorage.getItem('authToken');

// API Client
class PropertyAPI {
    constructor() {
        this.baseURL = API_BASE_URL;
        this.token = authToken;
    }

    /**
     * Aktuelle/Neue Immobilien laden (für Startseite)
     * @param {number} limit - Maximale Anzahl der Immobilien
     * @returns {Promise} - Liste der neuesten Immobilien
     */
    async getRecentProperties(limit = 6) {
        return this.getProperties({
            status: 'available',
            limit: limit,
            sort: 'newest'
        });
    }

    /**
     * Alle Immobilien laden (für Archiv/Angebote-Seite)
     * @param {number} limit - Maximale Anzahl
     * @param {number} offset - Offset für Pagination
     * @returns {Promise} - Liste aller verfügbaren Immobilien
     */
    async getAllProperties(limit = 100, offset = 0) {
        return this.getProperties({
            status: 'available',
            limit: limit,
            offset: offset,
            sort: 'newest'
        });
    }

    /**
     * Immobilien nach Datum kategorisieren (neu vs. archiviert)
     * @param {number} daysThreshold - Tage für "Neu" Definition (Standard: 14)
     * @returns {Promise} - Objekt mit 'recent' und 'archived' Arrays
     */
    async getCategorizedProperties(daysThreshold = 14) {
        const response = await this.getProperties({
            status: 'available',
            limit: 200,
            sort: 'newest'
        });

        const properties = response.properties || [];
        const now = new Date();
        const thresholdDate = new Date(now.getTime() - daysThreshold * 24 * 60 * 60 * 1000);

        const recent = [];
        const archived = [];

        properties.forEach(property => {
            const createdAt = new Date(property.created_at);
            if (createdAt >= thresholdDate) {
                recent.push(property);
            } else {
                archived.push(property);
            }
        });

        return { recent, archived, total: properties.length };
    }

    // Set auth token
    setToken(token) {
        this.token = token;
        if (token) {
            localStorage.setItem('authToken', token);
        } else {
            localStorage.removeItem('authToken');
        }
    }

    // Get auth headers
    getHeaders(includeAuth = true) {
        const headers = {
            'Content-Type': 'application/json'
        };

        if (includeAuth && this.token) {
            headers['Authorization'] = `Bearer ${this.token}`;
        }

        return headers;
    }

    // Handle API response
    async handleResponse(response) {
        const raw = await response.text();
        const cleaned = (raw || '').replace(/^\uFEFF+/, '').trim();
        let data = {};
        try {
            data = cleaned ? JSON.parse(cleaned) : {};
        } catch (e) {
            data = {
                error: cleaned || `HTTP ${response.status}`
            };
        }

        if (!response.ok) {
            // Handle authentication errors
            if (response.status === 401) {
                this.setToken(null);
                // Nur weiterleiten wenn nicht bereits auf Login-Seite
                if (!window.location.pathname.includes('/admin/login.php')) {
                    window.location.href = `${window.SITE_BASE || ''}/admin/login.php`;
                }
            }
            throw new Error(data.error || 'API Fehler');
        }

        return data;
    }

    // Authentication
    async login(username, password) {
        const response = await fetch(`${this.baseURL}/auth.php`, {
            method: 'POST',
            headers: this.getHeaders(false),
            body: JSON.stringify({ username, password })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Login fehlgeschlagen');
        }

        // Token speichern
        if (data.token) {
            this.setToken(data.token);
        }

        return data;
    }

    async logout() {
        this.setToken(null);
        return { message: 'Erfolgreich abgemeldet' };
    }

    async getCurrentUser() {
        const response = await fetch(`${this.baseURL}/auth.php?action=me`, {
            headers: this.getHeaders()
        });
        return this.handleResponse(response);
    }

    // Properties
    async getProperties(filters = {}) {
        const params = new URLSearchParams();
        Object.keys(filters).forEach(key => {
            if (filters[key] !== null && filters[key] !== undefined && filters[key] !== '') {
                params.append(key, filters[key]);
            }
        });

        const response = await fetch(`${this.baseURL}/properties.php?${params}`, {
            headers: this.getHeaders(false)
        });
        return this.handleResponse(response);
    }

    async getProperty(id) {
        const response = await fetch(`${this.baseURL}/properties.php?id=${id}`, {
            headers: this.getHeaders(false)
        });
        return this.handleResponse(response);
    }

    async getPropertyBySlug(slug) {
        const response = await fetch(`${this.baseURL}/properties.php?slug=${encodeURIComponent(slug)}`, {
            headers: this.getHeaders(false)
        });
        return this.handleResponse(response);
    }

    async createProperty(propertyData) {
        const response = await fetch(`${this.baseURL}/properties.php`, {
            method: 'POST',
            headers: this.getHeaders(),
            body: JSON.stringify(propertyData)
        });
        return this.handleResponse(response);
    }

    async updateProperty(id, propertyData) {
        const response = await fetch(`${this.baseURL}/properties.php?id=${id}`, {
            method: 'PUT',
            headers: this.getHeaders(),
            body: JSON.stringify(propertyData)
        });
        return this.handleResponse(response);
    }

    async deleteProperty(id) {
        const response = await fetch(`${this.baseURL}/properties.php?id=${id}`, {
            method: 'DELETE',
            headers: this.getHeaders()
        });
        return this.handleResponse(response);
    }

    // Image Upload
    async uploadPropertyImages(propertyId, files) {
        const formData = new FormData();
        formData.append('property_id', propertyId);

        for (let i = 0; i < files.length; i++) {
            formData.append('images', files[i]);
        }

        const response = await fetch(`${this.baseURL}/upload.php`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.token}`
            },
            body: formData
        });

        return this.handleResponse(response);
    }

    async uploadSingleImage(file) {
        const formData = new FormData();
        formData.append('image', file);

        const response = await fetch(`${this.baseURL}/upload.php`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.token}`
            },
            body: formData
        });

        return this.handleResponse(response);
    }

    async deleteImage(cloudinaryId) {
        const response = await fetch(`${this.baseURL}/upload.php?action=delete&id=${cloudinaryId.replace(/\//g, '-')}`, {
            method: 'DELETE',
            headers: this.getHeaders()
        });
        return this.handleResponse(response);
    }

    // Contact/Inquiries
    async submitInquiry(inquiryData) {
        const response = await fetch(`${this.baseURL}/contact.php?action=inquiry`, {
            method: 'POST',
            headers: this.getHeaders(false),
            body: JSON.stringify(inquiryData)
        });
        return this.handleResponse(response);
    }

    async getInquiries(filters = {}) {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${this.baseURL}/contact.php?action=inquiries&${params}`, {
            headers: this.getHeaders()
        });
        return this.handleResponse(response);
    }

    async getInquiry(id) {
        const response = await fetch(`${this.baseURL}/contact.php?action=inquiry&id=${id}`, {
            headers: this.getHeaders()
        });
        return this.handleResponse(response);
    }

    async updateInquiry(id, data) {
        const response = await fetch(`${this.baseURL}/contact.php?action=inquiry&id=${id}`, {
            method: 'PUT',
            headers: this.getHeaders(),
            body: JSON.stringify(data)
        });
        return this.handleResponse(response);
    }

    async getInquiryStats() {
        const response = await fetch(`${this.baseURL}/contact.php?action=stats`, {
            headers: this.getHeaders()
        });
        return this.handleResponse(response);
    }

    // Users (Admin only)
    async getUsers(filters = {}) {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${this.baseURL}/users?${params}`, {
            headers: this.getHeaders()
        });
        return this.handleResponse(response);
    }

    async getUser(id) {
        const response = await fetch(`${this.baseURL}/users/${id}`, {
            headers: this.getHeaders()
        });
        return this.handleResponse(response);
    }

    async createUser(userData) {
        const response = await fetch(`${this.baseURL}/users`, {
            method: 'POST',
            headers: this.getHeaders(),
            body: JSON.stringify(userData)
        });
        return this.handleResponse(response);
    }

    async updateUser(id, userData) {
        const response = await fetch(`${this.baseURL}/users/${id}`, {
            method: 'PUT',
            headers: this.getHeaders(),
            body: JSON.stringify(userData)
        });
        return this.handleResponse(response);
    }

    async deleteUser(id) {
        const response = await fetch(`${this.baseURL}/users/${id}`, {
            method: 'DELETE',
            headers: this.getHeaders()
        });
        return this.handleResponse(response);
    }

    async getUserStats() {
        const response = await fetch(`${this.baseURL}/users/stats/overview`, {
            headers: this.getHeaders()
        });
        return this.handleResponse(response);
    }
}

// Create global API instance
const api = new PropertyAPI();

// Display properties on page
async function displayProperties(container, filters = {}) {
    try {
        const data = await api.getProperties(filters);
        const properties = data.properties;

        if (!properties || properties.length === 0) {
            container.innerHTML = '<p class="no-properties">Keine Immobilien gefunden.</p>';
            return;
        }

        container.innerHTML = properties.map(property => createPropertyCard(property)).join('');

    } catch (error) {
        console.error('Error loading properties:', error);
        container.innerHTML = '<p class="error">Fehler beim Laden der Immobilien.</p>';
    }
}

// Create property card HTML
function createPropertyCard(property) {
    const primaryImage = property.images?.[0] || 'https://via.placeholder.com/400x300?text=Kein+Bild';
    const imageUrl = typeof primaryImage === 'string' ? primaryImage : primaryImage.image_url;

    return `
        <div class="property-card" data-id="${property.id}">
            <div class="property-image">
                <img src="${imageUrl}" alt="${property.title}" loading="lazy" decoding="async">
                ${property.featured ? '<span class="badge featured">Empfohlen</span>' : ''}
                ${property.status === 'sold' ? '<span class="badge sold">Verkauft</span>' : ''}
                ${property.status === 'reserved' ? '<span class="badge reserved">Reserviert</span>' : ''}
            </div>
            <div class="property-info">
                <h3>${property.title}</h3>
                <p class="location">📍 ${property.location}</p>
                <p class="price">€${parseInt(property.price).toLocaleString('de-DE')}</p>
                <div class="property-features">
                    <span>🏠 ${property.type}</span>
                    <span>📐 ${property.size} m²</span>
                    <span>🛏️ ${property.rooms} Zimmer</span>
                </div>
                <div class="property-actions">
                    <a href="${window.SITE_BASE||''}/${property.slug ? ('immobilie/' + encodeURIComponent(property.slug)) : ('property-detail.php?id=' + property.id)}" class="btn-primary">Details ansehen</a>
                </div>
            </div>
        </div>
    `;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', async () => {
    // Auto-display properties if container exists
    const propertyContainer = document.querySelector('.property-grid');
    if (propertyContainer) {
        // Get filters from URL params
        const params = new URLSearchParams(window.location.search);
        const filters = {
            type: params.get('type'),
            offer_type: params.get('offer_type'),
            city: params.get('city'),
            min_price: params.get('min_price'),
            max_price: params.get('max_price'),
            featured: params.get('featured')
        };

        await displayProperties(propertyContainer, filters);
    }

    // Property detail page
    const propertyDetail = document.querySelector('.property-detail');
    if (propertyDetail) {
        const params = new URLSearchParams(window.location.search);
        const propertyId = params.get('id');

        if (propertyId) {
            try {
                const data = await api.getProperty(propertyId);
                displayPropertyDetail(data.property);
            } catch (error) {
                console.error('Error loading property:', error);
                propertyDetail.innerHTML = '<p class="error">Immobilie nicht gefunden.</p>';
            }
        }
    }

    // Contact form submission
    const inquiryForm = document.querySelector('#inquiry-form');
    if (inquiryForm) {
        inquiryForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(inquiryForm);
            const data = Object.fromEntries(formData);

            try {
                const result = await api.submitInquiry(data);
                alert('Ihre Anfrage wurde erfolgreich gesendet!');
                inquiryForm.reset();
            } catch (error) {
                alert('Fehler beim Senden der Anfrage: ' + error.message);
            }
        });
    }
});

// Display property detail
function displayPropertyDetail(property) {
    // This function would populate the property detail page
    // Implementation depends on your HTML structure
    console.log('Property detail:', property);
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { PropertyAPI, api };
}
