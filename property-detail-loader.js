/**
 * Property Detail Page Loader
 * Loads specific property from database and displays it
 * Datenquelle: ausschließlich API/DB
 */

// Gallery state
let currentImageIndex = 0;
let propertyImages = [];

/**
 * Load property from URL parameter
 */
async function loadPropertyDetails() {
    // Get property ID or slug from URL
    const urlParams = new URLSearchParams(window.location.search);
    const propertyId = urlParams.get('id');
    let slug = urlParams.get('slug');
    if (!slug) {
        const pathMatch = window.location.pathname.match(/\/immobilie\/([^/?#]+)/i);
        if (pathMatch && pathMatch[1]) {
            slug = decodeURIComponent(pathMatch[1]);
        }
    }

    if (!propertyId && !slug) {
        showError('Keine gueltige Immobilien-ID oder kein Slug angegeben');
        return;
    }

    let property = null;

    try {
        // Show loading state
        showLoading();

        // Check if API client is available
        if (typeof PropertyAPI !== 'undefined') {
            const api = new PropertyAPI();
            if (slug) {
                const bySlug = await api.getPropertyBySlug(slug);
                property = bySlug?.property || null;
            } else {
                const byId = await api.getProperty(propertyId);
                property = byId?.property || null;
            }
        }
    } catch (error) {
        console.warn('API error, trying static fallback:', error.message);
    }

    if (!property) {
        showError('Immobilie nicht gefunden');
        return;
    }

    // Display property details
    displayProperty(property);
}

/**
 * Display property details on the page
 */
function displayProperty(property) {
    // Restore hero markup before filling it
    hideLoading();

    const locationName = property.location || property.city || 'Unbekannt';

    // Update page title
    document.title = `${property.title} - ${locationName} | Immobilien Ghumman`;

    // Update hero section
    updateHeroSection(property);

    // Update key facts
    updateKeyFacts(property);

    // Update gallery
    updateGallery(property);

    // Update description (includes ALL data)
    updateDescription(property);

    // Update features
    updateFeatures(property);

    // Update location info (includes ALL location data)
    updateLocation(property);

    // Add complete property info section
    addCompletePropertyInfo(property);

    // Initialize Leaflet map
    initPropertyMap(property);

    // Initialize favorites button
    initFavButton(property);

    // Update structured data
    updateStructuredData(property);

    // Sync detail contact form metadata
    syncInquiryForm(property);

    // Hide loading
    hideLoading();

    // Log property for debugging
    console.log('Property loaded:', property);
}

/**
 * Update hero section
 */
function updateHeroSection(property) {
    const heroSection = document.querySelector('.property-hero');
    const title = document.querySelector('.property-title h1');
    const location = document.querySelector('.property-location');
    const priceElement = document.querySelector('.property-price');



    // Update title
    if (title) {
        const typeLabel = getTypeLabel(property.type);
        title.innerHTML = `${typeLabel}: <span id="typewriter-text-property">${property.title}</span><span class="cursor">|</span>`;
    }

    // Update location
    if (location) {
        location.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle; margin-right: 8px;">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
            </svg>
            ${property.location || property.city || 'Unbekannt'}${property.address ? ', ' + property.address : ''}
        `;
    }

    // Update price
    if (priceElement) {
        const formattedPrice = new Intl.NumberFormat('de-DE').format(property.price);
        const priceType = property.offer_type === 'kauf' ? 'Kaufpreis' : 'Monat';
        priceElement.textContent = `€ ${formattedPrice} / ${priceType}`;
    }
}

function syncInquiryForm(property) {
    const idInput = document.getElementById('inquiry_property_id');
    const titleInput = document.getElementById('inquiry_property_title');
    const linkInput = document.getElementById('inquiry_property_link');
    if (!idInput || !titleInput || !linkInput) return;

    const slugPath = property.slug ? `/immobilie/${encodeURIComponent(property.slug)}` : `/property-detail?id=${encodeURIComponent(property.id)}`;
    const origin = (typeof window !== 'undefined' && window.location && window.location.origin) ? window.location.origin : 'https://www.immobilienghumman.de';

    idInput.value = property.id || '';
    titleInput.value = property.title || '';
    linkInput.value = origin + slugPath;
}

/**
 * Update key facts section - Shows ALL uploaded property data
 */
function updateKeyFacts(property) {
    const keyFactsContainer = document.querySelector('.key-facts');
    if (!keyFactsContainer) return;

    const facts = [];

    // Size (Wohnfläche)
    if (property.size) {
        facts.push({
            icon: `<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-8 16H9v-6h2v6zm4 0h-2v-8h2v8zm4 0h-2V7h2v12z"/>`,
            label: `${property.size} m²`,
            sublabel: 'Wohnfläche'
        });
    }

    // Rooms (Zimmer)
    if (property.rooms) {
        facts.push({
            icon: `<path d="M7 13c1.66 0 3-1.34 3-3S8.66 7 7 7s-3 1.34-3 3 1.34 3 3 3zm12-6h-8v7H9v5h4v-2h8V7z"/>`,
            label: `${property.rooms} Zimmer`,
            sublabel: property.rooms > 1 ? 'Mehrere Zimmer' : 'Ein Zimmer'
        });
    }

    // Bathrooms (Badezimmer)
    if (property.bathrooms) {
        facts.push({
            icon: `<path d="M20 2H4c-1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM7 18c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm0-3c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm0-3c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm10 6c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm0-3c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm0-3c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/>`,
            label: `${property.bathrooms} ${property.bathrooms === 1 ? 'Badezimmer' : 'Bäder'}`,
            sublabel: 'Sanitärräume'
        });
    }

    // Year built (Baujahr)
    if (property.year_built) {
        const age = new Date().getFullYear() - property.year_built;
        facts.push({
            icon: `<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>`,
            label: `Baujahr ${property.year_built}`,
            sublabel: age <= 5 ? 'Neubau' : age <= 15 ? 'Modernisiert' : 'Bestand'
        });
    }

    // Offer type (Miete/Kauf)
    if (property.offer_type) {
        const offerIcon = property.offer_type === 'kauf'
            ? `<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>`
            : `<path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>`;

        facts.push({
            icon: offerIcon,
            label: property.offer_type === 'kauf' ? 'Kaufobjekt' : 'Mietobjekt',
            sublabel: property.offer_type === 'kauf' ? 'Zum Verkauf' : 'Zur Miete'
        });
    }

    // Type (Wohnung/Haus/etc.)
    if (property.type) {
        const typeLabels = {
            'wohnung': { label: 'Wohnung', sublabel: 'Eigentumswohnung' },
            'haus': { label: 'Haus', sublabel: 'Einfamilienhaus' },
            'gewerbe': { label: 'Gewerbe', sublabel: 'Gewerbeobjekt' },
            'grundstück': { label: 'Grundstück', sublabel: 'Bauland' }
        };
        const typeInfo = typeLabels[property.type] || { label: property.type, sublabel: 'Immobilie' };

        facts.push({
            icon: `<path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>`,
            label: typeInfo.label,
            sublabel: typeInfo.sublabel
        });
    }

    // Availability (Verfügbarkeit)
    const availabilityText = property.available_from ? new Date(property.available_from).toLocaleDateString('de-DE') : 'ab sofort';
    facts.push({
        icon: `<path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>`,
        label: 'Verfügbar',
        sublabel: availabilityText
    });

    // Generate HTML
    keyFactsContainer.innerHTML = facts.map(fact => `
        <div class="fact-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="animated-icon">
                ${fact.icon}
            </svg>
            <div>
                <strong>${fact.label}</strong>
                <span>${fact.sublabel}</span>
            </div>
        </div>
    `).join('');
}

/**
 * Update gallery with property images
 */
function updateGallery(property) {
    // Normalize images → [{url, label}] objects
    propertyImages = [];
    if (property.images && property.images.length > 0) {
        propertyImages = property.images.map(img => {
            if (typeof img === 'string')    return { url: img,           label: '' };
            if (img.url)                    return { url: img.url,        label: img.label || '' };
            if (img.image_url)              return { url: img.image_url,  label: img.label || '' };
            return null;
        }).filter(Boolean);
    }
    if (propertyImages.length === 0 && property.primary_image) {
        propertyImages = [{ url: property.primary_image, label: '' }];
    }
    if (propertyImages.length === 0) {
        propertyImages = [{ url: 'https://via.placeholder.com/800x500?text=Kein+Bild', label: '' }];
    }

    currentImageIndex = 0;

    /* ── Main image wrapper ── */
    const mainImage = document.getElementById('main-image');
    const galleryMain = mainImage ? mainImage.parentElement : null;

    if (mainImage) {
        mainImage.src = propertyImages[0].url;
        mainImage.alt = property.title;
    }

    /* ── Room label overlay ── */
    if (galleryMain) {
        let labelEl = galleryMain.querySelector('.gallery-room-label');
        if (!labelEl) {
            labelEl = document.createElement('div');
            labelEl.className = 'gallery-room-label';
            galleryMain.appendChild(labelEl);
        }
        const updateLabel = () => {
            const lbl = propertyImages[currentImageIndex]?.label || '';
            labelEl.textContent = lbl;
            labelEl.style.display = lbl ? 'block' : 'none';
        };
        updateLabel();
        galleryMain._updateLabel = updateLabel;

        /* ── Fullscreen button ── */
        if (!galleryMain.querySelector('.gallery-fullscreen-btn')) {
            const fsBtn = document.createElement('button');
            fsBtn.className = 'gallery-fullscreen-btn';
            fsBtn.innerHTML = '<i class="fas fa-expand"></i>';
            fsBtn.title = 'Vollbild';
            fsBtn.setAttribute('aria-label', 'Galerie im Vollbild öffnen');
            fsBtn.addEventListener('click', () => openLightbox(currentImageIndex, property.title));
            galleryMain.appendChild(fsBtn);
        }

        /* ── Image counter ── */
        let counterEl = galleryMain.querySelector('.gallery-counter');
        if (!counterEl) {
            counterEl = document.createElement('div');
            counterEl.className = 'gallery-counter';
            galleryMain.appendChild(counterEl);
        }
        const updateCounter = () => {
            counterEl.textContent = `${currentImageIndex + 1} / ${propertyImages.length}`;
        };
        updateCounter();
        galleryMain._updateCounter = updateCounter;

        /* ── Touch swipe ── */
        let touchStartX = 0;
        galleryMain.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].clientX; }, { passive: true });
        galleryMain.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - touchStartX;
            if (Math.abs(dx) > 50) dx < 0 ? showImage(currentImageIndex + 1) : showImage(currentImageIndex - 1);
        }, { passive: true });

        /* ── Click to open lightbox ── */
        mainImage && mainImage.addEventListener('click', () => openLightbox(currentImageIndex, property.title));
        if (mainImage) mainImage.style.cursor = 'zoom-in';
    }

    /* ── Thumbnails ── */
    const thumbnailsContainer = document.getElementById('gallery-thumbnails');
    if (thumbnailsContainer) {
        thumbnailsContainer.innerHTML = propertyImages.map((img, index) => `
            <div class="thumbnail ${index === 0 ? 'active' : ''}" data-index="${index}" title="${img.label || 'Bild ' + (index + 1)}">
                <img src="${img.url}" alt="${property.title} - ${img.label || 'Bild ' + (index + 1)}" loading="lazy">
                ${img.label ? `<span class="thumb-label">${img.label}</span>` : ''}
            </div>
        `).join('');
        thumbnailsContainer.addEventListener('click', e => {
            const thumb = e.target.closest('.thumbnail');
            if (thumb) showImage(parseInt(thumb.dataset.index));
        });
    }

    /* ── Keyboard navigation ── */
    document.addEventListener('keydown', e => {
        if (document.getElementById('gallery-lightbox')?.style.display === 'flex') return;
        if (e.key === 'ArrowRight') showImage(currentImageIndex + 1);
        if (e.key === 'ArrowLeft')  showImage(currentImageIndex - 1);
    });
}

