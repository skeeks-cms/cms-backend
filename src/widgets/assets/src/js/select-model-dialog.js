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
        },

        update: function(model)
        {
            var self = this;

            self.setVal();
            this.jQueryContentWrapper.empty();
            this.jQueryDeselectBtn.hide();

            if (_.size(model) > 0)
            {
                this.jQueryContentWrapper.append(
                    this.renderItem(model)
                );

                self.setVal(model.id);
                this.jQueryDeselectBtn.show();
            }

            if (this.get('closeDialogAfterSelect'))
            {
                if (this.Window)
                {
                    this.Window.close();
                }

            }

            self.trigger('change', model);

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
        * @param id
        */
        setVal: function(id)
        {
            $("input", this.jQuryWrapper()).val(id).change();
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
})(sx, sx.$, sx._);