/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

(function (sx, $, _) {

    sx.createNamespace('classes.form', sx);

    sx.classes.form.FormButtonsBackend = sx.classes.Component.extend({

        _initCloseWindow: function () {
            if (this.onBeforeUnload === false) {
                this.onBeforeUnload = true;

                /*window.onbeforeunload = function () {
                    return 'Данные в форме НЕ СОХРАНЕНЫ. Вы действительно хотите уйти со страницы?'
                };*/
            }
        },

        _init: function () {
            this.onBeforeUnload = false;
        },

        /**
         * Выполнить действие с проверкой потери данных
         * @param callback
         * @returns {*}
         */
        checkAction: function (callback) {
            if (this.isFormDataChanged()) {
                sx.confirm('Данные в форме НЕ СОХРАНЕНЫ. Вы действительно хотите уйти со страницы?', {
                    'no': function () {
                    },
                    'yes': function () {
                        return callback();
                    }
                });
            } else {
                return callback();
            }
        },

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
            $(".sx-btn-save-and-close", this.getJButtons()).on("click", function () {
                return self.go("save");
            });

            $(".sx-btn-save", this.getJButtons()).on("click", function () {
                return self.go("apply");
            });

            $(".sx-btn-close", this.getJButtons()).on("click", function () {
                self.go("close");
                return false;
            });


            //Клавиши ctrl+s
            document.addEventListener("keydown", function (e) {
                //Горячая клавиша ctrl+z закрывает страницу
                if ((window.navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey) && e.keyCode == 90) {
                    e.preventDefault();

                    if ($(".sx-btn-close", self.getJForm()).length) {
                        $(".sx-btn-close", self.getJForm()).click();
                        return false;
                    }

                    return false;
                }

                //Горячая клавиша ctrl+s пытается сохранить форму и остаться на странице
                if ((window.navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey) && e.keyCode == 83) {
                    e.preventDefault();

                    if ($(".sx-btn-save", self.getJForm()).length) {
                        $(".sx-btn-save", self.getJForm()).click();
                        return false;
                    }
                    if ($(".sx-btn-save-and-close", self.getJForm()).length) {
                        $(".sx-btn-save-and-close", self.getJForm()).click();
                        return false;
                    }

                    return false;
                }

                //Горячая клавиша ctrl+enter пытается сохранить форму и закрыть окно
                if ((window.navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey) && ((event.keyCode == 0xA) || (event.keyCode == 0xD))) {
                    e.preventDefault();

                    if ($(".sx-btn-save-and-close", self.getJForm()).length) {
                        $(".sx-btn-save-and-close", self.getJForm()).click();
                        return false;
                    }

                    if ($(".sx-btn-save", self.getJForm()).length) {
                        $(".sx-btn-save", self.getJForm()).click();
                        return false;
                    }

                    return false;
                }

            }, false);


            //Отслеживание изменения данных в форме
            $("input, select, textarea", this.getJForm()).on("change", function () {
                self.getJForm().addClass("sx-form-data-changed");
                self._initCloseWindow();
            });
            $("input, select, textarea", this.getJForm()).on("keyup", function () {
                self.getJForm().addClass("sx-form-data-changed");
                self._initCloseWindow();
            });

            if (sx.Window.openerWidget()) {
                var widget = sx.Window.openerWidget();
                widget.on("beforeClose", function(e, data) {
                    console.log("beforeClose");
                    widget.isAllowClose = false;
                    self.checkAction(function () {
                        widget.isAllowClose = true;
                    });
                });
            }


            //Если разрешены кнопки впред назад
            if (this.get('isprevnext')) {

                if (this.get('isprevnextkeyboard')) {
                    //Прехеоды стрелками вправо и влево
                    addEventListener("keydown", function (e) {
                        switch (e.keyCode) {
                            case 39:  //стрелка вправо
                                if ($(":focus").closest("#" + self.getJForm().attr("id")).length) {
                                    return false;
                                }
                                if ($(":focus").hasClass("form-control")) {
                                    return false;
                                }

                                var jNext = $(".sx-btn-next", self.getJButtons());
                                if (jNext.is(":visible")) {
                                    jNext.click();
                                }

                                break;

                            case 37:  //стрелка влево
                                if ($(":focus").hasClass("form-control")) {
                                    return false;
                                }
                                if ($(":focus").closest("#" + self.getJForm().attr("id")).length) {
                                    return false;
                                }

                                var jPrev = $(".sx-btn-prev", self.getJButtons());
                                if (jPrev.is(":visible")) {
                                    jPrev.click();
                                }

                                break;
                        }
                    });
                }


                $(".sx-btn-prev", this.getJButtons()).on("click", function () {
                    var currentUrl = window.location.href;
                    var currentId = self.get("model-id");
                    var newUrl = currentUrl.replace("pk=" + currentId, "pk=" + $(this).data('pk'));
                    self.checkAction(function() {
                        window.location.href = newUrl;
                    });
                    return false;
                });

                $(".sx-btn-next", this.getJButtons()).on("click", function () {
                    var currentUrl = window.location.href;
                    var currentId = self.get("model-id");
                    var newUrl = currentUrl.replace("pk=" + currentId, "pk=" + $(this).data('pk'));
                    self.checkAction(function() {
                        window.location.href = newUrl;
                    });
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
            }
        },

        _onWindowReady: function () {
            var self = this;

            //Разрешить фиксирование внизу экрана
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


        isFormDataChanged: function () {
            return this.getJForm().hasClass("sx-form-data-changed");
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