/* ── showImage (global, used by prev/next buttons in HTML) ── */
function showImage(index) {
    if (propertyImages.length === 0) return;
    currentImageIndex = (index + propertyImages.length) % propertyImages.length;
    const mainImage = document.getElementById('main-image');
    if (mainImage) {
        mainImage.src = propertyImages[currentImageIndex].url;
    }
    const galleryMain = mainImage?.parentElement;
    if (galleryMain) {
        galleryMain._updateLabel?.();
        galleryMain._updateCounter?.();
    }
    document.querySelectorAll('#gallery-thumbnails .thumbnail').forEach((el, i) => {
        el.classList.toggle('active', i === currentImageIndex);
    });
    // Sync lightbox if open
    const lb = document.getElementById('gallery-lightbox');
    if (lb && lb.style.display === 'flex') {
        const lbImg = lb.querySelector('.lightbox-img');
        const lbLabel = lb.querySelector('.lightbox-label');
        const lbCounter = lb.querySelector('.lightbox-counter');
        if (lbImg) lbImg.src = propertyImages[currentImageIndex].url;
        if (lbLabel) { lbLabel.textContent = propertyImages[currentImageIndex].label || ''; lbLabel.style.display = propertyImages[currentImageIndex].label ? 'block' : 'none'; }
        if (lbCounter) lbCounter.textContent = `${currentImageIndex + 1} / ${propertyImages.length}`;
    }
}

