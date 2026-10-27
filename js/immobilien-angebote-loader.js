/**
 * IMMOBILIEN ANGEBOTE LOADER
 * Lädt und kategorisiert alle Immobilien für die Angebote-Seite
 * 
 * Kategorien:
 * - NEU: Immobilien, die weniger als 14 Tage alt sind
 * - ARCHIV: Ältere Immobilien
 */

document.addEventListener('DOMContentLoaded', async function () {
    await loadAllProperties();
});

/**
 * Lädt ALLE Immobilien für die Angebote-Seite
 * Sortiert nach Datum: Neueste zuerst, ältere weiter unten
 * Datenquelle: ausschließlich API/DB
 */
async function loadAllProperties() {
    const propertiesContainer = document.getElementById('properties-grid') || document.getElementById('propertiesGrid');
    if (!propertiesContainer) {
        console.warn('Properties container not found');
        return;
    }

    propertiesContainer.innerHTML = `
        <div class="skeleton-grid">
            ${Array(6).fill('<div class="skeleton-card"><div class="skeleton skeleton-img"></div><div class="skeleton-body"><div class="skeleton skeleton-line w-80"></div><div class="skeleton skeleton-line w-60"></div><div class="skeleton skeleton-line w-40"></div></div></div>').join('')}
        </div>`;

    let recent = [];
    let archived = [];
    let total = 0;

    try {
        // Check if API client is available
        if (typeof PropertyAPI !== 'undefined') {
            const api = new PropertyAPI();
            // Kategorisierte Immobilien laden (neu vs. archiviert)
            const result = await api.getCategorizedProperties(14); // 14 Tage Schwelle
            recent = result.recent || [];
            archived = result.archived || [];
            total = result.total || 0;
        }
    } catch (error) {
        console.warn('API error, will try static fallback:', error.message);
    }

    propertiesContainer.innerHTML = '';

    if (total === 0) {
        propertiesContainer.innerHTML = `
            <div class="empty-state">
                <h3>🏠 Derzeit keine Immobilien verfügbar</h3>
                <p>Schauen Sie bald wieder vorbei oder kontaktieren Sie uns für individuelle Anfragen.</p>
                <a href="${window.SITE_BASE||''}/#kontaktFormNew" class="cta-button" style="margin-top: 20px;">Kontakt aufnehmen</a>
            </div>
        `;
        return;
    }

    // === NEUE IMMOBILIEN SEKTION ===
    if (recent.length > 0) {
        const newSection = document.createElement('div');
        newSection.className = 'property-section new-properties-section';
        newSection.innerHTML = `
            <h3 class="section-subtitle">🆕 Neue Angebote <span class="count-badge">${recent.length}</span></h3>
            <div class="properties-grid-inner" id="new-properties-grid"></div>
        `;
        propertiesContainer.appendChild(newSection);

        const newGrid = document.getElementById('new-properties-grid');
        recent.forEach(property => {
            const propertyData = convertPropertyData(property, true);
            const card = createAngebotePropertyCard(propertyData);
            newGrid.appendChild(card);
        });
    }

    // === WEITERE/ARCHIVIERTE IMMOBILIEN SEKTION ===
    if (archived.length > 0) {
        const archiveSection = document.createElement('div');
        archiveSection.className = 'property-section archive-section';
        archiveSection.innerHTML = `
            <h3 class="section-subtitle">📁 Weitere Angebote <span class="count-badge">${archived.length}</span></h3>
            <div class="properties-grid-inner" id="archive-properties-grid"></div>
        `;
        propertiesContainer.appendChild(archiveSection);

        const archiveGrid = document.getElementById('archive-properties-grid');
        archived.forEach(property => {
            const propertyData = convertPropertyData(property, false);
            const card = createAngebotePropertyCard(propertyData);
            archiveGrid.appendChild(card);
        });
    }

    console.log(`✅ Loaded ${total} properties (${recent.length} new, ${archived.length} archived)`);

    // Inform favorites.js that new cards are in the DOM
    document.dispatchEvent(new CustomEvent('propertiesRendered'));
}

