const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');

const source = fs.readFileSync(path.join(__dirname, '..', 'admin', 'properties.php'), 'utf8');
const functionStart = source.indexOf('function updatePublishRequirements() {');
assert.notStrictEqual(functionStart, -1, 'Publish requirement marker logic is present');

let depth = 0;
let functionEnd = -1;
for (let i = functionStart; i < source.length; i++) {
    if (source[i] === '{') depth++;
    if (source[i] === '}') {
        depth--;
        if (depth === 0) {
            functionEnd = i + 1;
            break;
        }
    }
}
assert.notStrictEqual(functionEnd, -1, 'Publish requirement marker function is complete');
const functionSource = source.slice(functionStart, functionEnd);

function renderRequirements(values) {
    const fields = Object.fromEntries(Object.entries(values).map(([name, value]) => [
        name,
        typeof value === 'boolean' ? { checked: value } : { value }
    ]));
    const markerNames = [
        'area', 'rooms', 'bathrooms', 'plot_area', 'residential_units',
        'extra_costs', 'deposit', 'heating_costs', 'house_fee',
        'commission_amount', 'commission_payer', 'commission_note',
        'energy_certificate_type', 'energy_value_kind', 'energy_value',
        'energy_source', 'energy_value_electricity', 'energy_efficiency_class',
        'energy_building_year', 'energy_issue_date', 'energy_valid_until'
    ];
    const markers = Object.fromEntries(markerNames.map(name => [name, { hidden: true }]));
    const residentialOnly = [{ hidden: false }, { hidden: false }];
    const form = {
        querySelector(selector) {
            const field = selector.match(/^\[name="([^"]+)"\]$/);
            if (field) return fields[field[1]] || null;
            const marker = selector.match(/^\[data-publish-required="([^"]+)"\]$/);
            return marker ? markers[marker[1]] || null : null;
        },
        querySelectorAll(selector) {
            return selector === '[data-residential-energy-only]' ? residentialOnly : [];
        }
    };
    vm.runInNewContext(functionSource + '\nupdatePublishRequirements();', { form });
    return { markers, residentialOnly };
}

let result = renderRequirements({
    type: 'Wohnung',
    offer_type: 'Miete',
    energy_certificate_status: 'not_required',
    heating_costs_included: false,
    commission_required: false
});
assert.strictEqual(result.markers.area.hidden, false);
assert.strictEqual(result.markers.rooms.hidden, false);
assert.strictEqual(result.markers.bathrooms.hidden, false);
assert.strictEqual(result.markers.plot_area.hidden, true);
assert.strictEqual(result.markers.extra_costs.hidden, false);
assert.strictEqual(result.markers.deposit.hidden, false);
assert.strictEqual(result.markers.heating_costs.hidden, false);
assert.strictEqual(result.markers.house_fee.hidden, true);

result = renderRequirements({
    type: 'Gewerbe',
    offer_type: 'Kauf',
    energy_certificate_status: 'available',
    commission_required: true,
    energy_efficiency_class_not_in_certificate: false,
    energy_efficiency_class: ''
});
assert.strictEqual(result.markers.rooms.hidden, true);
assert.strictEqual(result.markers.energy_value_electricity.hidden, false);
assert.strictEqual(result.markers.energy_efficiency_class.hidden, true);
assert.strictEqual(result.markers.energy_building_year.hidden, true);
assert.strictEqual(result.markers.commission_amount.hidden, false);
assert(result.residentialOnly.every(element => element.hidden));

result = renderRequirements({
    type: 'Wohnung',
    offer_type: 'Kauf',
    energy_certificate_status: 'available',
    energy_efficiency_class_not_in_certificate: true,
    energy_efficiency_class: ''
});
assert.strictEqual(result.markers.energy_efficiency_class.hidden, true);
assert.strictEqual(result.markers.energy_issue_date.hidden, false);
assert(result.residentialOnly.every(element => !element.hidden));

console.log('Admin property form requirement marker tests passed.');
