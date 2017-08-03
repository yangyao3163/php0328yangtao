<?php
namespace frontend\controllers;


use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use frontend\models\Cart;
use yii\web\Controller;
use yii\web\Cookie;

class IndexController extends Controller
{

    public $enableCsrfValidation = false;

    public $layout = false;

    //首页
    public function actionIndex()
    {
        $model = GoodsCategory::find()->where(['=', 'parent_id', 0])->all();
        return $this->render('index', ['model' => $model]);
    }

    //分类
    public function actionList()
    {
        $query = Goods::find();
        if ($id = \Yii::$app->request->get('id')) {
            $id = \Yii::$app->request->get('id');
            $cate = GoodsCategory::findOne(['id' => $id]);
            //判断是否有子类
            if ($cate->rgt - $cate->lft > 1) {
                //查询出子类
                $chilh = GoodsCategory::find()->andWhere(['<', 'lft', $cate->rgt])->andWhere(['>', 'lft', $cate->lft])->andWhere(['=', 'depth', 2])->all();

                foreach ($chilh as $c) {
                    $query->orWhere(['=', 'goods_category_id', $c->id]);
                }
            } else {
                $query->andWhere(['=', 'goods_category_id', $id]);
            }
        }
        $model = $query->all();
        return $this->render('list', ['model' => $model]);
    }

    //详情页
    public function actionGoods($id)
    {
        $goods = Goods::findOne(['id' => $id]);

        $goodsGallery = GoodsGallery::find()->where(['=','goods_id',$goods->id])->all();

        return $this->render('goods', ['goods' => $goods,'goodsGallery'=>$goodsGallery]);
    }

    //添加到购物车页面
    public function actionAddToCart($goods_id, $amount)
    {
        //未登录
        if (\Yii::$app->user->isGuest) {
            //如果没有登录就存放在cookie中
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if ($cart == null) {
                $carts = [$goods_id => $amount];
            } else {
                $carts = unserialize($cart->value);
                if (isset($carts[$goods_id])) {
                    //购物车中已经有该商品，数量累加
                    $carts[$goods_id] += $amount;
                } else {
                    //购物车中没有该商品
                    $carts[$goods_id] = $amount;
                }
            }
            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name' => 'cart',
                'value' => serialize($carts),
                'expire' => 7 * 24 * 3600 + time()
            ]);
            $cookies->add($cookie);
            //var_dump($cookies->get('cart'));
        } else {
            //用户已登录，操作购物车数据表
            $cart = Cart::findOne(["goods_id" => $goods_id]);
//
            if ($cart == null) {
                //不存在重新加一条数据
                $cart = new Cart();
                $cart->goods_id = $goods_id;//商品ID
                $cart->amount = $amount;//商品数量
                $cart->member_id = \Yii::$app->user->id;//对应的用户id
                $cart->save();
//                var_dump(  $cart->save());exit;
            } else {
                $cart->amount = $cart->amount + $amount;//本来的数量+再次加入购物车的数量
                $cart->save();
            }
        }
            return $this->redirect(['index/cart']);
    }


    //购物车页面
    public function actionCart()
    {
        $this->layout = false;
        //1 用户未登录，购物车数据从cookie取出
        if (\Yii::$app->user->isGuest) {
            $cookies = \Yii::$app->request->cookies;
            //var_dump(unserialize($cookies->getValue('cart')));
            $cart = $cookies->get('cart');
            if ($cart == null) {
                $carts = [];
            } else {
                $carts = unserialize($cart->value);
            }
            //获取商品数据
            $models = Goods::find()->where(['in', 'id', array_keys($carts)])->asArray()->all();
        } else {
            //2 用户已登录，购物车数据从数据表取
            $cart=Cart::find()->where(["=",'member_id',\Yii::$app->user->id])->all();
            $carts=[];
            foreach ($cart as $v ){
                $carts[$v->goods_id]=$v->amount;
            }
            $models=Goods::find()->where(["in",'id',array_keys($carts)])->asArray()->all();
        }

        return $this->render('cart', ['models' => $models, 'carts' => $carts]);
    }

    //修改购物车数据
    public function actionAjaxCart()
    {
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
        //数据验证
        if (\Yii::$app->user->isGuest) {
            $cookies = \Yii::$app->request->cookies;
            //获取cookie中的购物车数据
            $cart = $cookies->get('cart');
            if ($cart == null) {
                $carts = [$goods_id => $amount];
            } else {
                $carts = unserialize($cart->value);
                if (isset($carts[$goods_id])) {
                    //购物车中已经有该商品，更新数量
                    $carts[$goods_id] = $amount;
                } else {
                    //购物车中没有该商品
                    $carts[$goods_id] = $amount;
                }
            }
            //将商品id和商品数量写入cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie([
                'name' => 'cart',
                'value' => serialize($carts),
                'expire' => 7 * 24 * 3600 + time()
            ]);
            $cookies->add($cookie);
            return 'success';
        } else {//已经登录
            //得到用户id
            $member_id = \Yii::$app->user->identity->id;
            //var_dump($goods_id);exit;
            $model = Cart::find()
                ->andWhere(['member_id' => $member_id])
                ->andWhere(['goods_id' => $goods_id])
                ->one();
            //var_dump($model);exit;
            $model->amount = $amount;
            $model->save();
            return 'success';
        }
    }

    //删除功能
    public function actionDelCart($id){
        if(\Yii::$app->user->isGuest){//没登录
            //先取出cookie中的购物车商品
            $cookies=\Yii::$app->request->cookies;//(读取信息request里面的cookie）
            $carts=unserialize($cookies->get('cart'));
            //var_dump($carts);exit;
            unset($carts[$id]);
            $cookies=\Yii::$app->response->cookies;
            //实例化cookie
            $cookie=new Cookie([
                'name'=>'cart',//cookie名
                'value'=>serialize($carts) ,//cookie值
                'expire'=>7*24*3600+time(),//设置过期时间
            ]);
            $cookies->add($cookie);//将数据保存到cookie

        }else{//已经登录
            $member_id=\Yii::$app->user->identity->id;
            $model =Cart::find()
                ->andWhere(['member_id'=>$member_id])
                ->andWhere(['goods_id'=>$id])
                ->one();
            $model->delete();
        }
        //删除成功，跳转到购物车页面
        return $this->redirect(['index/cart']);
    }
}
