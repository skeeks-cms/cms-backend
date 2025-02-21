/*!
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.02.2015
 */
(function(sx, $, _)
{
    sx.createNamespace('classes.backend.widgets', sx);

    sx.classes.backend.widgets.ActionInstance = sx.classes.Component.extend({

        _windowReady: function() {
            var self = this;

            var hash = window.location.hash;

            if (window.location.hash) {

                if (hash.indexOf("#sx-open=") != -1) {
                    var url = hash.replace("#sx-open=", "");

                    sx.onReady(function() {
                        new sx.classes.backend.widgets.Action({
                            'url' : url,
                            'isOpenNewWindow' : true
                        }).go();
                    });
                }

            }

        }
    });

    sx.BackendAction = new sx.classes.backend.widgets.ActionInstance();

    sx.classes.backend.widgets.Action = sx.classes.Component.extend({

        _init: function() {
            var self = this;

            var currentWindowHref = window.location.href;

            this._window = new sx.classes.Window(this.get('url'), this.get('newWindowName'));
            this._window.setCenterOptions().disableResize().disableLocation();

            if (self.get("size")) {
                this._window.set("size", self.get("size"));
            }

            this.isUpdateAfterClose = false;


            this._window.on('afterOpen', function() {

                /*window.history.pushState({}, '', self.get('url'));*/

            });

            this._window.on('close', function() {

                /*window.history.go(-1);*/
                //TODO: доработать если много открытых окон
                if (currentWindowHref.indexOf("#") != -1) {
                    currentWindowHref = currentWindowHref.substring(0, currentWindowHref.indexOf("#"));
                }
                sx.Window.getMainWindow().history.replaceState({}, '', currentWindowHref);

                if (self.isUpdateAfterClose) {
                    self.updateSuccess();
                }
                self.isUpdateAfterClose = false;
            });

            this._window.on('model-update', function(e, data) {
                self.isUpdateAfterClose = true;
                console.log(data);
                sx.Window.openerWidgetTriggerEvent('model-update');

                //console.log('model-update');
                if (data && data.submitBtn == 'save') {
                    //console.log('model-update save');
                    self._window.close();
                }

            });

            this._window.on('model-create', function(e, data) {
                self.isUpdateAfterClose = true;
                sx.Window.openerWidgetTriggerEvent('model-create');

                if (data && data.submitBtn == 'save') {
                    self._window.close();
                }
            });
        },

        /**
         * @returns {sx.classes.app.controllerAction}
         */
        go: function()
        {
            var self = this;

            if (this.get("confirm"))
            {
                sx.confirm(this.get("confirm"), {
                    'no' : function()
                    {},
                    'yes' : function()
                    {
                        self._go();
                    }
                });
            } else
            {
                return this._go();
            }
        },

        _go: function()
        {
            var self = this;
            //Надо делать ajax запрос
            if (this.get("request") == 'ajax')
            {
                if (this.get("method", "post") == "post")
                {
                    this.ajax = sx.ajax.preparePostQuery(this.get('url'));
                } else
                {
                    this.ajax = sx.ajax.prepareGetQuery(this.get('url'));
                }

                this.ajax.onSuccess(function(e, data)
                {
                    if (data.response.success)
                    {
                        sx.notify.success(data.response.message);
                        self.updateSuccess();
                    } else
                    {
                        sx.notify.error(data.response.message);
                    }
                });

                this.ajax.onError(function(e, response)
                {
                    sx.notify.error(response.errorThrown + '. Обратитесь к разарботчикам');
                });

                this.ajax.execute();
                return this;
            }

            if (this.get('isOpenNewWindow') && window.name != this.get('newWindowName'))
            {
                this._window.open().focus();
                return this;
            }

            location.href = this.get('url');
        },

        updateSuccess: function() {
            var successCallback = this.get('updateSuccessCallback');
            if (successCallback) {
                return successCallback(this);
            }
            if (this.get('pjax-id')) {
                $.pjax.reload('#' + this.get('pjax-id'), {});
            } else {
                //window.location.reload();
                sx.Window.trigger('reload');
            }
        },
        
        /**
         * @returns {sx.classes.Window|*}
         */
        getWindow: function() {
            return this._window;
        }
    });

})(sx, sx.$, sx._);