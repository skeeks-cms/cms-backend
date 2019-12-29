/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

(function (sx, $, _) {

    /**
     * 
     */
    sx.classes.AjaxControllerActionsWidget = sx.classes.Component.extend({

        _init: function () {},



        _onDomReady: function () {
            var self = this;

            //Вызов первого действия

            $("body").on("firstAction", '.sx-btn-ajax-actions', function(e) {

                console.log("firstAction");

                var jQueryBtn = $(this);

                if (!jQueryBtn.data("content") || jQueryBtn.data("content").length == 0) {
                    var data = _.clone(jQueryBtn.data());
                    //Загружаем контент
                    self._createPopover(jQueryBtn, data, false);

                    jQueryBtn.on("contentComplete", function () {
                        self._goFirstAction(jQueryBtn);
                    });
                } else {
                    self._goFirstAction(jQueryBtn);
                }
            });

            $("body").on("click", '.sx-btn-ajax-actions', function(e) {
                e.preventDefault();

                $('.popover').popover('hide');

                if ($(this).hasClass('is-rendered')) {
                    $(this).popover('show');
                } else {
                    var data = _.clone($(this).data());
                    self._createPopover($(this), data);
                }
            });

            //Скрыть лишние окошки
            $("html").on("mouseup", function (e) {

                var l = $(e.target);

                if (l.closest('.popover').length > 0) {
                    return this;
                }

                if (l[0].className.indexOf("popover") == -1) {
                    $(".popover").each(function () {
                        $(this).popover("hide");
                    });
                }
            });
        },

        _goFirstAction: function(jQueryBtn) {

            if (jQueryBtn.hasClass("sx-start")) {
                console.log("Еще не завершено предыдущее действие");
                return false;
            }

            jQueryBtn.addClass("sx-start");

            var jContent = $($.parseHTML(jQueryBtn.data("content")));
            var jFirst = $("li:first", jContent);
            $('body').append($("<div>", {
                'style': 'display: none;'
            }).append(jFirst));

            jFirst.click();

            jQueryBtn.trigger("firstActionOpen");

            _.delay(function() {
                jQueryBtn.removeClass("sx-start");
            }, 1000);
            return false;
        },

        _createPopover(jQueryBtn, data, isShow = true) {

            var self = this;
            
            var url = data.url;

            jQueryBtn.popover({
                "html": true,
                //'container': "body",
                'trigger': "click",
                'boundary': 'window',
                'popover-mode':'single',
                'title': 'действия',
                'template': '<div class="popover sx-popover-ajax-controller-actions" role="tooltip">' +
                    '<div class="arrow"></div>' +
                    //'<div class="sx-title-wrapper"><h3 class="popover-title popover-header"></h3></div>' +
                    //'<a class="pill grey action_button add_to_galleries_popover__close_button btn btn-xs btn-secondary g-color-white">Close</a></div>' +
                    '<div class="popover-content list info popover-body"></div>' +
                    '</div>',
                //'content': "Загрузка..."
                'content': $("<img>", {"src" : self.get("loader")})
            });

            jQueryBtn.on('show.bs.popover', function (e, data) {
                /*/!*$(e.target).popover('destroy');*!/*/
                jQueryBtn.addClass('is-rendered');
            });

            if (isShow) {
                jQueryBtn.popover('show');
            }


            var AjaxQuery = sx.ajax.prepareGetQuery(url);
            var AjaxHandler = new sx.classes.AjaxHandlerStandartRespose(AjaxQuery, {
                'ajaxExecuteErrorAllowMessage' : false,
                'allowResponseErrorMessage' : false,
                'ajaxExecuteSuccessAllowMessage' : false,
            });

            AjaxHandler.on("success", function (e, response) {
                jQueryBtn.addClass('is-success');

                var html = response.data.html;
                html = new String(html);

                var jFirst = $("li", $.parseHTML(html));
                console.log($.parseHTML(html));


                jQueryBtn.attr('data-content', html);
                var popover = jQueryBtn.data('bs.popover');
                popover.setContent();

                jQueryBtn.popover('update');

                jQueryBtn.trigger("contentComplete");
                jQueryBtn.trigger("contentLoaded");
            });

            AjaxHandler.on("error", function (e, response) {
                /*console.log(e);
                console.log(response);*/
                jQueryBtn.attr('data-content', response.message);
                var popover = jQueryBtn.data('bs.popover');
                popover.setContent();
                jQueryBtn.popover('update');

                jQueryBtn.trigger("contentError");
                jQueryBtn.trigger("contentComplete");
            });
            
            AjaxQuery.execute();
        }
    });
    

})(sx, sx.$, sx._);