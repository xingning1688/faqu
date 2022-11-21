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

namespace app\common\controller;

use think\admin\Controller;
use think\facade\Db;


class PayNotify  extends Controller
{
    //支付合同异步返回地址 微信小程序
    public function contract() {
        $return = false;
        $xml         = file_get_contents("php://input");
        $array_data  = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        _minipay_log('小程序支付异步通知返回数据：' . json_encode($array_data));

        if(($array_data['return_code']=='SUCCESS') ){

            $trade_no     = $array_data['transaction_id'];    // 微信支付系统生成的订单编号
            $order_no     = $array_data['out_trade_no'];      // 商城系统中的订单编号
            $total_amount = $array_data['total_fee']/100;     // 收款金额
            $result_code  = $array_data['result_code'];       // 订单支付状态 SUCCESS/FAIL

            if(isset($array_data['attach'])){
                $attach = json_decode($array_data['attach'],true);
            }

            /*查询出订单*/

            if(isset($attach['order_type']) && $attach['order_type']==1 ){
                $dataOrder = Db::name('order_contract')->where(['order_no'=>$order_no,'order_price'=>$total_amount,'pay_status'=>0])->field('id')->find();
                _minipay_log('order_contract 订单数据: '.json_encode($dataOrder));
                if(!empty($dataOrder)) {
                    $time = time();
                    try {
                        // 修改订单状态
                        $ordersData['transaction_id']             = $trade_no;
                        $ordersData['pay_type']             = isset($attach['pay_type']) ? $attach['pay_type'] : 0;//微信支付
                        $ordersData['pay_time']             = $time;
                        $ordersData['update_time']          = $time;
                        if($result_code == 'SUCCESS') {
                            $ordersData['pay_status']           = 1;
                        } else {
                            $ordersData['pay_status']           = '-1';
                        }

                        $res = Db::name('order_contract')->where('id', $dataOrder['id'])->update($ordersData);
                        $return = true;
                    } catch (\Exception $e) {
                        _minipay_log('商城系统中的订单编号:'.$order_no.'微信支付系统生成的订单编号:'.$trade_no.'订单修改失败 (失败原因：'.$e->getMessage().')');
                    }
                }
            }elseif( isset($attach['order_type']) && $attach['order_type']==2 ){
                $dataOrder = Db::name('leave_message')->where(['order_no'=>$order_no,'order_price'=>$total_amount,'status'=>0])->field('id')->find();
                _minipay_log('leave_message 订单数据: '.json_encode($dataOrder));
                if(!empty($dataOrder)) {
                    $time = time();
                    try {
                        // 修改订单状态
                        $ordersData['transaction_id']             = $trade_no;
                        $ordersData['pay_type']             = isset($attach['pay_type']) ? $attach['pay_type'] : 0;//微信支付
                        $ordersData['pay_time']             = $time;
                        $ordersData['update_time']          = $time;
                        if($result_code == 'SUCCESS') {
                            $ordersData['status']           = 1;
                            $ordersData['pay_status']           = 1;
                        } else {
                            $ordersData['status']           = '-1';
                            $ordersData['pay_status']           = '-1';
                        }

                        $res = Db::name('leave_message')->where('id', $dataOrder['id'])->update($ordersData);
                        $return = true;
                    } catch (\Exception $e) {
                        _minipay_log('leave_message 商城系统中的订单编号:'.$order_no.'微信支付系统生成的订单编号:'.$trade_no.'订单修改失败 (失败原因：'.$e->getMessage().')');
                    }
                }
            }elseif( isset($attach['order_type']) && $attach['order_type']==3 ){
                $dataOrder = Db::name('order')->where(['order_no'=>$order_no,'order_price'=>$total_amount,'status'=>0])->field('id')->find();
                _minipay_log('order 订单数据: '.json_encode($dataOrder));
                if(!empty($dataOrder)) {
                    $time = time();
                    try {
                        // 修改订单状态
                        $ordersData['transaction_id']             = $trade_no;
                        $ordersData['pay_type']             = isset($attach['pay_type']) ? $attach['pay_type'] : 0;//微信支付
                        $ordersData['pay_time']             = $time;
                        $ordersData['update_time']          = $time;
                        if($result_code == 'SUCCESS') {
                            $ordersData['status']           = 1;
                            $ordersData['pay_status']           = 1;
                        } else {
                            $ordersData['status']           = '-1';
                            $ordersData['pay_status']           = '-1';
                        }

                        $res = Db::name('order')->where('id', $dataOrder['id'])->update($ordersData);
                        $return = true;
                    } catch (\Exception $e) {
                        _minipay_log('order 商城系统中的订单编号:'.$order_no.'微信支付系统生成的订单编号:'.$trade_no.'订单修改失败 (失败原因：'.$e->getMessage().')');
                    }
                }
            }


        } else {
            _minipay_log('支付失败 (失败原因：'.$array_data['return_msg'].')');
        }

        echo $this->getWxReturnXml($return);
    }


