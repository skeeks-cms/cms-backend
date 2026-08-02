/*!
 * Same-origin iframe communication for SkeekS backend widgets.
 *
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link https://skeeks.com/
 * @copyright 2010 SkeekS
 */
(function (sx, $, _) {
    sx.createNamespace('classes', sx);

    sx.classes._IframeManager = sx.classes.Component.extend({
        _init: function () {
            this.childIframes = [];
            this.isFrame = false;
            this.parentIframeManager = null;
            this.parentFrameElement = window.frameElement;

            if (window.parent.window !== window.window) {
                this.isFrame = true;
            }

            if (window.parent.window.sx.IframeManager) {
                this.parentIframeManager = window.parent.window.sx.IframeManager;
            }

            this.parentIframe = null;

            if (
                this.parentIframeManager &&
                this.isFrame &&
                this.parentFrameElement &&
                this.parentFrameElement.getAttribute('id')
            ) {
                this.parentIframe = this.parentIframeManager.findIframeById(
                    this.parentFrameElement.getAttribute('id')
                );
            }

            if (this.parentIframe) {
                this.parentIframe.trigger('initChildIframe', this);
            }
        },

        _onDomReady: function () {
            if (!this.parentIframe) {
                return;
            }

            this.parentIframe.trigger('domReady', this);

            if (this.parentIframe.isAutoHeight()) {
                this.listenHeight();
            }
        },

        registerIframe: function (iframe) {
            if (!(iframe instanceof sx.classes.Iframe)) {
                throw new Error("object must be instance of 'sx.classes._Iframe'");
            }

            this.childIframes.push(iframe);

            return this;
        },

        findIframeById: function (id) {
            if (typeof id !== 'string') {
                throw new Error('id must be string');
            }

            return _.find(this.childIframes, function (iframe) {
                return iframe.get('id') === id;
            });
        },

        listenHeight: function () {
            var self = this;

            this._actualHeight = 0;

            setInterval(function () {
                self._listenHeight();
            }, this.parentIframe.get('heightTimer', 500));
        },

        _listenHeight: function () {
            var actualHeight;

            if (this.parentIframe.get('heightSelector')) {
                actualHeight = $(this.parentIframe.get('heightSelector')).height();
            } else {
                actualHeight = $(window).height();
            }

            if (Number(this._actualHeight) !== Number(actualHeight)) {
                this._actualHeight = actualHeight;
                this.trigger('changeHeight', {
                    height: actualHeight
                });
            }
        },

        getWindow: function () {
            return window;
        },

        getSx: function () {
            return sx;
        }
    });

    sx.classes.IframeManager = sx.classes._IframeManager.extend({});
    sx.IframeManager = new sx.classes.IframeManager();

    sx.classes._Iframe = sx.classes.Component.extend({
        construct: function (id, opts) {
            opts = opts || {};

            if (typeof id !== 'string') {
                throw new Error('id must be string');
            }

            opts.id = id;
            this.applyParentMethod(sx.classes.Component, 'construct', [opts]);
        },

        _init: function () {
            var self = this;

            this.childIframeManager = false;
            this.ready = false;
            this.sx = null;

            sx.IframeManager.registerIframe(this);

            this.bind('domReady', function () {
                self.ready = true;
                self.trigger('ready', this);
            });

            this.bind('initChildIframe', function (event, childIframeManager) {
                self.childIframeManager = childIframeManager;
                self.sx = childIframeManager.getSx();

                self.childIframeManager.bind('changeHeight', function (changeEvent, data) {
                    self.setHeight(data.height);
                });
            });
        },

        onReady: function (callback) {
            if (this.ready === true) {
                callback('', this);
            } else {
                this.bind('ready', callback);
            }

            return this;
        },

        onSxReady: function (callback) {
            var self = this;

            this.onReady(function () {
                self.sx.onReady(callback);
            });

            return this;
        },

        _onDomReady: function () {
            this.JqueryIframe().attr('scrolling', this.get('scrolling', 'no'));
        },

        JqueryIframe: function () {
            return $('#' + this.get('id'));
        },

        isAutoHeight: function () {
            return Boolean(this.get('autoHeight'));
        },

        setHeight: function (newHeight) {
            var self = this;

            newHeight = Number(newHeight);

            if (Number(this.get('minHeight', 200)) > newHeight) {
                newHeight = Number(this.get('minHeight', 200));
            }

            this.onDomReady(function () {
                self.JqueryIframe().attr('height', self.get('height', newHeight));
            });

            return this;
        }
    });

    sx.classes.Iframe = sx.classes._Iframe.extend({});
})(sx, sx.$, sx._);
