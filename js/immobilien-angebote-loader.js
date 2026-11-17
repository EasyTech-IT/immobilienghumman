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
            total = Number(result.total || (recent.length + archived.length));
        }
    } catch (error) {
        console.warn('API error, will try static fallback:', error.message);
    }

    propertiesContainer.innerHTML = '';

    if (total === 0) {
        propertiesContainer.innerHTML = `
            <div class="empty-state">
                <h3>Derzeit keine Immobilien verfügbar</h3>
                <p>Schauen Sie bald wieder vorbei oder kontaktieren Sie uns für individuelle Anfragen.</p>
                <a href="${window.SITE_BASE||''}/#kontaktFormNew" class="cta-button" style="margin-top: 20px;">Kontakt aufnehmen</a>
            </div>
        `;
        document.dispatchEvent(new CustomEvent('propertiesRendered'));
        return;
    }

    const grid = document.createElement('div');
    grid.className = 'properties-grid-inner';
    propertiesContainer.appendChild(grid);

    [...recent.map(property => ({ property, isNew: true })), ...archived.map(property => ({ property, isNew: false }))]
        .forEach(({ property, isNew }) => {
            grid.appendChild(createAngebotePropertyCard(convertPropertyData(property, isNew)));
        });

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
        zip: property.zip || '',
        price: Number(property.price || 0),
        warmRent: Number(property.warm_rent || 0),
        pricingType: normalizedOfferType === 'kauf' ? 'Kaufpreis' : 'Kaltmiete',
        offerType: normalizedOfferType,
        rooms: Number(property.rooms || 0),
        area: Number(property.area || property.size || 0),
        type: normalizedType,
        image: property.main_image || property.primary_image || imageFromArray || '',
        images: property.images || [],
        description: property.description || '',
        shortDescription: property.short_description || '',
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
    card.dataset.pricingType = property.offerType;
    card.dataset.price = property.price;
    card.dataset.area = property.area;
    card.dataset.createdAt = property.createdAt || '';
    card.dataset.location = property.location;
    card.dataset.search = [
        property.title,
        property.locationName,
        property.zip,
        property.shortDescription,
        property.description,
        ...(Array.isArray(property.features) ? property.features : [])
    ].join(' ').toLocaleLowerCase('de-DE');

    const formatPrice = (price) => {
        return new Intl.NumberFormat('de-DE').format(price);
    };

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    })[char]);

    const offerLabel = property.offerType === 'kauf' ? 'Kaufen' : 'Mieten';
    const priceLabel = property.offerType === 'kauf' ? 'Kaufpreis' : 'Kaltmiete';
    const formatMeasure = (value) => new Intl.NumberFormat('de-DE', { maximumFractionDigits: 1 }).format(value);
    const areaLabel = property.area > 0 ? `${formatMeasure(property.area)} m²` : '—';
    const roomsLabel = property.rooms > 0 ? formatMeasure(property.rooms) : '—';
    const priceValue = property.price > 0 ? `${formatPrice(property.price)} €` : 'Auf Anfrage';
    const newBadgeHTML = property.isNew ? '<span class="new-badge">NEU</span>' : '';
    const locationLabel = [property.locationName, property.zip].filter(Boolean).join(' · ');
    const objectNumber = `Objekt ${String(property.id || '').padStart(4, '0')}`;
    const detailUrl = `${window.SITE_BASE || ''}/${property.slug
        ? `immobilie/${encodeURIComponent(property.slug)}`
        : `property-detail.php?id=${encodeURIComponent(property.id)}`}`;

    card.style.position = 'relative';
    card.innerHTML = `
        <a href="${escapeHtml(detailUrl)}" class="property-card-link-overlay" aria-label="${escapeHtml(`${objectNumber} in ${locationLabel} ansehen`)}"></a>
        <div class="property-image">
            ${property.image
                ? `<img src="${escapeHtml(property.image)}" alt="Immobilienangebot in ${escapeHtml(locationLabel)}" loading="lazy" decoding="async">`
                : '<div class="property-image-placeholder" aria-hidden="true"><i class="fas fa-house"></i></div>'}
            <span class="property-offer-badge">${offerLabel}</span>
            ${newBadgeHTML}
            <button type="button" class="property-card-fav fav-btn" data-fav-id="${escapeHtml(property.id)}" aria-label="${escapeHtml(objectNumber)} merken" title="Zu Favoriten hinzufügen">
                <i class="far fa-heart"></i>
            </button>
            <div class="property-image-location"><i class="fas fa-location-dot" aria-hidden="true"></i> ${escapeHtml(locationLabel)}</div>
        </div>
        <div class="property-content">
            <p class="property-reference">${escapeHtml(objectNumber)}</p>
            <div class="property-facts">
                <div class="property-fact">
                    <i class="fas fa-tag" aria-hidden="true"></i>
                    <div><strong>${priceValue}</strong><span>${priceLabel}</span></div>
                </div>
                <div class="property-fact">
                    <i class="far fa-square" aria-hidden="true"></i>
                    <div><strong>${areaLabel}</strong><span>Wohnfläche</span></div>
                </div>
                <div class="property-fact">
                    <i class="fas fa-door-open" aria-hidden="true"></i>
                    <div><strong>${roomsLabel}</strong><span>Zimmer</span></div>
                </div>
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
