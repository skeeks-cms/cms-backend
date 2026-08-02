/*!
 * Dependency-light backend iframe drawer.
 */
(function (window, sx, $, _) {
    "use strict";

    sx.createNamespace("classes", sx);

    var getMainWindow = function () {
        if (sx.Window && sx.Window.openerWindow && sx.Window.openerWindow()) {
            return sx.Window.openerWindow();
        }

        return window;
    };

    var BackendWindowStack = function (mainWindow) {
        this.mainWindow = mainWindow;
        this.document = mainWindow.document;
        this.entries = [];
        this.duration = 240;
        this._onKeydown = this._onKeydown.bind(this);
    };

    BackendWindowStack.prototype._onKeydown = function (event) {
        if (event.key !== "Escape" || !this.entries.length) {
            return;
        }

        event.preventDefault();
        this.close(this.entries[this.entries.length - 1].widget);
    };

    BackendWindowStack.prototype._createEntry = function (widget) {
        var doc = this.document;
        var size = widget.get("size") || "default";
        var layer = doc.createElement("div");
        var backdrop = doc.createElement("button");
        var panel = doc.createElement("div");
        var iframe = doc.createElement("iframe");
        var close = doc.createElement("button");

        layer.className = "sx-backend-window sx-backend-window--" + size;
        layer.setAttribute("role", "dialog");
        layer.setAttribute("aria-modal", "true");
        layer.setAttribute("aria-label", widget.get("label") || "Backend window");

        backdrop.className = "sx-backend-window__backdrop";
        backdrop.type = "button";
        backdrop.tabIndex = -1;
        backdrop.setAttribute("aria-label", "Close");

        panel.className = "sx-backend-window__panel";
        panel.style.setProperty(
            "--sx-backend-window-stack-offset",
            (this.entries.length * 15) + "px"
        );

        iframe.className = "sx-backend-window__frame";
        iframe.name = widget.getName();
        iframe.allowFullscreen = true;
        iframe.setAttribute("allow", "autoplay; fullscreen");
        iframe.setAttribute("title", widget.get("label") || "Backend window");

        close.className = "sx-backend-window__close";
        close.type = "button";
        close.setAttribute("aria-label", "Close");
        close.setAttribute("title", "Close");

        panel.appendChild(iframe);
        panel.appendChild(close);
        layer.appendChild(backdrop);
        layer.appendChild(panel);

        return {
            id: widget.getName(),
            src: String(widget._src || ""),
            openedAt: Date.now(),
            widget: widget,
            layer: layer,
            backdrop: backdrop,
            panel: panel,
            iframe: iframe,
            close: close,
            blocker: null,
            previousFocus: doc.activeElement
        };
    };

    BackendWindowStack.prototype.open = function (widget) {
        var self = this;
        var duplicate = this.findRecentBySource(widget._src, 1000);

        if (duplicate) {
            widget._openedWindow = duplicate.iframe.contentWindow;
            duplicate.close.focus();
            return duplicate;
        }

        var entry = this._createEntry(widget);

        entry.close.addEventListener("click", function () {
            self.close(widget);
        });
        entry.backdrop.addEventListener("click", function () {
            self.close(widget);
        });
        entry.iframe.addEventListener("load", function () {
            var iframeWindow = entry.iframe.contentWindow;
            var loadedUrl = "";

            try {
                loadedUrl = iframeWindow.location.href;
            } catch (error) {
                // Cross-origin drawers are allowed. A load event is enough to
                // remove the parent-side loading state in that case.
            }

            if (loadedUrl === "about:blank" && widget._src !== "about:blank") {
                return;
            }

            widget._openedWindow = iframeWindow;
            try {
                if (iframeWindow.sx && iframeWindow.sx.Window) {
                    iframeWindow.sx.Window._openerWindowWidget = widget;
                }
            } catch (error) {
                // The backend normally uses same-origin frames. Keep the
                // drawer usable if a custom action intentionally does not.
            }

            if (entry.blocker) {
                entry.blocker.unblock();
            }
        });

        this.entries.push(entry);
        entry.layer.style.zIndex = String(100000 + this.entries.length);
        this.document.body.appendChild(entry.layer);
        this.document.documentElement.classList.add("sx-backend-window-is-open");
        this.document.body.classList.add("sx-backend-window-is-open");

        entry.blocker = sx.block($(entry.panel));

        if (this.entries.length === 1) {
            this.document.addEventListener("keydown", this._onKeydown);
        }

        entry.iframe.src = widget._src;
        this.mainWindow.requestAnimationFrame(function () {
            entry.layer.classList.add("is-open");
            entry.close.focus();
        });

        widget._openedWindow = entry.iframe.contentWindow;
        widget.trigger("afterOpen");

        return entry;
    };

    BackendWindowStack.prototype.find = function (widget) {
        for (var i = this.entries.length - 1; i >= 0; i--) {
            if (this.entries[i].widget === widget) {
                return this.entries[i];
            }
        }

        return null;
    };

    BackendWindowStack.prototype.findRecentBySource = function (src, maxAge) {
        var normalizedSrc = String(src || "");
        var now = Date.now();

        for (var i = this.entries.length - 1; i >= 0; i--) {
            var entry = this.entries[i];
            if (
                entry.src === normalizedSrc
                && !entry.layer.classList.contains("is-closing")
                && now - entry.openedAt <= maxAge
            ) {
                return entry;
            }
        }

        return null;
    };

    BackendWindowStack.prototype.close = function (widget) {
        var self = this;
        var entry = this.find(widget);

        if (!entry || entry.layer.classList.contains("is-closing")) {
            return false;
        }

        widget.trigger("beforeClose");
        if (!widget.isAllowClose) {
            return false;
        }

        if (entry.blocker && entry.blocker.isBlocked()) {
            entry.blocker.unblock();
        }

        entry.layer.classList.remove("is-open");
        entry.layer.classList.add("is-closing");

        this.mainWindow.setTimeout(function () {
            var index = self.entries.indexOf(entry);
            if (index !== -1) {
                self.entries.splice(index, 1);
            }
            if (entry.layer.parentNode) {
                entry.layer.parentNode.removeChild(entry.layer);
            }

            if (!self.entries.length) {
                self.document.removeEventListener("keydown", self._onKeydown);
                self.document.documentElement.classList.remove("sx-backend-window-is-open");
                self.document.body.classList.remove("sx-backend-window-is-open");
            }

            if (
                entry.previousFocus
                && entry.previousFocus.focus
                && self.document.contains(entry.previousFocus)
            ) {
                entry.previousFocus.focus();
            }

            widget._openedWindow = null;
            widget.trigger("close");
        }, this.duration);

        return true;
    };

    BackendWindowStack.prototype.focus = function (widget) {
        var entry = this.find(widget);
        if (entry) {
            entry.close.focus();
        }
    };

    var getStack = function () {
        var mainWindow = getMainWindow();
        if (!mainWindow.sx.BackendWindowStack) {
            mainWindow.sx.BackendWindowStack = new BackendWindowStack(mainWindow);
        }

        return mainWindow.sx.BackendWindowStack;
    };

    sx.classes.BackendWindow = sx.classes._Window.extend({
        open: function () {
            this.trigger("beforeOpen");
            getStack().open(this);
            return this;
        },

        focus: function () {
            getStack().focus(this);
            return this;
        },

        close: function () {
            getStack().close(this);
            return this;
        },

        getMainWindow: getMainWindow,

        getBackendWindowInstances: function () {
            return getStack().entries;
        },

        getFancyWindowInstances: function () {
            return this.getBackendWindowInstances();
        },

        addFancyWindow: function () {
            return this;
        },

        closeFancyWindow: function () {
            return this;
        }
    });
})(window, sx, sx.$, sx._);
