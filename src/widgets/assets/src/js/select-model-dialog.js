/*!
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 21.08.2017
 */
(function (sx, $, _) {

    sx.classes.SelectModelDialog = sx.classes.Component.extend({

        _init: function () {
            var self = this;
            this.Window = null;
        },

        _onDomReady: function () {
            var self = this;

            this._isChangeAllow = false;

            this.jQueryCreateBtn().on("click", function () {
                self.openModalWindow();
                return this;
            });

            this.jQueryDeselectBtn().on("click", function () {
                self.update({});
                return this;
            });


            if (this.get('initClientData')) {
                self.update(this.get('initClientData'));
            }

            self._isChangeAllow = true;
        },


        update: function (data) {
            var self = this;

            this.jQueryContentWrapper().empty();
            this.jQueryDeselectBtn().hide();

            if (_.size(data) > 0) {
                var rendered = this.renderItem(data);
                this.jQueryContentWrapper().append(
                    rendered
                );

                self.setVal(data[self.get('modelpk')]);
                this.jQueryDeselectBtn().show();
            } else {
                self.setVal();
            }

            if (this.get('closeDialogAfterSelect')) {
                if (this.Window) {
                    this.Window.close();
                }
            }

            self.trigger('change', data);

            return this;
        },

        renderItem: function (data) {
            if (!this.get('previewValueClientCallback')) {
                return '';
            }

            var callback = this.get('previewValueClientCallback');
            return callback(data);
        },

        /**
         *
         * @param id
         * @returns {sx.classes.SelectModelDialog}
         */
        setVal: function (id) {
            this.jQueryInput().val(id);
            if (this._isChangeAllow) {
                this.jQueryInput().change();
            }
            return this;
        },


        /**
         *
         * @returns {sx.classes.SelectOneImage}
         */
        openModalWindow: function () {
            var self = this;

            //this.Window = new sx.classes.Window(this.get('url'), 'sx-select-input-' + this.get('id'));
            this.Window = new sx.classes.Window(this.get('url'), 'sx-select-input-' + this.get('id'));
            this.Window.on(this.get('callbackEventName'), function (e, data) {
                if (self.get('multiple')) {
                    self.add(data);
                } else {
                    self.update(data);
                }
            });

            this.Window.open();

            return this;
        },
        /**
         *
         * @returns {*|HTMLElement}
         */
        jQuryWrapper: function () {
            return $('#' + this.get('id'));
        },
        /**
         *
         * @returns {*|HTMLElement}
         */
        jQueryDeselectBtn: function () {
            return $(".sx-btn-deselect", this.jQuryWrapper());
        },
        /**
         *
         * @returns {*|HTMLElement}
         */
        jQueryContentWrapper: function () {
            return $(".sx-view-cms-content", this.jQuryWrapper());
        },
        /**
         *
         * @returns {*|HTMLElement}
         */
        jQueryInput: function () {
            return $("input", this.jQuryWrapper());
        },
        /**
         *
         * @returns {*|HTMLElement}
         */
        jQueryCreateBtn: function () {
            return $(".sx-btn-create", this.jQuryWrapper());
        }
    });

    sx.classes.SelectModelDialogMultiple = sx.classes.SelectModelDialog.extend({

        _init: function () {
            var self = this;

            this.Window = null;

            /*sx.EventManager.bind(this.get('callbackEventName'), function (e, data) {
                self.add(data, true);
            });*/
            //sx.Window.openerWidgetTriggerEvent(this.get('callbackEventName'), data);
        },

        /**
         *
         * @returns {*|HTMLElement}
         */
        jQueryInput: function () {
            return $("select", this.jQuryWrapper());
        },

        _onDomReady: function () {
            var self = this;

            this.jQueryCreateBtn().on("click", function () {
                self.openModalWindow();
                return this;
            });

            this.jQueryDeselectBtn().on("click", function () {
                self.update({});
                return this;
            });

            if (this.get('initClientData')) {
                _.each(this.get('initClientData'), function (data, key) {
                    self.add(data, false);
                });
            }
        },

        /**
         * @param itemData
         * @returns {sx.classes.SelectModelDialogMultiple}
         */
        add: function (itemData, triggerUpdate) {
            var triggerUpdate = triggerUpdate;
            var self = this;

            if (_.size(itemData) > 0) {
                var jLi = $('<li>', {
                    'data-id': itemData[self.get('modelpk')]
                });

                var jContainer = $("<div>", {
                    'class': 'd-flex flex-row'
                });
                jLi.append(jContainer);


                jContainer.append($("<div class='my-auto'>").append(self.renderItem(itemData)));

                jLiCloseBtm = $("<a>", {
                    'href': '#',
                    'class': 'sx-close-btn',
                }).append('<i class="fa fa-times"></i>');

                jContainer.append($("<div class='sx-close-btn-wrapper my-auto'>").append(jLiCloseBtm));

                jLiCloseBtm.on('click', function () {
                    jLi.fadeOut();

                    var id = jLi.data('id');
                    var value = self.getVal();
                    value = _.without(value, String(id));

                    self.setVal(value, true);

                    _.delay(function () {
                        jLi.remove()
                    }, 500);

                    return false;
                });

                self.jQueryContentWrapper().append(jLi);

                var val = this.getVal();
                if (val) {
                    val.push(String(itemData[self.get('modelpk')]));
                } else {
                    val = [itemData[self.get('modelpk')]];
                }

                val = _.uniq(val);
                self.setVal(val, triggerUpdate);
                this.jQueryDeselectBtn().show();
            }

            if (this.get('closeDialogAfterSelect')) {
                if (this.Window) {
                    this.Window.close();
                }

            }

            self.trigger('change', itemData);

            return this;
        },


        /**
         * @returns {*}
         */
        getVal: function () {
            return this.jQueryInput().val();
        },

        /**
         *
         * @param id
         * @returns {sx.classes.SelectModelDialog}
         */
        setVal: function (data, triggerUpdate) {
            var triggerUpdate = triggerUpdate;

            var self = this;
            self.jQueryInput().empty();

            _.each(data, function (id, key) {
                self.jQueryInput().append(
                    $('<option>', {
                        'value': id,
                        'selected': 'selected'
                    }).append(id)
                )
            });

            if (triggerUpdate) {
                this.jQueryInput().change();
            }
            return this;
        },


    });

})(sx, sx.$, sx._);