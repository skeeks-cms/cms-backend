/*!
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 22.03.2018
 */
(function(sx, $, _)
{
    sx.createNamespace('classes.backend', sx);

    sx.classes.backend.EditComponent = sx.classes.Component.extend({

        _init: function()
        {
            var self = this;

            this.Window = new sx.classes.Window(this.get('url'));

            this.Window.bind('close', function(e, data)
            {
                self.reload();
            });

            this.Window.open();
        },

        reload: function()
        {
            if (this.get('enabledPjax'))
            {
                var id = null;
                var pjax = this.get('pjax');
                if (pjax.options)
                {
                    id = pjax.options.id;
                }

                if (id)
                {
                    $.pjax.reload('#' + id, {});
                    return this;
                }

            }

            window.location.reload();
            return this;
        },
    });

})(sx, sx.$, sx._);