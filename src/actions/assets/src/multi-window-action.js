/*!
 * Opens parameterized grid multi-actions in a standard backend iframe window.
 */
(function(sx, $, _)
{
    sx.createNamespace('classes.grid', sx);

    sx.classes.grid.MultiWindowAction = sx.classes.grid.MultiAction.extend({

        _go: function()
        {
            var self = this;
            var data = this.Grid.getDataForRequest();
            data[this.get('selectionPrepareParam')] = 1;

            var ajax = sx.ajax.preparePostQuery(this.get('url'));
            ajax.setData(data);

            this.Grid.getBlocker().block();
            new sx.classes.AjaxHandlerNoLoader(ajax);

            ajax.onSuccess(function(e, data)
            {
                self.Grid.getBlocker().unblock();

                if (!data.response.success) {
                    sx.notify.error(data.response.message);
                    return;
                }

                var windowAction = new sx.classes.backend.widgets.Action({
                    url: data.response.data.url,
                    isOpenNewWindow: true,
                    size: self.get('size'),
                    updateSuccessCallback: function() {
                        self.Grid.reload();
                    }
                });

                windowAction.go();
            });

            ajax.onError(function(e, response)
            {
                self.Grid.getBlocker().unblock();
                sx.notify.error(response.errorThrown);
            });

            ajax.execute();
            return this;
        }
    });

})(sx, sx.$, sx._);
