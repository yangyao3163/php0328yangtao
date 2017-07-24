<?php
namespace backend\widgets;
use yii\base\Widget;
use yii\helpers\Json;
use yii\web\JqueryAsset;
use yii\web\JsExpression;
use yii\web\View;
class ZTreeWidget extends Widget
{
    public $html_id = 'ztree';
    public $setting='{}';
    public $zNodes=[];
    public $expandAll=true;
    public $selectNodes=[];
    public function run()
    {
        $this->registerCssFiles();
        $this->registerJsFiles();
        $this->registerJs();
        return '<ul id="'.$this->html_id.'" class="ztree"></ul>';
    }
    private function registerCssFiles()
    {
        $this->view->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
    }
    private function registerJsFiles()
    {
        $this->view->registerJsFile('@web/zTree/js/jquery.ztree.core.js',['depends'=>JqueryAsset::className()]);
    }
    private function registerJs()
    {
        $z = Json::encode($this->zNodes);
        $js = new JsExpression(<<<JS
        var zTreeObj;var setting = {$this->setting};var zNodes = {$z};
JS
        );
        $this->view->registerJs($js,View::POS_END);
        $this->view->registerJs(new JsExpression('zTreeObj = $.fn.zTree.init($("#'.$this->html_id.'"), setting, zNodes);'));
        if($this->expandAll){
            $this->view->registerJs(new JsExpression('zTreeObj.expandAll(true);'));
        }
        //选中节点
        foreach($this->selectNodes as $k=>$v){
            $this->view->registerJs(new JsExpression('zTreeObj.selectNode(zTreeObj.getNodeByParam("'.$k.'","'.$v.'",null));'));
        }
    }
}