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

use think\admin\Controller;
use app\api\model\OrderContract;
use app\common\model\Pay;


class Payment  extends AuthController
//class Payment  extends Controller
{

    //合同发起支付请求
    public function send() {

        //test
        /*$order_no = request()->post('order_no', '20221020-58137');//合同订单编号
        $open_id = request()->post('open_id', 'ol76D5P0G4xbW6LmKvPG-omV551w');//用户open_id
        $order_price =  request()->post('order_price', 9.9);
        $pay_type =  request()->post('pay_type',1);//1是小程序支付*/
        //test

        //获取参数
        $order_no = request()->post('order_no', '');//合同订单编号
        $open_id = request()->post('open_id', '');//用户open_id
        $order_price =  request()->post('order_price', 0.00);
        $pay_type =  request()->post('pay_type',0);



        if(empty($order_no) || empty($open_id) || empty($order_price) || empty($pay_type)){
            $this->error('参数错误1');
        }


        //获取订单
        $order = OrderContract::getOrderContractById($order_no);
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

        $notify_url = $_SERVER['SERVER_NAME'].'/common/PayNotify/contract';
        $pay = new Pay();
        // 组装参数，可以参考官方商户文档
        $options = [
            'body'             => '购买合同-合同名称',//合同名称
            'out_trade_no'     => $order['order_no'],//商户订单号
            'total_fee'        => $order['order_price']*100,
            'openid'           => $order['open_id'],
            'trade_type'       => 'JSAPI',
            'notify_url'       => $notify_url,//异步通知url
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
        ];

        if($pay_type == 20){//小程序支付
            $res = $pay->miniCreateOrder($options);
        }

        if(!is_array($res)){
            $this->error($res);
        }
        $this->success('ok',$res);

    }




    



}