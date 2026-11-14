const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const root = path.join(__dirname, '..');
const source = fs.readFileSync(path.join(root, 'js', 'cookie-consent.js'), 'utf8');

function setup(initialConsent, failStorage = false) {
    const stored = new Map(initialConsent ? [['cookie_consent', JSON.stringify(initialConsent)]] : []);
    const cookies = new Map();
    const elements = new Map();
    const scripts = [];
    const events = {};
    let reloads = 0;
    let alerts = 0;

    function element(id) {
        return {
            id, style: {}, checked: false, listeners: {},
            addEventListener(name, callback) { this.listeners[name] = callback; },
            click() { this.listeners.click(); },
            focus() {},
            contains() { return false; },
            remove() {
                elements.delete(id);
                for (const key of ['cookie-analytics', 'cookie-marketing', 'cookie-accept-all', 'cookie-reject-optional', 'cookie-save-selection']) elements.delete(key);
            }
        };
    }

    const document = {
        readyState: 'complete',
        activeElement: null,
        createElement() { return element(''); },
        getElementById(id) { return elements.get(id) || null; },
        head: {
            appendChild(node) {
                if (node.src) scripts.push(node.src);
                if (node.id) elements.set(node.id, node);
            }
        },
        body: {
            appendChild(node) {
                elements.set(node.id, node);
                for (const id of ['cookie-analytics', 'cookie-marketing', 'cookie-accept-all', 'cookie-reject-optional', 'cookie-save-selection']) elements.set(id, element(id));
            }
        }
    };
    Object.defineProperty(document, 'cookie', {
        get() { return Array.from(cookies, ([name, value]) => name + '=' + value).join('; '); },
        set(value) {
            const [pair] = value.split(';');
            const [name, content] = pair.split('=');
            if (value.includes('Max-Age=0')) cookies.delete(name);
            else cookies.set(name, content);
        }
    });
    const storage = {
        getItem(key) { return stored.get(key) || null; },
        setItem(key, value) { if (failStorage) throw new Error('storage blocked'); stored.set(key, value); },
        removeItem(key) { stored.delete(key); }
    };
    const location = { hostname: 'www.example.test', reload() { reloads++; } };
    const window = {
        addEventListener(name, callback) { events[name] = callback; }
    };
    const context = {
        window, document, localStorage: storage, location,
        console: { error() {} },
        alert() { alerts++; },
        setTimeout() { return 1; },
        clearTimeout() {}
    };
    vm.runInNewContext(source, context);
    return { window, document, storage, scripts, events, elements, cookies, get reloads() { return reloads; }, get alerts() { return alerts; } };
}

test('analytics is not loaded without consent or after rejection', () => {
    const page = setup();
    assert.equal(page.scripts.length, 0);
    assert.ok(page.elements.has('cookie-reject-optional'));
    page.elements.get('cookie-reject-optional').click();
    assert.equal(JSON.parse(page.storage.getItem('cookie_consent')).analytics, false);
    assert.equal(page.scripts.length, 0);
});

test('acceptance loads analytics once and withdrawal disables it and clears cookies', () => {
    const page = setup();
    page.elements.get('cookie-accept-all').click();
    assert.equal(page.scripts.length, 1);
    assert.match(page.scripts[0], /^https:\/\/www\.googletagmanager\.com\/gtag\/js\?/);
    page.window.CookieConsent.show();
    assert.equal(page.elements.get('cookie-analytics').checked, true);
    page.cookies.set('_ga', 'test');
    page.cookies.set('_ga_123456', 'test');
    page.elements.get('cookie-reject-optional').click();
    assert.equal(JSON.parse(page.storage.getItem('cookie_consent')).analytics, false);
    assert.equal(page.window['ga-disable-G-Q5J1C2PTR2'], true);
    assert.equal(page.cookies.size, 0);
    assert.equal(page.reloads, 1);
});

test('only an unexpired, explicit stored analytics choice enables tracking', () => {
    const valid = { version: '1.0', timestamp: new Date().toISOString(), analytics: true };
    assert.equal(setup(valid).scripts.length, 1);
    assert.equal(setup({ ...valid, analytics: 'true' }).scripts.length, 0);
    const expired = setup({ ...valid, timestamp: '2020-01-01T00:00:00Z' });
    assert.equal(expired.scripts.length, 0);
    assert.ok(expired.elements.has('cookie-reject-optional'));
});

test('storage failure blocks analytics and withdrawal in another tab stops tracking', () => {
    const blocked = setup(null, true);
    blocked.elements.get('cookie-accept-all').click();
    assert.equal(blocked.scripts.length, 0);
    assert.equal(blocked.alerts, 1);

    const page = setup({ version: '1.0', timestamp: new Date().toISOString(), analytics: true });
    page.storage.setItem('cookie_consent', JSON.stringify({ version: '1.0', timestamp: new Date().toISOString(), analytics: false }));
    page.events.storage({ key: 'cookie_consent' });
    assert.equal(page.window['ga-disable-G-Q5J1C2PTR2'], true);
    assert.equal(page.reloads, 1);
});

test('clearing consent in another tab displays the selection again', () => {
    const page = setup({ version: '1.0', timestamp: new Date().toISOString(), analytics: false });
    assert.equal(page.elements.has('cookie-consent-banner'), false);
    page.storage.removeItem('cookie_consent');
    page.events.storage({ key: 'cookie_consent' });
    assert.equal(page.scripts.length, 0);
    assert.ok(page.elements.has('cookie-reject-optional'));
});

test('Montserrat is served locally with valid font assets', () => {
    const header = fs.readFileSync(path.join(root, 'includes', 'header.php'), 'utf8');
    const css = fs.readFileSync(path.join(root, 'Montserrat.css'), 'utf8');
    assert.match(header, /Montserrat\.css/);
    assert.doesNotMatch(header + css, /fonts\.(?:googleapis|gstatic)\.com/);
    const urls = Array.from(css.matchAll(/url\(['"]([^'"]+)['"]\)/g), match => match[1]);
    assert.ok(urls.length > 0);
    for (const url of urls) {
        assert.match(url, /^assets\/fonts\/[^/]+\.woff2$/);
        assert.equal(fs.readFileSync(path.join(root, url)).subarray(0, 4).toString(), 'wOF2');
    }
});
