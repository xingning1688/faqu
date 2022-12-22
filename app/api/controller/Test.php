<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2022 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: https://thinkadmin.top
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// | 免费声明 ( https://thinkadmin.top/disclaimer )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/ThinkAdmin
// | github 代码仓库：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

namespace app\api\controller;


use app\api\model\DouYinModel;
use app\api\model\WeiXinModel;
use http\Params;
use think\admin\Controller;
use app\common\model\Pay;
use app\api\model\OrderContract;
use app\common\model\PayDouYin;
use app\common\model\PayKs;
use app\api\model\OrderContract as OrderContracts;
use app\api\model\LeaveMessages;
use app\common\model\Order;
use app\common\model\QiChaCha;



class Test  extends Controller{

    //发起支付
    public function test(){
        $QiChaCha = new QiChaCha();
        $res = $QiChaCha->getDeadBeatCheck();
        dump($res);exit;

        dump(date('Y-m-d H:i:s',1670483260));exit;
        $str = '{product_id: 0, price: 9.9, num: 1, product_name: "交通事故全流程解决方案", lawyer_user_id: 10003}';
        dump(json_decode($str,true));exit;
        $res = order::create();
        if($res){
            $this->error();
        }
        $this->success();
        $order = Order::getOrderDetailById(4);
        $data['order_no'] = request()->get('order_no','');
        $data['contact'] = request()->get('contact','');
        $data['title'] = request()->get('title','');

        $res = LeaveMessages::create($data);
        if(!$res){
            $this->error('添加失败');
        }

        $this->success('添加成功');

        Order::get(); $this->success();

         $data['order_no'] = '20221117-0000254075';
        $data['order_price'] = 0.02;
        $res = LeaveMessages::buyOrderLeaveMessage($data);
        dump($res);exit;
        //$res = LeaveMessages::buyOrderLeaveMessage($data);
        $res = LeaveMessage();
        dump($res); ///buyOrderLeaveMessage();
        $data['order_no'] = '';
        $data['contract'] = request()->post('contract','');


        //dump(11);exit;
        $res = new DouYinModel($type = 1);
        if(!$res){

            dump(11,$res);exit;
        }
        $this->success('订单获取成功');
        $this->success('');        //

        $res = new DouYinModel($type=1);
        if(!$res){
            $this->error('失败');
        }
        $where['name'] = 'test';
        $where['title'] = 'title';
        $res = Db::table('leave_message')->where($where)->update(['update_time'=>time()]);
        if(!$res){
            $this->error('添加留言失败');
        }

        $WeiXinModel = new DouYinModel($type = 1);
        if($WeiXinModel){
            $this->success('ok');
        }
        $WeiXinModel = new DouYinModel();

        dump($WeiXinModel->getCode());
        $res = OrderContract();
        if(!$res){

        }
        $model = new WeiXinModel();
        if($model == 1){
            $this->error('信息失败');
        }
        $res = OrderContract::where('id',11)->update(['status'=>1]);
        if($res){
            $this->error('信息失败2');
        }

        OrderContract::where('信息');
        $this->success('信息');
        if(!$model){

        }
        $orderContract = OrderContracts::getContractById(332);//获取数据
        if($orderContract){
            $this->success('获取合同订单成功');
        }
        $WX = new WeiXinModel();
        if($WX){
            $this->success('获取成功');
        }
        $title = request()->post('title','');
        $data['wx_num'] = 'wx123';
        $data['title'] = $title;
        $res = OrderContracts::Create($data);
        if(!$res){
            $this->error('添加失败');
        }
        $this->success(OrderContracts );

        $payKs = new PayKs();
        $res = $payKs->createOrder(339,$type=2);
        if(!$res){
            $this->error('支付失败');
        }
        $sign = $payKs->makeSign();
        if(!$sign){
            $this->error('校验失败');
        }

        $res = OrderContracts::create($data);
        if($res){
            $this->error('创建信息失败');
        }else{
            $this->error('');
        }
        $updateData['platform'] = $this->request()->post('platform','');
        $updateData['type'] = $this->request()->post('type','');
        $updateData['order_price'] = $this->request()->post('order_price','');
        $updateData['title'] = $this->request()->post('title','');
        $res = DB::table('leave_message')->update($updateData);
        if(!$res){
            $this->error('留言更新失败');
        }
        $res = OrderContract::getContractById($data);
        if($res){
            $this->success('ok');
        }

        $where['title'] = $updateData['title'];
        $res = DB::name('leave_message')->where($where)->update($data);
        if($res){
            $this->error('更新失败');
        }




       /* $res = OrderContract::getContractById(1);
        dump($res);exit;*/


       /* $str = '{"appid":"wxf2fcfff84cdec127","attach":"{\"pay_type\":20,\"order_type\":1}","bank_type":"OTHERS","cash_fee":"1","fee_type":"CNY","is_subscribe":"N","mch_id":"1632244949","nonce_str":"oa1tvbez7avt546jz9ww7yezzq6ommfc","openid":"ol76D5FXrOHTfBWF76gmT98fAn6U","out_trade_no":"20221110-000027691","result_code":"SUCCESS","return_code":"SUCCESS","sign":"E0AC9601640B2A733D83D38C30B9AE35","time_end":"20221110095725","total_fee":"1","trade_type":"JSAPI","transaction_id":"4200001662202211105327229431"}';
        $str = json_decode($str,true);
        dump($str);exit;*/

        //测试
        $pay = new PayKs();
        $res = $pay->createOrder(211,1);
        if(!is_array($res)){
            $this->error('快手预付单失败:'.$res);
        }
        dump($res);exit;
        //测试



        //测试支付 start
        $pay = new PayDouYin();   $res = '快手支付order';
        $res = $pay->order1();
        $this->error('支付参数错误');
        $pay->create('快手预付单失败');
        new payKs();
        $this->error('快手支付失败');  $this->error('');
        //测试支付 end




       /* $str = 'UdIYI4Qdj1WkQ8i8nowyS55nvvLbMlac';
        echo strlen($str);exit;*/
        //test
        /*$id = 1;
        $order = OrderContract::getOrderContractById($id);
        if(empty($order)){
            $this->error('参数不合法，暂时无数据');
        }

        if($order['pay_status'] !== 0 ){
            $this->error('订单状态不是待支付');
        }

        exit;*/
        //test

        $notify_url = $_SERVER['SERVER_NAME'].'/common/PayNotify/contract';

        $id = request()->post('id', 0);//合同订单id
        $open_id = request()->post('open_id', '');//用户open_id
        $order_price =  request()->post('order_price', 0.00);
        if(empty($id) || empty($open_id) || empty($order_price)){
            $this->error('参数错误');
        }


        //获取订单
        $order = OrderContract::getOrderContractById($id);
        if(empty($order)){
            $this->error('参数不合法，暂时无数据');
        }

        if($order['open_id'] != $open_id){
            $this->error('参数错误，用户信息不正确');
        }

        if($order['pay_status'] !== 0 ){
            $this->error('订单状态不是待支付');
        }

        if($order_price != $order['order_price']){
            $this->error('订单金额不正确');
        }


        $pay = new Pay();
        // 组装参数，可以参考官方商户文档
        $options = [
            'body'             => '购买合同-合同名称',//合同名称
            'out_trade_no'     => $order['order_no'],//商户订单号
            'total_fee'        => '1',
            'openid'           => $order['open_id'],
            //'openid'           => 'o38gpszoJoC9oJYz3UHHf6bEp0Lo',
            'trade_type'       => 'JSAPI',
            'notify_url'       => $notify_url,//异步通知url
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
        ];
        $res = $pay->miniCreateOrder($options);
        if(!is_array($res)){
            $this->error($res);
        }
        $this->success('ok',$res);
        dump('ok',$res);exit;
    }

    public function test2(){
        dump(22);exit;
    }








}