/* ── Lightbox ── */
function openLightbox(startIndex, propertyTitle) {
    let lb = document.getElementById('gallery-lightbox');
    if (!lb) {
        lb = document.createElement('div');
        lb.id = 'gallery-lightbox';
        lb.setAttribute('role', 'dialog');
        lb.setAttribute('aria-modal', 'true');
        lb.setAttribute('aria-label', 'Bildergalerie');
        lb.innerHTML = `
            <div class="lightbox-backdrop"></div>
            <button class="lightbox-close" aria-label="Schließen"><i class="fas fa-times"></i></button>
            <button class="lightbox-nav lightbox-prev" aria-label="Vorheriges Bild"><i class="fas fa-chevron-left"></i></button>
            <div class="lightbox-content">
                <img class="lightbox-img" src="" alt="">
                <div class="lightbox-label"></div>
                <div class="lightbox-counter"></div>
            </div>
            <button class="lightbox-nav lightbox-next" aria-label="Nächstes Bild"><i class="fas fa-chevron-right"></i></button>
        `;
        document.body.appendChild(lb);

        lb.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
        lb.querySelector('.lightbox-backdrop').addEventListener('click', closeLightbox);
        lb.querySelector('.lightbox-prev').addEventListener('click', () => showImage(currentImageIndex - 1));
        lb.querySelector('.lightbox-next').addEventListener('click', () => showImage(currentImageIndex + 1));

        let lbTouchX = 0;
        lb.addEventListener('touchstart', e => { lbTouchX = e.changedTouches[0].clientX; }, { passive: true });
        lb.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - lbTouchX;
            if (Math.abs(dx) > 50) dx < 0 ? showImage(currentImageIndex + 1) : showImage(currentImageIndex - 1);
        }, { passive: true });

        document.addEventListener('keydown', e => {
            if (lb.style.display !== 'flex') return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') showImage(currentImageIndex + 1);
            if (e.key === 'ArrowLeft')  showImage(currentImageIndex - 1);
        });
    }

    currentImageIndex = startIndex;
    const img   = lb.querySelector('.lightbox-img');
    const label = lb.querySelector('.lightbox-label');
    const ctr   = lb.querySelector('.lightbox-counter');
    img.src   = propertyImages[currentImageIndex].url;
    img.alt   = propertyTitle || '';
    label.textContent = propertyImages[currentImageIndex].label || '';
    label.style.display = propertyImages[currentImageIndex].label ? 'block' : 'none';
    ctr.textContent = `${currentImageIndex + 1} / ${propertyImages.length}`;
    lb.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lb = document.getElementById('gallery-lightbox');
    if (lb) lb.style.display = 'none';
    document.body.style.overflow = '';
}

