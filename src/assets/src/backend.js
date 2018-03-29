 /**
  * @link https://cms.skeeks.com/
  * @copyright Copyright (c) 2010 SkeekS
  * @license https://cms.skeeks.com/license/
  * @author Semenov Alexander <semenov@skeeks.com>
  */
(function (sx, $, _) {
    sx.createNamespace('classes.backend', sx);

    sx.classes.backend.EditComponent = sx.classes.Component.extend({

        _init: function () {
            var self = this;

            this.Window = new sx.classes.Window(this.get('url'));

            this.Window.bind('close', function (e, data) {
                self.reload();
            });

            this.Window.open();
        },

        reload: function () {
            var jPjax = $('[data-pjax-container]');

            if (jPjax.length) {
                var id = jPjax.attr('id');
                if (id) {
                    $.pjax.reload('#' + id, {});
                    return this;
                }

            }

            window.location.reload();
            return this;
        },
    });

})(sx, sx.$, sx._);