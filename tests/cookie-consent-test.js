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
                elements.delete('cookie-reject-optional');
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
                elements.set('cookie-reject-optional', element('cookie-reject-optional'));
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

test('analytics stays disabled and the notice can only be dismissed', () => {
    const page = setup();
    assert.equal(page.scripts.length, 0);
    assert.ok(page.elements.has('cookie-reject-optional'));
    page.elements.get('cookie-reject-optional').click();
    assert.equal(JSON.parse(page.storage.getItem('cookie_consent')).analytics, false);
    assert.equal(page.scripts.length, 0);
});

test('analytics stays disabled for previous and manually restored opt-ins and clears cookies', () => {
    const oldConsent = setup({ version: '1.1', timestamp: new Date().toISOString(), analytics: true });
    assert.equal(oldConsent.scripts.length, 0);
    assert.ok(oldConsent.elements.has('cookie-consent-banner'));

    const page = setup({ version: '1.2', timestamp: new Date().toISOString(), analytics: true });
    assert.equal(page.scripts.length, 0);
    assert.ok(page.elements.has('cookie-consent-banner'));
    page.cookies.set('_ga', 'test');
    page.cookies.set('_ga_123456', 'test');
    page.events.storage({ key: 'cookie_consent' });
    assert.equal(page.cookies.size, 0);
    assert.equal(page.window['ga-disable-G-Q5J1C2PTR2'], true);
});

test('old consent versions are not treated as an active choice', () => {
    const previousVersion = setup({ version: '1.1', timestamp: new Date().toISOString(), analytics: false });
    assert.equal(previousVersion.scripts.length, 0);
    assert.ok(previousVersion.elements.has('cookie-reject-optional'));
});

test('storage failure blocks analytics and withdrawal in another tab stops tracking', () => {
    const blocked = setup(null, true);
    blocked.elements.get('cookie-reject-optional').click();
    assert.equal(blocked.scripts.length, 0);
    assert.equal(blocked.alerts, 1);

    const page = setup({ version: '1.2', timestamp: new Date().toISOString(), analytics: false });
    page.storage.setItem('cookie_consent', JSON.stringify({ version: '1.2', timestamp: new Date().toISOString(), analytics: true }));
    page.events.storage({ key: 'cookie_consent' });
    assert.equal(page.window['ga-disable-G-Q5J1C2PTR2'], true);
    assert.equal(page.scripts.length, 0);
});

test('clearing consent in another tab displays the selection again', () => {
    const page = setup({ version: '1.2', timestamp: new Date().toISOString(), analytics: false });
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
