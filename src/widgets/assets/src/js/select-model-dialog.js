/*!
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 21.08.2017
 */
(function(sx, $, _)
{

    sx.classes.SelectModelDialog = sx.classes.Component.extend({

         _init: function()
        {
            var self = this;

            this.Window = null;

            sx.EventManager.bind(this.get('callbackEventName'), function(e, data)
            {
                self.update(data);
                if (self.get('multiple'))
                {
                    self.add(data);
                } else
                {
                    self.update(data);
                }

            });
        },

        _onDomReady: function()
        {
            var self = this;

            this.jQueryCreateBtn        = $(".sx-btn-create", this.jQuryWrapper());
            this.jQueryInput            = $("input", this.jQuryWrapper());
            this.jQueryContentWrapper   = $(".sx-view-cms-content", this.jQuryWrapper());
            this.jQueryDeselectBtn      = $(".sx-btn-deselect", this.jQuryWrapper());

            this.jQueryCreateBtn.on("click", function()
            {
                self.openModalWindow();
                return this;
            });

            this.jQueryDeselectBtn.on("click", function()
            {
                self.update({});
                return this;
            });


            if (this.get('initClientData'))
            {
                self.update(this.get('initClientData'));
            }
        },



        update: function(data)
        {
            var self = this;

            this.jQueryContentWrapper.empty();
            this.jQueryDeselectBtn.hide();

            if (_.size(data) > 0)
            {
                this.jQueryContentWrapper.append(
                    this.renderItem(data)
                );

                self.setVal(data.id);
                this.jQueryDeselectBtn.show();
            } else
            {
                self.setVal();
            }

            if (this.get('closeDialogAfterSelect'))
            {
                if (this.Window)
                {
                    this.Window.close();
                }

            }

            self.trigger('change', data);

            return this;
        },

        renderItem: function(data)
        {
            if (!this.get('previewValueClientCallback'))
            {
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
        setVal: function(id)
        {
            this.jQueryInput.val(id).change();
            return this;
        },


        /**
        *
        * @returns {sx.classes.SelectOneImage}
        */
        openModalWindow: function()
        {
            this.Window = new sx.classes.Window(this.get('url'), 'sx-select-input-' + this.get('id'));
            this.Window.open();

            return this;
        },
        /**
        *
        * @returns {*|HTMLElement}
        */
        jQuryWrapper: function()
        {
            return $('#' + this.get('id'));
        }
    });

    sx.classes.SelectModelDialogMultiple = sx.classes.SelectModelDialog.extend({

        _init: function()
        {
            var self = this;

            this.Window = null;

            sx.EventManager.bind(this.get('callbackEventName'), function(e, data)
            {
                self.add(data);
            });
        },

        _onDomReady: function()
        {
            var self = this;

            this.jQueryCreateBtn        = $(".sx-btn-create", this.jQuryWrapper());
            this.jQueryInput            = $("select", this.jQuryWrapper());


            this.jQueryContentWrapper   = $(".sx-view-cms-content", this.jQuryWrapper());
            this.jQueryDeselectBtn      = $(".sx-btn-deselect", this.jQuryWrapper());

            this.jQueryCreateBtn.on("click", function()
            {
                self.openModalWindow();
                return this;
            });

            this.jQueryDeselectBtn.on("click", function()
            {
                self.update({});
                return this;
            });

            if (this.get('initClientData'))
            {
                _.each(this.get('initClientData'), function(data, key){
                    self.add(data);
                });
            }
        },

        /**
         * @param itemData
         * @returns {sx.classes.SelectModelDialogMultiple}
         */
        add: function(itemData)
        {
            var self = this;

            if (_.size(itemData) > 0)
            {
                var jLi = $('<li>', {
                    'data-id' : itemData.id
                });

                jLi.append(
                    self.renderItem(itemData)
                )

                jLiCloseBtm = $("<a>", {
                    'href' : '#',
                    'class' : 'sx-close-btn pull-right',
                }).append('<i class="glyphicon glyphicon-remove"></i>').appendTo(jLi);

                jLiCloseBtm.on('click', function()
                {
                    jLi.slideUp();

                    var id = jLi.data('id');
                    var value = self.getVal();
                    value = _.without(value, String(id));

                    self.setVal(value);

                    _.delay(function(){
                        jLi.remove()
                    }, 500);

                    return false;
                });

                self.jQueryContentWrapper.append(jLi);

                var val = this.getVal();
                if (val)
                {
                    val.push(String(itemData.id));
                } else
                {
                    val = [itemData.id];
                }

                val = _.uniq(val);
                self.setVal(val);
                this.jQueryDeselectBtn.show();
            }

            if (this.get('closeDialogAfterSelect'))
            {
                if (this.Window)
                {
                    this.Window.close();
                }

            }

            self.trigger('change', itemData);

            return this;
        },


        /**
         * @returns {*}
         */
        getVal: function()
        {
            return this.jQueryInput.val();
        },

        /**
         *
         * @param id
         * @returns {sx.classes.SelectModelDialog}
         */
        setVal: function(data)
        {
            var self = this;
            self.jQueryInput.empty();

            _.each(data, function(id, key){
                self.jQueryInput.append(
                    $('<option>', {
                        'value' : id,
                        'selected' : 'selected'
                    }).append(id)
                )
            });

            this.jQueryInput.change();
            return this;
        },


    });

})(sx, sx.$, sx._);