/**
 * Konvertiert API Property-Daten in Card-Format
 * @param {Object} property - Raw property data from API
 * @param {boolean} isNew - Whether to show "NEU" badge
 * @returns {Object} - Formatted property data
 */
function convertPropertyData(property, isNew = false) {
    const normalizedType = String(property.type || '').toLowerCase();
    const normalizedOfferType = String(property.offer_type || '').toLowerCase();
    const locationName = property.location || property.city || 'Unbekannt';
    const imageFromArray = Array.isArray(property.images) && property.images.length > 0
        ? (typeof property.images[0] === 'string' ? property.images[0] : (property.images[0]?.url || ''))
        : '';

    return {
        id: property.id,
        slug: property.slug || '',
        title: property.title,
        location: locationName.toLowerCase().replace(/\s+/g, '-'),
        locationName: locationName,
        price: Number(property.price || 0),
        pricingType: normalizedOfferType === 'kauf' ? 'Kaufpreis' : 'Kaltmiete',
        rooms: Number(property.rooms || 0),
        area: Number(property.area || property.size || 0),
        type: normalizedType,
        image: property.main_image || property.primary_image || imageFromArray ||
                'https://via.placeholder.com/400x300?text=Kein+Bild',
        images: property.images || [],
        description: property.description || '',
        features: property.features || [],
        isNew: isNew,
        createdAt: property.created_at
    };
}

/**
 * Erstellt eine Property Card für die Angebote-Seite
 * @param {Object} property - Property data
 * @returns {HTMLElement} - Property card element
 */
function createAngebotePropertyCard(property) {
    const card = document.createElement('div');
    card.className = 'property-card';
    card.dataset.type = property.type;
    card.dataset.price = property.price;
    card.dataset.location = property.location;

    const formatPrice = (price) => {
        return new Intl.NumberFormat('de-DE').format(price);
    };

    const getTypeLabel = (type) => {
        const types = {
            'haus': 'Haus',
            'wohnung': 'Wohnung',
            'gewerbe': 'Gewerbe',
            'grundstück': 'Grundstück'
        };
        return types[type] || type;
    };

    // Truncate description if too long
    const shortDescription = property.description.length > 120
        ? property.description.substring(0, 120) + '...'
        : property.description;

    // NEU-Badge HTML nur wenn property.isNew = true
    const newBadgeHTML = property.isNew ? '<span class="new-badge">NEU</span>' : '';
    const areaLabel = property.area ? `${property.area} m²` : 'Fläche k.A.';

    card.style.position = 'relative';
    card.innerHTML = `
        <div class="property-image">
            <img src="${property.image}" alt="${property.title}" loading="lazy">
            ${newBadgeHTML}
            <button class="property-card-fav fav-btn" data-fav-id="${property.id}" aria-label="Favorit" title="Zu Favoriten hinzufügen">
                <i class="far fa-heart"></i>
            </button>
        </div>
        <div class="property-content">
            <div class="property-price">€ ${formatPrice(property.price)}${property.pricingType ? ` / ${property.pricingType}` : ''}</div>
            <h3 class="property-title">${property.title}</h3>
            <div class="property-location">📍 ${property.locationName}</div>
            <div class="property-features">
                ${property.rooms > 0 ? `<span>🏠 ${property.rooms} Zimmer</span>` : ''}
                <span>📐 ${areaLabel}</span>
                <span>🏷️ ${getTypeLabel(property.type)}</span>
            </div>
            <p class="property-description">${shortDescription}</p>
            <div class="property-actions">
                <a href="${window.SITE_BASE||''}/${property.slug ? ('immobilie/' + encodeURIComponent(property.slug)) : ('property-detail.php?id=' + property.id)}" class="cta-button">Details ansehen</a>
            </div>
        </div>
    `;

    return card;
}

/**
 * Filter properties by type
 * @param {string} type - Property type to filter
 */
function filterByType(type) {
    const cards = document.querySelectorAll('.property-card');

    cards.forEach(card => {
        if (type === 'all' || card.dataset.type === type) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });

    // Update active filter button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.filter === type) {
            btn.classList.add('active');
        }
    });
}
