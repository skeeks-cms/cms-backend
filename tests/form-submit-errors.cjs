const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

// Execute the production default error callback with a minimal jQuery DOM adapter.
const source = fs.readFileSync(path.join(__dirname, '../src/widgets/ActiveFormAjaxBackend.php'), 'utf8');
const match = source.match(/ActiveFormAjaxSubmit\.on\('error', function\(e, response\) \{([\s\S]*?)\n                    \}\);/);
assert.ok(match, 'Default form error callback must exist');
let rendered;
const $ = (selector, options) => {
    if (selector === '<span>') {
        return {options, text(value) { this.textContent = value; return this; }};
    }
    return {empty() { rendered = undefined; return this; }, append(value) { rendered = value; return this; }};
};
$.each = (values, callback) => Object.entries(values).forEach(([key, value]) => callback(key, value));
const callback = vm.runInNewContext(`(function(e, response) {${match[1]} })`, {
    $, ActiveFormAjaxSubmit: {jForm: {}},
});
function render(response) {
    callback(null, response);
    assert.equal(rendered.options.role, 'alert');
    assert.equal(rendered.options.class, 'sx-form-submit-error');
    return rendered.textContent;
}

assert.equal(render({data: {validation: {phone: ['Этот телефон уже добавлен.']}}}),
    'Не удалось сохранить. Этот телефон уже добавлен.');
assert.equal(render({data: {validation: {name: ['Введите имя.'], email: ['Введите email.', 'Введите имя.']}}}),
    'Не удалось сохранить. Введите имя. Введите email.');
assert.equal(render({message: 'Недостаточно прав.'}), 'Не удалось сохранить. Недостаточно прав.');
assert.equal(render({message: 'Общая ошибка', data: {validation: {hidden: ['Укажите компанию.']}}}),
    'Не удалось сохранить. Укажите компанию.');
assert.equal(render({data: {validation: {value: 'Некорректное значение.', empty: ['', null]}}}),
    'Не удалось сохранить. Некорректное значение.');
const fallback = 'Не удалось сохранить данные. Попробуйте ещё раз. Если ошибка повторится, обратитесь в поддержку.';
for (const response of [undefined, {}, {message: ''}, {message: {}}, {data: {validation: {value: []}}}]) {
    assert.equal(render(response), fallback);
}
assert.equal(render({message: '<img src=x onerror=alert(1)>'}),
    'Не удалось сохранить. <img src=x onerror=alert(1)>');
// The DOM adapter exposes text() only: server messages cannot become HTML.
console.log('PASS default form errors: validation, deduplication, hidden fields, server errors, fallback, text rendering');
