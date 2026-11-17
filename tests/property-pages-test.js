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
assert(!rentalCard.includes('Wohnfläche'));
assert(!rentalCard.includes('—'));

const listeners = {};
const detailWindow = {
    SITE_BASE: '',
    API_BASE_URL: '/api',
    location: { search: '', pathname: '/immobilie/test', origin: 'https://example.test' }
};
const detailDocument = {
    readyState: 'loading',
    addEventListener(name, callback) {
        listeners[name] = callback;
    },
    getElementById() {
        return null;
    },
    querySelector() {
        return null;
    },
    querySelectorAll() {
        return [];
    }
};
const detailContext = vm.createContext({ window: detailWindow, document: detailDocument, console });
const detailSource = fs.readFileSync(path.join(root, 'property-detail-loader.js'), 'utf8');
vm.runInContext(detailSource, detailContext);

assert.strictEqual(typeof detailWindow.loadPropertyDetails, 'function');
assert.strictEqual(typeof listeners.DOMContentLoaded, 'function');

console.log('Property page tests passed.');
