const assert = require("node:assert");
const fs = require("node:fs");
const path = require("node:path");
const vm = require("node:vm");

const rootElement = {};
const secondRootElement = {};
const itemElement = {};
const calls = [];
const providerInstances = [];
let providerOptions = null;

const rootCollection = {
    jquery: true,
    length: 1,
    get: () => rootElement,
    each: function (callback) {
        callback.call(rootElement, 0, rootElement);
        return this;
    }
};
const itemCollection = {jquery: true, length: 1, get: () => itemElement};
const secondRootCollection = {jquery: true, length: 1, get: () => secondRootElement};
const connectedCollection = {
    jquery: true,
    length: 2,
    get: (index) => index === 0 ? rootElement : secondRootElement,
    each: function (callback) {
        callback.call(rootElement, 0, rootElement);
        callback.call(secondRootElement, 1, secondRootElement);
        return this;
    }
};
const emptyCollection = {
    jquery: true,
    length: 0,
    get: () => null,
    each: function () {
        return this;
    }
};

function jquery(value) {
    if (value === rootElement || value === rootCollection) {
        return rootCollection;
    }
    if (value === itemElement || value === itemCollection) {
        return itemCollection;
    }
    if (value === secondRootElement || value === secondRootCollection) {
        return secondRootCollection;
    }
    if (value === connectedCollection) {
        return connectedCollection;
    }
    return emptyCollection;
}

jquery.extend = function () {
    return Object.assign.apply(Object, arguments);
};
jquery.isFunction = (value) => typeof value === "function";
jquery.each = (values, callback) => values.forEach((value, index) => callback(index, value));

function Sortable(element, options) {
    providerOptions = options;
    providerInstances.push({element, options});
    this.option = (name, value) => calls.push(["option", name, value]);
    this.destroy = () => calls.push(["destroy"]);
}

global.window = {Sortable};
global.sx = {
    $: jquery,
    createNamespace: function (spec, where) {
        let current = where || this;
        spec.split(".").forEach((part) => {
            current[part] = current[part] || {};
            current = current[part];
        });
        return current;
    }
};

const adapterPath = path.join(__dirname, "../src/widgets/sortable/assets/src/sortable-adapter.js");
vm.runInThisContext(fs.readFileSync(adapterPath, "utf8"), {filename: adapterPath});

let updateEvent = null;
const adapter = sx.backend.sortable.create(rootCollection, {
    handle: ".move",
    itemSelector: ".item",
    onUpdate: (event) => {
        updateEvent = event;
    }
});

assert.equal(adapter.engine, "sortablejs");
assert.equal(adapter.isInitialized(), true);
assert.equal(providerOptions.handle, ".move");
assert.equal(providerOptions.filter, "input, textarea, button, select, option");
assert.equal(providerOptions.preventOnFilter, false);
assert.equal(providerOptions.draggable, ".item");
assert.equal(providerOptions.ghostClass, "ui-state-highlight");

providerOptions.onStart({item: itemElement, from: rootElement, to: rootElement, oldIndex: 1, newIndex: 1});
providerOptions.onEnd({item: itemElement, from: rootElement, to: rootElement, oldIndex: 1, newIndex: 2});

assert.equal(updateEvent.item, itemElement);
assert.equal(updateEvent.container, rootElement);
assert.equal(updateEvent.oldIndex, 1);
assert.equal(updateEvent.newIndex, 2);

adapter.refresh().disable().enable().destroy();
assert.deepEqual(calls, [
    ["option", "disabled", true],
    ["option", "disabled", false],
    ["destroy"]
]);
assert.equal(adapter.isInitialized(), false);

let connectedUpdateEvent = null;
const connectedAdapter = sx.backend.sortable.create(connectedCollection, {
    itemSelector: "> li",
    group: "connected-lists",
    onUpdate: (event) => {
        connectedUpdateEvent = event;
    }
});
const connectedInstances = providerInstances.slice(-2);

assert.equal(connectedInstances.length, 2);
assert.equal(connectedInstances[0].element, rootElement);
assert.equal(connectedInstances[1].element, secondRootElement);
assert.equal(connectedInstances[0].options.group, "connected-lists");
assert.equal(connectedInstances[1].options.group, "connected-lists");

connectedInstances[0].options.onEnd({
    item: itemElement,
    from: rootElement,
    to: secondRootElement,
    oldIndex: 0,
    newIndex: 1
});

assert.equal(connectedUpdateEvent.from, rootElement);
assert.equal(connectedUpdateEvent.container, secondRootElement);
assert.equal(connectedUpdateEvent.jFrom.get(0), rootElement);
assert.equal(connectedUpdateEvent.jContainer.get(0), secondRootElement);
assert.equal(connectedUpdateEvent.oldIndex, 0);
assert.equal(connectedUpdateEvent.newIndex, 1);

connectedAdapter.destroy();
assert.equal(connectedAdapter.isInitialized(), false);

console.log("Backend sortable adapter: OK");
