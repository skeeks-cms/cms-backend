<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.05.2015
 */

namespace skeeks\cms\backend\actions;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class BackendModelMultiDialogEditAction extends BackendModelMultiAction
{
    public $viewDialog = "";

    public $dialogOptions = [
        'style' => 'min-height: 500px; min-width: 600px;',
    ];

    /**
     * @param GridView $grid
     * @return string
     */
    public function registerForGrid($grid)
    {
        $dialogId = $this->getGridActionId($grid);

        $clientOptions = Json::encode(ArrayHelper::merge($this->getClientOptions(), [
            'dialogId' => $dialogId,
        ]));

        $grid->view->registerJs(<<<JS
(function(sx, $, _)
{

    sx.createNamespace('sx.classes.grid', sx);

    sx.classes.grid.MultiDialogAction = sx.classes.grid.MultiAction.extend({

        _onDomReady: function()
        {
            var self = this;

            this.jDialog = $( '#' + this.get('dialogId') );
            this.jDialogContent = $( '.modal-content', this.jDialog );

            this.Blocker = new sx.classes.Blocker(this.jDialogContent);

            $('form', this.jDialog).on('beforeSubmit', function() {
                return false;
            });
            
            this.CurrentAjax = false;
            
            $('form', this.jDialog).on('submit', function()
            {
                /*console.log('MultiDialogAction submit');*/
                //Пресечь повторную отправку
                if (self.CurrentAjax !== false) {
                    /*console.log('alrady submited');*/
                    return false;
                }
                
                var data = _.extend({
                    'formData' : $(this).serialize()
                }, self.Grid.getDataForRequest());

                self.Blocker.block();

                //self.set("url", $(this).attr("action"));
                
                var ajax = self.createAjaxQuery(data);
                self.CurrentAjax = ajax;

                ajax.onComplete(function(e, data)
                {
                    self.jDialog.modal('hide');
                    self.Blocker.unblock();
                    self.CurrentAjax = false;
                    /*_.delay(function()
                    {
                        self.jDialog.modal('hide');
                    }, 1000);
*/
                });
                
                _.delay(function() {
                    ajax.execute();
                }, 300);

                return false;
                
            });

        },

        _go: function()
        {
            var self = this;
            self.jDialog.modal('show');
        },

    });

    new sx.classes.grid.MultiDialogAction(sx.Grid{$grid->id}, '{$this->id}' ,{$clientOptions});
})(sx, sx.$, sx._);
JS
        );
        $content = '';
        if ($this->viewDialog) {
            $content = $this->controller->view->render($this->viewDialog, [
                'action' => $this,
            ]);
        }

        return \Yii::$app->view->render('@skeeks/cms/backend/actions/views/multi-dialog', [
            'dialogId' => $dialogId,
            'content'  => $content,
        ], $this);
    }


    /**
     * @param GridViewStandart $grid
     * @return string
     */
    public function getGridActionId($grid)
    {
        return $grid->id."-".$this->id;
    }

}