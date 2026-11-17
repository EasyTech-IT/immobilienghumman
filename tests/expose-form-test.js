const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');

const source = fs.readFileSync(path.join(__dirname, '..', 'property-detail.php'), 'utf8');
const script = source.match(/<script>\s*([\s\S]*?)<\/script>/)[1];
const fields = {
    inquiry_property_id: { value: '12' },
    inquiry_property_title: { value: 'Testwohnung' },
    inquiry_property_link: { value: 'https://example.test/immobilie/testwohnung' }
};
const normalButton = { name: '', innerHTML: 'Anfrage senden' };
const exposeButton = { name: 'request_expose', innerHTML: 'Exposé anfordern' };
const buttons = [normalButton, exposeButton];
const message = { style: {}, textContent: '' };
let handler;
let request;
let widget = null;
let response = { success: true, message: 'PDF übergeben' };
const form = {
    action: '/api/contact.php?action=inquiry',
    querySelector(selector) {
        if (selector === 'button[type="submit"]') return normalButton;
        return selector === '.cf-turnstile' ? widget : null;
    },
    querySelectorAll() { return buttons; },
    addEventListener(name, callback) { if (name === 'submit') handler = callback; },
    reset() { Object.values(fields).forEach(field => { field.value = ''; }); }
};
class FormDataStub {
    constructor() { this.values = { form_type: 'immobilien_anfrage_detailseite' }; }
    set(key, value) { this.values[key] = value; }
}
const context = vm.createContext({
    document: {
        getElementById(id) {
            if (id === 'propertyInquiryForm') return form;
            if (id === 'propertyInquiryMsg') return message;
            return fields[id] || null;
        }
    },
    window: {},
    FormData: FormDataStub,
    fetch: async (url, options) => {
        request = { url, options };
        return { ok: response.success, status: response.success ? 200 : 503, text: async () => JSON.stringify(response) };
    }
});
vm.runInContext(script, context);

async function submit(button) {
    handler({ preventDefault() {}, submitter: button });
    await new Promise(resolve => setImmediate(resolve));
}
(async () => {
    await submit(exposeButton);
    assert.strictEqual(request.options.body.values.form_type, 'expose_anfrage');
    assert.strictEqual(message.textContent, 'PDF übergeben');
    assert.strictEqual(fields.inquiry_property_id.value, '12');
    assert.strictEqual(fields.inquiry_property_link.value, 'https://example.test/immobilie/testwohnung');
    assert(buttons.every(button => !button.disabled));
    response = { success: false, stored: true, message: 'Versand nicht bestätigt; nicht erneut senden.' };
    await submit(exposeButton);
    assert.strictEqual(message.textContent, response.message);
    assert.strictEqual(fields.inquiry_property_id.value, '12');
    await submit(normalButton);
    assert.strictEqual(request.options.body.values.form_type, 'immobilien_anfrage_detailseite');
    widget = {};
    await submit(exposeButton);
    assert(buttons.every(button => button.disabled));
    assert(message.textContent.includes('Seite neu'));
    console.log('Exposé form submit and error handling tests passed.');
})().catch(error => { console.error(error); process.exitCode = 1; });
