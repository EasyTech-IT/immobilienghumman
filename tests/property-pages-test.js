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
assert(purchaseCard.includes('href="/immobilie/1122"'));
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
assert(rentalCard.includes('href="/immobilie/1123"'));
assert(!rentalCard.includes('Wohnfläche'));
assert(!rentalCard.includes('—'));

const listeners = {};
let requestedDetailUrl = '';
const detailContent = { innerHTML: '' };
const detailRoot = { setAttribute() {} };
const detailLoading = { hidden: true };
const detailError = { hidden: true };
const detailWindow = {
    SITE_BASE: '',
    API_BASE_URL: '/api',
    location: { search: '', pathname: '/immobilie/1123', origin: 'https://example.test' }
};
const detailDocument = {
    readyState: 'loading',
    addEventListener(name, callback) {
        listeners[name] = callback;
    },
    getElementById() {
        return null;
    },
    querySelector(selector) {
        return {
            '#pd-content': detailContent,
            '#pd-root': detailRoot,
            '#pd-loading': detailLoading,
            '#pd-error': detailError
        }[selector] || null;
    },
    querySelectorAll() {
        return [];
    },
    createElement() {
        return {};
    },
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
                    public_number: 1123,
                    slug: 'langes-altes-objekt',
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
                    images: [],
                    features: ['Neubau', 'Erstbezug', 'Balkon', 'Einbauküche'],
                    extra_data: { building_year: 1980 }
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
    assert.strictEqual(requestedDetailUrl, '/api/properties.php?number=1123');
    assert(detailContent.innerHTML.includes('pd-description-more'));
    assert(detailContent.innerHTML.includes('<summary>Mehr anzeigen</summary>'));
    assert(!detailContent.innerHTML.includes('id="pd-energy"'));
    assert(detailContent.innerHTML.includes('pd-detail-fact'));
    assert(detailContent.innerHTML.includes('fa-kitchen-set'));
    assert(detailContent.innerHTML.includes('Objekt 1123'));
    detailWindow.location.pathname = '/immobilie/legacy-slug';
    return detailWindow.loadPropertyDetails();
}).then(() => {
    assert.strictEqual(requestedDetailUrl, '/api/properties.php?slug=legacy-slug');
    console.log('Property page tests passed.');
}).catch(error => {
    console.error(error);
    process.exitCode = 1;
});