/**
 * Update description section - Shows ALL text data
 */
function updateDescription(property) {
    const descSection = document.querySelector('.property-details .content-section');
    if (!descSection) return;

    const description = property.description || 'Keine Beschreibung verfügbar.';

    // Build comprehensive property information
    let html = `
        <h2>Objektbeschreibung</h2>
        <div class="description-text">
            ${description.split('\n').map(para => para.trim() ? `<p>${para}</p>` : '').join('')}
        </div>
        
        <h3 style="margin-top: 2rem; color: var(--secondary-color);">Objektdaten im Detail</h3>
        <div class="property-data-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
    `;

    // Create data sections
    const dataGroups = [];

    // Basic Information
    const basicInfo = [];
    if (property.type) basicInfo.push({ label: 'Objektart', value: getTypeLabel(property.type) });
    if (property.offer_type) basicInfo.push({ label: 'Angebotstyp', value: property.offer_type === 'kauf' ? 'Kaufobjekt' : 'Mietobjekt' });
    if (property.price) basicInfo.push({ label: 'Preis', value: `€ ${new Intl.NumberFormat('de-DE').format(property.price)}` });
    if (property.size) basicInfo.push({ label: 'Wohnfläche', value: `${property.size} m²` });
    if (property.rooms) basicInfo.push({ label: 'Zimmer', value: property.rooms });
    if (property.bathrooms) basicInfo.push({ label: 'Badezimmer', value: property.bathrooms });

    if (basicInfo.length > 0) {
        dataGroups.push({ title: 'Basisdaten', items: basicInfo });
    }

    // Building Information
    const buildingInfo = [];
    if (property.year_built) buildingInfo.push({ label: 'Baujahr', value: property.year_built });
    if (property.status) {
        const statusLabels = {
            'available': 'Verfügbar',
            'reserved': 'Reserviert',
            'sold': 'Verkauft',
            'rented': 'Vermietet'
        };
        buildingInfo.push({ label: 'Status', value: statusLabels[property.status] || property.status });
    }

    if (buildingInfo.length > 0) {
        dataGroups.push({ title: 'Gebäudedaten', items: buildingInfo });
    }

    // Location Information
    const locationInfo = [];
    if (property.address) locationInfo.push({ label: 'Adresse', value: property.address });
    if (property.zip_code) locationInfo.push({ label: 'PLZ', value: property.zip_code });
    if (property.city) locationInfo.push({ label: 'Stadt', value: property.city });
    if (property.state) locationInfo.push({ label: 'Bundesland', value: property.state });
    if (property.location) locationInfo.push({ label: 'Lage', value: property.location });

    if (locationInfo.length > 0) {
        dataGroups.push({ title: 'Lage', items: locationInfo });
    }

    // Additional Information
    const additionalInfo = [];
    if (property.created_at) {
        additionalInfo.push({
            label: 'Veröffentlicht am',
            value: new Date(property.created_at).toLocaleDateString('de-DE', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            })
        });
    }
    if (property.views) additionalInfo.push({ label: 'Aufrufe', value: property.views });
    if (property.featured) additionalInfo.push({ label: 'Highlight', value: '⭐ Top-Angebot' });

    if (additionalInfo.length > 0) {
        dataGroups.push({ title: 'Weitere Informationen', items: additionalInfo });
    }

    // Generate HTML for data groups
    dataGroups.forEach(group => {
        html += `
            <div class="data-group" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h4 style="color: var(--primary-color); margin-bottom: 1rem; font-size: 1.1rem;">${group.title}</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        ${group.items.map(item => `
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 0.75rem 0; font-weight: 600; color: var(--text-gray);">${item.label}:</td>
                                <td style="padding: 0.75rem 0; text-align: right; color: var(--secondary-color);">${item.value}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    });

    html += `</div>`;

    descSection.innerHTML = html;
}

