const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '..', 'js', 'contact-turnstile.js'), 'utf8');

function setup({ hasProtectedForm = false, hasWidget = false } = {}) {
    const scripts = [];
    const form = {
        querySelector(selector) {
            if (selector === '.cf-turnstile') return hasWidget ? {} : null;
            if (selector === 'input[name="csrf_token"], input[name="form_type"]') {
                return hasProtectedForm ? {} : null;
            }
            return null;
        }
    };
    const document = {
        readyState: 'complete',
        querySelectorAll(selector) { return selector === 'form' ? [form] : []; },
        createElement() {
            return { listeners: {}, addEventListener(name, callback) { this.listeners[name] = callback; } };
        },
        head: { appendChild(script) { scripts.push(script); } }
    };
    const window = { TURNSTILE_SITE_KEY: 'test-site-key' };

    vm.runInNewContext(source, {
        window,
        document,
        console: { error() {} },
        setInterval() { return 1; },
        clearInterval() {}
    });

    return { scripts };
}

test('does not contact Cloudflare on pages without a protected form', () => {
    assert.equal(setup().scripts.length, 0);
    assert.equal(setup({ hasProtectedForm: false, hasWidget: false }).scripts.length, 0);
});

test('loads Turnstile only when a form needs it', () => {
    const protectedForm = setup({ hasProtectedForm: true });
    assert.equal(protectedForm.scripts.length, 1);
    assert.equal(protectedForm.scripts[0].src, 'https://challenges.cloudflare.com/turnstile/v0/api.js');

    const existingWidget = setup({ hasWidget: true });
    assert.equal(existingWidget.scripts.length, 1);
});
