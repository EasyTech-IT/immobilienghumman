const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');

const root = path.resolve(__dirname, '..');

function loadOffersLoader() {
    const document = {
        addEventListener() {},
        createElement() {
            return { dataset: {}, style: {} };
        }
    };
    const context = vm.createContext({ document, window: { SITE_BASE: '' } });
    const source = fs.readFileSync(path.join(root, 'js', 'immobilien-angebote-loader.js'), 'utf8');
    vm.runInContext(source, context);
    return context;
}

function renderCard(context, property) {
    return context.createAngebotePropertyCard(context.convertPropertyData(property));
}

const offers = loadOffersLoader();
const purchaseCard = renderCard(offers, {
    id: 12,
    slug: 'haus-in-frankfurt',
    title: 'Modernes Haus',
    description: 'Eine Beschreibung, die nicht auf der Karte erscheinen soll.',
    offer_type: 'kauf',
    price: 500000,
    area: 120,
    rooms: 5,
    location: 'Frankfurt',
    zip: '60311'
}).innerHTML;

assert(purchaseCard.includes('Kaufpreis'));
assert(purchaseCard.includes('Wohnfläche'));
assert(purchaseCard.includes('Zimmer'));
assert(purchaseCard.includes('Kaufpreis pro m²'));
assert(purchaseCard.includes('4.167 € / m²'));
assert(purchaseCard.includes('href="/immobilie/haus-in-frankfurt"'));
assert(!purchaseCard.includes('Objekt 0012'));
assert(!purchaseCard.includes('Modernes Haus'));
assert(!purchaseCard.includes('Beschreibung'));
assert(!purchaseCard.includes('Exposé ansehen'));

const rentalCard = renderCard(offers, {
    id: 13,
    slug: 'wohnung-in-frankfurt',
    title: 'Wohnung',
    offer_type: 'miete',
    price: 1000,
    warm_rent: 1250,
    rooms: 3,
    area: 0,
    location: 'Frankfurt',
    zip: '60311'
}).innerHTML;

assert(rentalCard.includes('Kaltmiete'));
assert(rentalCard.includes('Warmmiete'));
assert(rentalCard.includes('Zimmer'));
assert(rentalCard.includes('href="/immobilie/wohnung-in-frankfurt"'));
assert(!rentalCard.includes('Wohnfläche'));
assert(!rentalCard.includes('—'));

const listeners = {};
let requestedDetailUrl = '';
let detailEnergyValue = null;
const detailContent = { innerHTML: '' };
const detailRoot = { setAttribute() {} };
const detailLoading = { hidden: true };
const detailError = { hidden: true };
const mediaEvents = {};
const mediaSection = { addEventListener(name, callback) { mediaEvents[name] = callback; } };
const printButton = { addEventListener(name, callback) { this[name] = callback; } };
const lightboxParts = {};
for (const name of ['img', 'label', 'counter', 'close', 'backdrop', 'prev', 'next']) {
    lightboxParts['.lightbox-' + name] = {
        style: {},
        addEventListener(event, callback) { this[event] = callback; },
        focus() {}
    };
}
let lightbox = null;
let printed = false;
const detailWindow = {
    SITE_BASE: '',
    API_BASE_URL: '/api',
    addEventListener() {},
    print() { printed = true; },
    location: { search: '', pathname: '/immobilie/buedingen-wohnung-3-zimmer', origin: 'https://example.test' },
    history: {
        replaceState(_state, _title, url) {
            detailWindow.location.pathname = url;
        }
    }
};
const detailDocument = {
    readyState: 'loading',
    addEventListener(name, callback) {
        listeners[name] = callback;
    },
    getElementById(id) {
        return id === 'gallery-lightbox' ? lightbox : null;
    },
    querySelector(selector) {
        return {
            '#pd-content': detailContent,
            '#pd-root': detailRoot,
            '#pd-loading': detailLoading,
            '#pd-error': detailError,
            '#pd-media': mediaSection,
            '#detail-print-btn': printButton
        }[selector] || null;
    },
    querySelectorAll() {
        return [];
    },
    createElement() {
        return {
            style: {},
            setAttribute() {},
            addEventListener() {},
            querySelector(selector) { return lightboxParts[selector]; }
        };
    },
    body: { style: {}, appendChild(element) { lightbox = element; } },
    head: { appendChild() {} }
};
const detailContext = vm.createContext({
    window: detailWindow,
    document: detailDocument,
    console: { error() {} },
    URLSearchParams,
    AbortController,
    setTimeout,
    clearTimeout,
    fetch: async url => {
        requestedDetailUrl = url;
        return {
            status: 200,
            ok: true,
            text: async () => JSON.stringify({
                property: {
                    id: 13,
                    slug: 'buedingen-wohnung-3-zimmer',
                    title: 'Helle Wohnung',
                    description: 'Eine lange Objektbeschreibung mit vielen Details. '.repeat(20),
                    short_description: 'Modernisiert und bezugsfrei',
                    offer_type: 'Kauf',
                    price: 250000,
                    area: 100,
                    rooms: 3,
                    bathrooms: 1,
                    location: 'Büdingen',
                    zip: '63654',
                    address: 'Geheime Straße 1',
                    images: [{ url: '/uploads/staged.jpg', virtual_staging: true }, { url: '/uploads/room.jpg' }],
                    features: ['Neubau', 'Erstbezug', 'Balkon', 'Einbauküche'],
                    extra_data: {
                        building_year: 1980, plot_area: 800, residential_units: 4, energy_value_kind: 'bedarf', energy_value: detailEnergyValue, hide_exact_address: false, address_public_note: 'Geheime Adresse',
                        location_description: 'Ruhige Lage nahe dem Bahnhof. '.repeat(30),
                        floorplans: [{ url: '/uploads/plan.png', name: 'Grundriss', label: 'Dachgeschoss W4' }, { url: '/uploads/plan.pdf', name: 'PDF' }],
                        documents: [{ url: '/uploads/dokument.jpg?version=1', name: 'Dokument' }]
                    }
                }
            })
        };
    }
});
const detailSource = fs.readFileSync(path.join(root, 'property-detail-loader.js'), 'utf8');
vm.runInContext(detailSource, detailContext);

