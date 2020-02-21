/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

(function (sx, $, _) {

    sx.createNamespace('classes.form', sx);

    sx.classes.form.FormButtonsBackend = sx.classes.Component.extend({

        go: function (value) {
            if (value == "close") {
                if (sx.Window.openerWidget()) {
                    sx.Window.openerWidget().close();
                    return false;
                } else {
                    window.location = this.get('indexUrl');
                    return false;
                }

                return false;
            } else {
                $("#" + this.get('input-id')).val(value);
            }

            return true;
        },

        _onDomReady: function () {
            var self = this;
            //Клик по стандартной кнопки сохранить и закрыть
            $(".sx-btn-save-and-close", this.getJButtons()).on("click", function() {
                return self.go("save");
            });

            $(".sx-btn-save", this.getJButtons()).on("click", function() {
                return self.go("apply");
            });

            $(".sx-btn-close", this.getJButtons()).on("click", function() {
                return self.go("close");
            });





            $(".sx-btn-prev", this.getJButtons()).on("click", function() {
                var currentUrl = window.location.href;
                var currentId = self.get("model-id");
                var newUrl = currentUrl.replace("pk=" + currentId, "pk=" + $(this).data('pk'));
                window.location.href = newUrl;;
                return false;
            });

            $(".sx-btn-next", this.getJButtons()).on("click", function() {
                var currentUrl = window.location.href;
                var currentId = self.get("model-id");
                var newUrl = currentUrl.replace("pk=" + currentId, "pk=" + $(this).data('pk'));
                window.location.href = newUrl;;
                return false;
            });

            if (sx.Window.openerWidget()) {
                var modelId = this.get("model-id");
                if (modelId) {
                    var currentTr = sx.Window.openerWindow().$(".sx-grid-view [data-key=" + modelId + "]");
                    if (currentTr.length) {
                        var prevTr = currentTr.prev("tr");
                        var nextTr = currentTr.next("tr");

                        if (prevTr.length) {
                            var prevKey = prevTr.data("key");
                            if (prevKey) {
                                $(".sx-next-prev-btns", this.getJButtons()).show();
                                $(".sx-btn-prev", this.getJButtons()).attr("data-pk", prevKey).show();
                            }
                        }

                        if (nextTr.length) {
                            var nextKey = nextTr.data("key");
                            if (nextKey) {
                                $(".sx-next-prev-btns", this.getJButtons()).show();
                                $(".sx-btn-next", this.getJButtons()).attr("data-pk", nextKey).show();
                            }
                        }
                    }
                }
            }



        },

        _onWindowReady: function () {
            var self = this;

            if (this.get("isfixed")) {
                this._initStartPoint();
                self.updatePositions();

                $(window).on('resize', function () {
                    self._disableFixed()._initStartPoint();
                    self.updatePositions();
                });

                $(window).scroll(function () {
                    self._disableFixed()._initStartPoint();
                    self.updatePositions();
                });

                _.delay(function () {
                    self._disableFixed()._initStartPoint();
                    self.updatePositions();
                }, 500);

                _.delay(function () {
                    self._disableFixed()._initStartPoint();
                    self.updatePositions();
                }, 1000);
            }
        },




        getJForm: function () {
            return $("#" + this.get("form-id"));
        },

        getJButtons: function () {
            return $(".sx-buttons-standart-wrapper", this.getJForm());
        },



        _initStartPoint: function () {
            this.startPosition = this.getJButtons().offset().top;
            this.startPositionLeft = this.getJButtons().offset().left;
            this.buttonsWidth = this.getJButtons().width();
            this.buttonsHeight = this.getJButtons().height();
            return this;
        },

        _enableFixed: function () {
            this.getJButtons()
                .css("width", this.buttonsWidth)
                .css("position", "fixed")
                .css("bottom", "0")
                .css("left", this.startPositionLeft)
                .css("background-color", "white")
                .addClass("sx-fixed-buttons")
            ;
            return this;
        },

        _disableFixed: function () {
            this.getJButtons()
                .removeAttr("style")
                .removeClass("sx-fixed-buttons")
            ;
            return this;
        },

        updatePositions: function () {

            var pageYOffset = window.pageYOffset;
            var scrolled = $(this).scrollTop();
            var height = window.innerHeight;
            var heightAll = pageYOffset + height;

            if (heightAll < this.startPosition) {
                this._enableFixed();
            } else {
                this._disableFixed();
            }
            /*console.log("pageYOffset: " + pageYOffset)
            console.log("offset top: " + this.startPosition)
            console.log("height: " + height)
            console.log("heightAll: " + heightAll)*/
        }
    });
})(sx, sx.$, sx._);