/**
 * Update features section
 */
function updateFeatures(property) {
    const featuresSection = document.querySelector('.property-features-section');
    if (!featuresSection) {
        // Create features section if it doesn't exist
        const detailsSection = document.querySelector('.property-details');
        if (detailsSection) {
            const newSection = document.createElement('div');
            newSection.className = 'property-features-section';
            detailsSection.appendChild(newSection);
        }
        return updateFeatures(property); // Retry
    }

    const features = property.features || [];
    const featuresList = Array.isArray(features) ? features : [];

    if (featuresList.length === 0) {
        featuresSection.style.display = 'none';
        return;
    }

    featuresSection.style.display = 'block';
    featuresSection.innerHTML = `
        <div class="container">
            <h2>Ausstattung & Merkmale</h2>
            <div class="features-grid">
                ${featuresList.map(feature => `
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                        </svg>
                        <span>${feature}</span>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

/**
 * Update location information - Shows ALL location data
 */
function updateLocation(property) {
    const locationSection = document.querySelector('.location-info');
    if (!locationSection) return;

    let locationHTML = `
        <div class="container">
            <h2>Lage & Standort</h2>
    `;

    // Main location info
    locationHTML += `<div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 2rem;">`;

    if (property.city) {
        locationHTML += `<h3 style="color: var(--primary-color); margin-bottom: 1rem;">📍 ${property.city}</h3>`;
    }

    if (property.address) {
        locationHTML += `<p style="font-size: 1.1rem; margin-bottom: 0.5rem;"><strong>Adresse:</strong> ${property.address}</p>`;
    }

    if (property.zip_code && property.city) {
        locationHTML += `<p style="margin-bottom: 0.5rem;"><strong>PLZ/Ort:</strong> ${property.zip_code} ${property.city}</p>`;
    }

    if (property.state) {
        locationHTML += `<p style="margin-bottom: 0.5rem;"><strong>Bundesland:</strong> ${property.state}</p>`;
    }

    if (property.country) {
        locationHTML += `<p style="margin-bottom: 0.5rem;"><strong>Land:</strong> ${property.country}</p>`;
    }

    if (property.location) {
        locationHTML += `<p style="margin-top: 1rem; color: var(--text-gray); line-height: 1.6;">${property.location}</p>`;
    }

    locationHTML += `</div>`;

    // Coordinates (if available)
    if (property.latitude && property.longitude) {
        locationHTML += `
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                <h4 style="color: var(--secondary-color); margin-bottom: 1rem;">🗺️ Koordinaten</h4>
                <p><strong>Breitengrad:</strong> ${property.latitude}</p>
                <p><strong>Längengrad:</strong> ${property.longitude}</p>
                <a href="https://www.google.com/maps?q=${property.latitude},${property.longitude}" 
                   target="_blank" 
                   class="cta-button" 
                   style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: var(--primary-color); color: white; text-decoration: none; border-radius: 8px;">
                    📍 Auf Google Maps öffnen
                </a>
            </div>
        `;
    }

    locationHTML += `</div>`;

    locationSection.innerHTML = locationHTML;
}

/**
 * Add complete property information section - Shows EVERYTHING uploaded
 */
function addCompletePropertyInfo(property) {
    // Find or create a section for complete info
    let completeInfoSection = document.querySelector('.complete-property-info');

    if (!completeInfoSection) {
        // Create section after location info
        const locationSection = document.querySelector('.location-info');
        if (locationSection) {
            completeInfoSection = document.createElement('section');
            completeInfoSection.className = 'complete-property-info';
            completeInfoSection.style.cssText = 'margin: 3rem 0; padding: 3rem 0; background: #f8f9fa;';
            locationSection.parentNode.insertBefore(completeInfoSection, locationSection.nextSibling);
        } else {
            return; // Can't add section
        }
    }

    let html = `
        <div class="container">
            <h2 style="text-align: center; color: var(--secondary-color); margin-bottom: 2rem;">
                📋 Vollständige Immobiliendaten
            </h2>
            <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--primary-color); color: white;">
                            <th style="padding: 1rem; text-align: left; border-radius: 8px 0 0 0;">Eigenschaft</th>
                            <th style="padding: 1rem; text-align: right; border-radius: 0 8px 0 0;">Wert</th>
                        </tr>
                    </thead>
                    <tbody>
    `;

    // Add all property data
    const allData = [
        { label: 'Immobilien-ID', value: property.id, show: true },
        { label: 'Titel', value: property.title, show: true },
        { label: 'Objektart', value: getTypeLabel(property.type), show: property.type },
        { label: 'Angebotstyp', value: property.offer_type === 'kauf' ? 'Kaufobjekt' : 'Mietobjekt', show: property.offer_type },
        { label: 'Preis', value: `€ ${new Intl.NumberFormat('de-DE').format(property.price)}`, show: property.price },
        { label: 'Wohnfläche', value: `${property.size} m²`, show: property.size },
        { label: 'Anzahl Zimmer', value: property.rooms, show: property.rooms !== undefined },
        { label: 'Anzahl Badezimmer', value: property.bathrooms, show: property.bathrooms },
        { label: 'Baujahr', value: property.year_built, show: property.year_built },
        { label: 'Lage/Beschreibung', value: property.location, show: property.location },
        { label: 'Straße/Adresse', value: property.address, show: property.address },
        { label: 'Postleitzahl', value: property.zip_code, show: property.zip_code },
        { label: 'Stadt', value: property.city, show: property.city },
        { label: 'Bundesland', value: property.state, show: property.state },
        { label: 'Land', value: property.country, show: property.country },
        { label: 'Breitengrad', value: property.latitude, show: property.latitude },
        { label: 'Längengrad', value: property.longitude, show: property.longitude },
        { label: 'Status', value: getStatusLabel(property.status), show: property.status },
        { label: 'Top-Angebot', value: property.featured ? '⭐ Ja' : 'Nein', show: property.featured !== undefined },
        { label: 'Anzahl Bilder', value: (property.images?.length || 0) + ' Bild(er)', show: true },
        { label: 'Aufrufe', value: property.views || 0, show: property.views !== undefined },
        { label: 'Veröffentlicht am', value: formatDate(property.created_at), show: property.created_at },
        { label: 'Letzte Aktualisierung', value: formatDate(property.updated_at), show: property.updated_at }
    ];

    let rowIndex = 0;
    allData.forEach(item => {
        if (item.show && item.value) {
            const bgColor = rowIndex % 2 === 0 ? '#f8f9fa' : 'white';
            html += `
                <tr style="background: ${bgColor}; border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 1rem; font-weight: 600; color: var(--text-gray);">${item.label}</td>
                    <td style="padding: 1rem; text-align: right; color: var(--secondary-color);">${item.value}</td>
                </tr>
            `;
            rowIndex++;
        }
    });

    html += `
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 2rem; text-align: center; color: var(--text-gray); font-size: 0.9rem;">
                <p>✅ Alle hochgeladenen Daten werden hier vollständig angezeigt</p>
            </div>
        </div>
    `;

    completeInfoSection.innerHTML = html;
}

/**
 * Helper: Format date
 */
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('de-DE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Helper: Get status label
 */
function getStatusLabel(status) {
    const labels = {
        'available': '✅ Verfügbar',
        'reserved': '⏳ Reserviert',
        'sold': '✔️ Verkauft',
        'rented': '🏠 Vermietet'
    };
    return labels[status] || status;
}

/**
 * Update structured data for SEO
 */
function updateStructuredData(property) {
    // Remove existing structured data
    const existingScript = document.querySelector('script[type="application/ld+json"]');
    if (existingScript) {
        existingScript.remove();
    }

    // Create new structured data
    const structuredData = {
        "@context": "https://schema.org",
        "@type": property.type === 'haus' ? "SingleFamilyResidence" : "Apartment",
        "name": property.title,
        "description": property.description,
        "address": {
            "@type": "PostalAddress",
            "addressLocality": property.city,
            "addressRegion": "HE",
            "postalCode": property.zip_code || "",
            "addressCountry": "DE"
        },
        "numberOfRooms": property.rooms,
        "floorSize": {
            "@type": "QuantitativeValue",
            "value": property.size,
            "unitCode": "MTK"
        }
    };

    if (propertyImages.length > 0) {
        structuredData.image = propertyImages;
    }

    if (property.price) {
        structuredData.price = {
            "@type": "MonetaryAmount",
            "value": property.price,
            "currency": "EUR"
        };
    }

    // Add to page
    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.textContent = JSON.stringify(structuredData);
    document.head.appendChild(script);
}

/**
 * Gallery prev/next wrappers (showImage is defined earlier with full swipe+lightbox support)
 */
function nextImage() {
    showImage(currentImageIndex + 1);
}

function previousImage() {
    showImage(currentImageIndex - 1);
}

/**
 * Helper: Get type label
 */
function getTypeLabel(type) {
    const types = {
        'haus': 'Haus',
        'wohnung': 'Wohnung',
        'gewerbe': 'Gewerbe',
        'grundstück': 'Grundstück'
    };
    return types[type] || 'Immobilie';
}

/**
 * Show loading state
 */
let heroOriginalHtml = null;

function showLoading() {
    const heroSection = document.querySelector('.property-hero .container');
    if (heroSection) {
        heroOriginalHtml = heroSection.innerHTML;
        heroSection.innerHTML = '<div class="loading-message">Immobilie wird geladen...</div>';
    }
}

/**
 * Hide loading state – restores the hero markup that showLoading() replaced
 */
function hideLoading() {
    const heroSection = document.querySelector('.property-hero .container');
    if (heroSection && heroOriginalHtml !== null) {
        heroSection.innerHTML = heroOriginalHtml;
        heroOriginalHtml = null;
    }
}

/**
 * Show error message
 */
function showError(message) {
    const heroSection = document.querySelector('.property-hero .container');
    if (heroSection) {
        heroSection.innerHTML = `
            <div class="error-message">
                <h2>Fehler</h2>
                <p>${message}</p>
                <a href="${window.SITE_BASE||''}/immobilien-angebote.php" class="cta-button">Zurück zu den Angeboten</a>
            </div>
        `;
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', loadPropertyDetails);

// Make gallery functions globally available
window.showImage     = showImage;
window.openLightbox  = openLightbox;
window.closeLightbox = closeLightbox;
window.nextImage     = nextImage;
window.previousImage = previousImage;

/* ── Leaflet Map ─────────────────────────────────────────── */
function initPropertyMap(property) {
    const section = document.getElementById('property-map-section');
    const mapEl   = document.getElementById('property-map');
    if (!section || !mapEl || typeof L === 'undefined') return;

    const address = [property.address, property.zip, property.city].filter(Boolean).join(', ');
    if (!address) return;

    // Geocode via Nominatim (open, no key required)
    const query = encodeURIComponent(address);
    fetch(`https://nominatim.openstreetmap.org/search?q=${query}&format=json&limit=1`, {
        headers: { 'Accept-Language': 'de' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data || data.length === 0) return;
        const lat = parseFloat(data[0].lat);
        const lon = parseFloat(data[0].lon);

        section.style.display = 'block';
        const map = L.map(mapEl, { scrollWheelZoom: false }).setView([lat, lon], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        // Custom icon
        const icon = L.divIcon({
            className: '',
            html: '<div style="background:#1a56a4;color:#fff;border-radius:50% 50% 50% 0;width:36px;height:36px;display:flex;align-items:center;justify-content:center;transform:rotate(-45deg);box-shadow:0 2px 8px rgba(0,0,0,.3)"><i class="fas fa-home" style="transform:rotate(45deg);font-size:14px"></i></div>',
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            popupAnchor: [0, -36]
        });

        const popup = `<strong>${property.title}</strong><br>${property.zip} ${property.city}`;
        L.marker([lat, lon], { icon }).addTo(map).bindPopup(popup).openPopup();
    })
    .catch(() => { /* Geocoding failed silently – map stays hidden */ });
}

/* ── Favorites Button ────────────────────────────────────── */
function initFavButton(property) {
    const btn = document.getElementById('detail-fav-btn');
    if (!btn || !property.id) return;
    btn.dataset.favId = String(property.id);
    // favorites.js handles the rest via [data-fav-id] delegation
    // But it may have already run before the ID was set, so manually init:
    if (typeof Favorites !== 'undefined') {
        const active = Favorites.has(property.id);
        btn.classList.toggle('fav-active', active);
        const icon = btn.querySelector('i');
        if (icon) {
            icon.className = active ? 'fas fa-heart' : 'far fa-heart';
        }
        btn.innerHTML = `<i class="${active ? 'fas' : 'far'} fa-heart"></i> ${active ? 'Gemerkt' : 'Merken'}`;

        btn.addEventListener('click', function () {
            const nowActive = Favorites.toggle(property.id);
            this.innerHTML = `<i class="${nowActive ? 'fas' : 'far'} fa-heart"></i> ${nowActive ? 'Gemerkt' : 'Merken'}`;
            this.classList.toggle('fav-active', nowActive);
        });
    }
}