assert.strictEqual(typeof detailWindow.loadPropertyDetails, 'function');
assert.strictEqual(typeof listeners.DOMContentLoaded, 'function');

detailWindow.loadPropertyDetails().then(() => {
    assert.strictEqual(requestedDetailUrl, '/api/properties.php?slug=buedingen-wohnung-3-zimmer');
    assert(detailContent.innerHTML.includes('pd-description-more'));
    assert(detailContent.innerHTML.includes('<summary>Mehr anzeigen</summary>'));
    assert.strictEqual((detailContent.innerHTML.match(/<summary>Mehr anzeigen<\/summary>/g) || []).length, 2);
    assert(!detailContent.innerHTML.includes('id="pd-energy"'));
    assert(detailContent.innerHTML.includes('class="pd-detail-facts"'));
    assert(detailContent.innerHTML.includes('pd-detail-fact'));
    assert(detailContent.innerHTML.includes('Grundstücksfläche'));
    assert(detailContent.innerHTML.includes('Wohneinheiten'));
    assert(detailContent.innerHTML.includes('Visualisierung / Einrichtungsbeispiel'));
    assert(detailContent.innerHTML.includes('Dachgeschoss W4'));
    assert(detailContent.innerHTML.includes('fa-kitchen-set'));
    assert(!detailContent.innerHTML.includes('Objekt 1123'));
    assert(!detailContent.innerHTML.includes('Geheime Straße'));
    assert(!detailContent.innerHTML.includes('Geheime Adresse'));
    assert(!detailContent.innerHTML.includes('id="pd-energy"'));
    assert(detailContent.innerHTML.includes('href="/uploads/plan.png" data-media-index="0"'));
    assert(detailContent.innerHTML.includes('href="/uploads/dokument.jpg?version=1" data-media-index="1"'));
    assert(detailContent.innerHTML.includes('href="/uploads/plan.pdf"'));
    assert(detailContent.innerHTML.includes('id="detail-print-btn"'));
    assert(!detailContent.innerHTML.includes('Exposé ansehen'));
    assert(!detailContent.innerHTML.includes('expose.php'));
    printButton.click();
    assert(printed);
    let prevented = false;
    mediaEvents.click({
        target: { closest() { return { dataset: { mediaIndex: '0' } }; } },
        preventDefault() { prevented = true; }
    });
    assert(prevented);
    assert.strictEqual(lightboxParts['.lightbox-img'].src, '/uploads/plan.png');
    lightboxParts['.lightbox-next'].click();
    assert.strictEqual(lightboxParts['.lightbox-img'].src, '/uploads/dokument.jpg?version=1');
    listeners.keydown({ key: 'ArrowLeft', target: {} });
    assert.strictEqual(lightboxParts['.lightbox-img'].src, '/uploads/plan.png');
    listeners.keydown({ key: 'Escape', target: {} });
    assert.strictEqual(lightbox.style.display, 'none');
    detailWindow.location.pathname = '/immobilie/legacy-slug';
    detailEnergyValue = 0;
    return detailWindow.loadPropertyDetails();
}).then(() => {
    assert.strictEqual(requestedDetailUrl, '/api/properties.php?slug=legacy-slug');
    assert.strictEqual(detailWindow.location.pathname, '/immobilie/buedingen-wohnung-3-zimmer');
    assert(detailContent.innerHTML.includes('0 kWh/(m²·a)'));
    console.log('Property page tests passed.');
}).catch(error => {
    console.error(error);
    process.exitCode = 1;
});
