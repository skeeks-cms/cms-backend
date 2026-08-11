(function (sx, $) {
    "use strict";

    sx.createNamespace("classes.backend", sx);
    sx.createNamespace("backend.sortable", sx);

    var defaults = {
        cursor: "move",
        handle: null,
        cancel: "input, textarea, button, select, option",
        itemSelector: null,
        placeholderClass: "ui-state-highlight",
        chosenClass: "sx-sortable-chosen",
        dragClass: "sx-sortable-drag",
        forceHelperSize: true,
        forcePlaceholderSize: true,
        opacity: 0.5,
        disabled: false,
        animation: 150,
        group: null,
        onStart: null,
        onUpdate: null,
        providerOptions: {}
    };

    sx.classes.backend.SortableAdapter = function (element, options) {
        this.jElement = $(element);
        this.options = $.extend({}, defaults, options || {});
        this.engine = "sortablejs";
        this.instances = [];
        this._initialized = false;

        this.init();
    };

    sx.classes.backend.SortableAdapter.prototype = {
        init: function () {
            var self = this;

            if (this._initialized || !this.jElement.length) {
                return this;
            }
            if (typeof window.Sortable !== "function") {
                throw new Error("SortableJS provider is not loaded.");
            }

            this.jElement.each(function () {
                var providerOptions = self._providerOptions();
                var providerStart = providerOptions.onStart;
                var providerEnd = providerOptions.onEnd;

                providerOptions.onStart = function (event) {
                    if ($.isFunction(providerStart)) {
                        providerStart.apply(this, arguments);
                    }
                    if ($.isFunction(self.options.onStart)) {
                        self.options.onStart.call(self, self._normalizeEvent(event));
                    }
                };

                providerOptions.onEnd = function (event) {
                    if ($.isFunction(providerEnd)) {
                        providerEnd.apply(this, arguments);
                    }
                    if (
                        $.isFunction(self.options.onUpdate)
                        && (event.from !== event.to || event.oldIndex !== event.newIndex)
                    ) {
                        self.options.onUpdate.call(self, self._normalizeEvent(event));
                    }
                };

                self.instances.push(new window.Sortable(this, providerOptions));
            });

            this._initialized = true;

            return this;
        },

        _providerOptions: function () {
            var providerOptions = $.extend({}, {
                handle: this.options.handle,
                filter: this.options.cancel,
                preventOnFilter: false,
                draggable: this.options.itemSelector,
                ghostClass: this.options.placeholderClass,
                chosenClass: this.options.chosenClass,
                dragClass: this.options.dragClass,
                disabled: this.options.disabled,
                animation: this.options.animation
            }, this.options.providerOptions || {});

            if (this.options.group) {
                providerOptions.group = this.options.group;
            }
            if (!providerOptions.handle) {
                delete providerOptions.handle;
            }
            if (!providerOptions.draggable) {
                delete providerOptions.draggable;
            }

            return providerOptions;
        },

        _normalizeEvent: function (event) {
            var jItem = event && event.item ? $(event.item) : $();
            var jContainer = event && event.to ? $(event.to) : $();

            return {
                adapter: this,
                engine: this.engine,
                item: jItem.length ? jItem.get(0) : null,
                jItem: jItem,
                container: jContainer.length ? jContainer.get(0) : null,
                jContainer: jContainer,
                from: event ? event.from : null,
                jFrom: event && event.from ? $(event.from) : $(),
                oldIndex: event && typeof event.oldIndex === "number" ? event.oldIndex : null,
                newIndex: event && typeof event.newIndex === "number" ? event.newIndex : null,
                originalEvent: event ? event.originalEvent || event : null
            };
        },

        refresh: function () {
            return this;
        },

        enable: function () {
            this._setDisabled(false);
            return this;
        },

        disable: function () {
            this._setDisabled(true);
            return this;
        },

        _setDisabled: function (disabled) {
            $.each(this.instances, function (index, instance) {
                instance.option("disabled", disabled);
            });
        },

        destroy: function () {
            if (this._initialized) {
                $.each(this.instances, function (index, instance) {
                    instance.destroy();
                });
                this.instances = [];
                this._initialized = false;
            }

            return this;
        },

        isInitialized: function () {
            return this._initialized;
        }
    };

    sx.backend.sortable.create = function (element, options) {
        return new sx.classes.backend.SortableAdapter(element, options);
    };
})(sx, sx.$);