    public function getWxReturnXml($result = false) {
        $arr['return_code'] = $result ? 'SUCCESS' : 'FAIL';
        $arr['return_msg']  = $result ? 'OK' : 'NO';

        $xml = '<xml>';
        foreach ($arr as $key=>$val) {
            if(is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";

        return $xml;
    }


    //合同订单 异步返回 contractKs
    public function contractKs(){
        $return = false;
        $result = file_get_contents("php://input");
        _minipay_log('快手小程序支付异步通知返回数据：' . $result);
        $result = json_decode($result,true);

        if(($result['data']['status'] == 'SUCCESS') ){

            $kwaisign = isset($_SERVER['HTTP_KWAISIGN'])? $_SERVER['HTTP_KWAISIGN'] : '';
            $appSecret = 'xs9iePnaFdIFG6GBCCw5mw';
            $resulta = json_encode($result);
            $notify = md5($resulta.$appSecret);
            if($notify != $kwaisign){
                _minipay_log('失败-验签失败：快手小程序支付异步通知返回数据：' . $result);
                return false;
            }

            $ks_order_no =  $result['data']['ks_order_no'];         //快手平台订单号
            $trade_no     = $result['data']['trade_no'];            // 用户侧的 交易订单号
            $order_no     = $result['data']['out_order_no'];        // 商城系统中的订单编号
            $total_amount =  $result['data']['order_amount']/100;   // 收款金额

            $pay_type = $result['data']['channel'];
            if($pay_type == 'WECHAT'){
                $pay_type = 11;//快手-微信
            }elseif($pay_type == 'ALIPAY'){
                $pay_type = 12;//快手-支付宝
            }else{
                $pay_type = 10;//快手-未知
            }

            if(isset($result['data']['attach'])){
                $attach = json_decode($result['data']['attach'],true);
            }


            if(isset($attach['order_type']) && $attach['order_type']==1 ){
                $dataOrder = Db::name('order_contract')->where(['order_no'=>$order_no,'order_price'=>$total_amount,'pay_status'=>0])->field('id')->find();
                _minipay_log('order_contract 订单数据: '.json_encode($dataOrder));
                if(!empty($dataOrder)) {
                    $time = time();
                    try {
                        // 修改订单状态
                        $ordersData['platform_order_no']             = $ks_order_no;
                        $ordersData['transaction_id']             = $trade_no;
                        $ordersData['pay_type']             = $pay_type;
                        $ordersData['pay_time']             = $time;
                        $ordersData['update_time']          = $time;
                        if($result['data']['status'] == 'SUCCESS') {
                            $ordersData['pay_status']           = 1;
                        } else {
                            $ordersData['pay_status']           = '-1';
                        }

                        $res = Db::name('order_contract')->where('id', $dataOrder['id'])->update($ordersData);
                        $return =true;
                    } catch (\Exception $e) {
                        _minipay_log('快手平台支付失败-异常：商城系统中的订单编号:'.$order_no.'快手支付系统生成的订单编号:'.$trade_no.'订单修改失败 (失败原因：'.$e->getMessage().')');
                    }
                }
            }elseif(isset($attach['order_type']) && $attach['order_type']==2 ){
                //file_put_contents('./log/pay_log.txt', '订单号：'.$order_no.'支付单成功返回信息：'.var_export($result,true)."\r\n",FILE_APPEND | LOCK_EX);
                $dataOrder = Db::name('leave_message')->where(['order_no'=>$order_no,'order_price'=>$total_amount,'status'=>0])->field('id')->find();
                _minipay_log('leave_message 订单数据: '.json_encode($dataOrder));
                if(!empty($dataOrder)) {
                    $time = time();
                    try {
                        // 修改订单状态
                        $ordersData['platform_order_no']             = $ks_order_no;
                        $ordersData['transaction_id']             = $trade_no;
                        $ordersData['pay_type']             = $pay_type;
                        $ordersData['pay_time']             = $time;
                        $ordersData['update_time']          = $time;
                        if($result['data']['status'] == 'SUCCESS') {
                            $ordersData['status']           = 1;
                            $ordersData['pay_status']           = 1;
                        } else {
                            $ordersData['status']           = '-1';
                            $ordersData['pay_status']           = '-1';
                        }

                        $res = Db::name('leave_message')->where('id', $dataOrder['id'])->update($ordersData);
                        $return =true;
                    } catch (\Exception $e) {
                        _minipay_log('leave_message快手平台支付失败-异常：商城系统中的订单编号:'.$order_no.'快手支付系统生成的订单编号:'.$trade_no.'订单修改失败 (失败原因：'.$e->getMessage().')');
                    }
                }
            }elseif(isset($attach['order_type']) && $attach['order_type']==3 ){
                //file_put_contents('./log/pay_log.txt', '订单号：'.$order_no.'支付单成功返回信息：'.var_export($result,true)."\r\n",FILE_APPEND | LOCK_EX);
                $dataOrder = Db::name('order')->where(['order_no'=>$order_no,'order_price'=>$total_amount,'status'=>0])->field('id')->find();
                _minipay_log('order 订单数据: '.json_encode($dataOrder));
                if(!empty($dataOrder)) {
                    $time = time();
                    try {
                        // 修改订单状态
                        $ordersData['platform_order_no']             = $ks_order_no;
                        $ordersData['transaction_id']             = $trade_no;
                        $ordersData['pay_type']             = $pay_type;
                        $ordersData['pay_time']             = $time;
                        $ordersData['update_time']          = $time;
                        if($result['data']['status'] == 'SUCCESS') {
                            $ordersData['status']           = 1;
                            $ordersData['pay_status']           = 1;
                        } else {
                            $ordersData['status']           = '-1';
                            $ordersData['pay_status']           = '-1';
                        }

                        $res = Db::name('order')->where('id', $dataOrder['id'])->update($ordersData);
                        $return =true;
                    } catch (\Exception $e) {
                        _minipay_log('order快手平台支付失败-异常：商城系统中的订单编号:'.$order_no.'快手支付系统生成的订单编号:'.$trade_no.'订单修改失败 (失败原因：'.$e->getMessage().')');
                    }
                }
            }

        } else {
            _minipay_log('快手平台支付失败 (失败原因状态：'.$result['data']['status'].')');
        }

        echo $this->getKsReturn($return,$result['message_id']);
    }

    public function getKsReturn($result = false,$message_id) {
        $msg['result'] = 0;
        if($result===true){
            $msg['result'] = 1;
        }
        $msg['message_id'] = $message_id;
        return json_encode($msg);
    }





}