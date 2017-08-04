<?php
namespace frontend\controllers;


use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\helpers\Json;
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

    public function actionOrder(){
        $model = new Order();//新订单

        $model2=Member::findOne(['id'=>\Yii::$app->user->id]);
        $address=Address::find()->where(['=','user_id',$model2->id])->all();

        $cart=Cart::find()->where(['=','member_id',\Yii::$app->user->id])->asArray()->all();//购物车数据

        return $this->render('order',['model'=>$model,'address'=>$address,'cart'=>$cart]);
    }

    //订单页面
    public function actionAjaxOrder()
    {
        if(\Yii::$app->user->isGuest){
            //跳转到登录页面去
            return $this->redirect(['member/login']);
        }else{
            //开启事务
            $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $model = new Order();//新订单
                    $address_id=\Yii::$app->request->post('address');
                    $payment_id=\Yii::$app->request->post('payment');
                    $delivery_id=\Yii::$app->request->post('delivery');

                    $payment=Order::$pay[$payment_id];

                    $address=Address::findOne(["id"=>$address_id]);
//                    $address->name;//得到收货人的名字

                    $delivery = Order::$deliveries[$delivery_id];
//                    $delivery["name"];//得到送货方式


                    $model->member_id = \Yii::$app->user->id;

                    $model->name = $address->name;//得到收货人的名字
                    $model->province = $address->province;
                    $model->city = $address->city;
                    $model->area = $address->area;
                    $model->address = $address->full_address;
                    $model->tel = $address->tel;

                    $model->delivery_id =$delivery_id;
                    $model->delivery_name = $delivery['name'];//得到送货方式
                    $model->delivery_price = $delivery['price'];

                    $model->payment_id = $payment_id;
                    $model->payment_name = $payment['name'];

                    $model->total = $delivery['price'];
                    $model->status = 1;
                    $model->trade_no = time();

                    $model->create_time = time();

                    $model->save(false);

                    //继续保存订单商品表
                    //（检查库存，如果足够）保存订单商品表
                    //检查库存：购物车商品的数量和商品表库存对比，足够
                    $carts=Cart::find()->where(['member_id'=>\Yii::$app->user->id])->all();//获取购物车数据
                    foreach ($carts as $cart) {
                        $goods = Goods::findOne(['id' => $cart->goods_id]);
//                         return Json::encode($goods);exit;
                        $order_goods = new OrderGoods();
                        if ($cart->amount <= $goods->stock) {
                            //$order_goods的其他属性赋值
                            $order_goods->order_id = $model->id;
                            $order_goods->goods_id = $goods->id;
                            $order_goods->goods_name = $goods->name;
                            $order_goods->logo = $goods->logo;
                            $order_goods->price = $goods->shop_price;
                            $order_goods->amount = $cart->amount;
                            $order_goods->total = $order_goods->price*$order_goods->amount;
                            $goods->stock =$goods->stock-$cart->amount;
                            $goods->save(false);

                            $order_goods->save(false);
                            $model->total+=$order_goods->total;
                            //扣减对应商品的库存

                        } else {
                            //（检查库存，如果不够）
                            //抛出异常
                            throw new Exception('商品库存不足，无法继续下单，请修改购物车商品数量');
                        }
                    }
                    $model->save(false);
                    //下单成功后清除购物车
                    $cart->delete();
                    //提交事务
                    $transaction->commit();
                } catch (Exception $e) {
                    //回滚
                    $transaction->rollBack();
                }
            }
    }

    public function actionFormOrder(){
        $frorder = new OrderGoods();
        return $this->render('form-order', ['frorder' => $frorder]);

    }


    public function actionMyOrder(){

        $myorder = new OrderGoods();

        return $this->render('my-order', ['myorder' => $myorder]);

    }




}
