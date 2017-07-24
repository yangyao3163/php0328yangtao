<?php
/**
 * 这是文章的添加
 */
use \kucha\ueditor\UEditor;
use yii\web\JsExpression;

$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,"name");
echo $form->field($model,"logo")->hiddenInput();
//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['someKey' => 'someValue'],
        'width' => 120,
        'height' => 40,
        'onError' => new JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadComplete' => new JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
        //将图片的地址赋值给logo字段
        $("#goods-logo").val(data.fileUrl);
        //将上传的图片展示出来
        $("#img").attr("src",data.fileUrl);
    }
}
EOF
        ),
    ]
]);
echo \yii\bootstrap\Html::img($model->logo?$model->logo:false,["id"=>"img","height"=>30]);
echo $form->field($model,"goods_category_id")->hiddenInput();
$zTree =  \backend\widgets\ZTreeWidget::widget([
    'setting'=>'{
    data: {
		simpleData: {
			enable: true,
			pIdKey: "parent_id",
		}
	},
	callback: {
		onClick: function(event, treeId, treeNode) {
            $("#goods-goods_category_id").val(treeNode.id);
        }
	}
}',
    'zNodes'=>\backend\models\GoodsCategory::getZtreeNodes(),
    'selectNodes'=>['id'=>$model->goods_category_id],
]);
echo $zTree;

echo $form->field($model,"brand_id")->dropDownList(\backend\models\Goods::getBrandOptions(),['prompt'=>'=请选择品牌=']);
echo $form->field($model,"market_price")->textInput();
echo $form->field($model,"shop_price")->textInput();
echo $form->field($model,"stock")->textInput(["type"=>"number"]);
echo $form->field($model,"is_on_sale",["inline"=>true])->radioList(\backend\models\Goods::$sale_options);
echo $form->field($model,"sort")->textInput(["type"=>"number"]);
echo $form->field($introModel,'content')->widget('kucha\ueditor\UEditor',[
    'clientOptions' => [
        //编辑区域大小
        'initialFrameHeight' => '200',
        //设置语言
        'lang' =>'en', //中文为 zh-cn
        //定制菜单
        /*'toolbars' => [
            [
                'fullscreen', 'source', 'undo', 'redo', '|',
                'fontsize',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat',
                'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|',
                'forecolor', 'backcolor', '|',
                'lineheight', '|',
                'indent', '|'
            ],
        ]*/
    ]
]);
echo $form->field($model,"status",["inline"=>true])->radioList(\backend\models\Goods::getStatusOptions());
echo \yii\bootstrap\Html::submitButton("提交",["class"=>"btn btn-info"]);
\yii\bootstrap\ActiveForm::end();

/*
//调用视图的方法加载静态资源
//加载css文件
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
//加载js文件
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
//加载js代码

$nodes = \yii\helpers\Json::encode(\backend\models\GoodsCategory::getZtreeNodes());
$nodeId = $model->goods_category_id;
//$categories []=["id"=>0,"name"=>"顶级分类","parent_id"=>0,"open"=>true];
//$nodes=\yii\helpers\Json::encode($categories);
//$nodeId=0;
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback: {
		        onClick: function(event, treeId, treeNode){
		            //console.log(treeNode.id);
		            //将当期选中的分类的id，赋值给parent_id隐藏域
		            $("#goods-goods_category_id").val(treeNode.id);
		        }
	        }
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$nodes};
  
        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        zTreeObj.expandAll(true);//展开全部节点
        
        //获取节点
        var node = zTreeObj.getNodeByParam("id", "{$nodeId}", null);
        //选中节点
        zTreeObj.selectNode(node);
        //触发选中事件
        

JS

));

*/