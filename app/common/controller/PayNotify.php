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
    //支付合同异步返回地址
    public function contract() {
        $return = false;
        $xml         = file_get_contents("php://input");
        $array_data  = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        _minipay_log('小程序支付异步通知返回数据：' . json_encode($array_data));
        if(($array_data['return_code']=='SUCCESS') && ($array_data['result_code']=='SUCCESS')){
            $trade_no     = $array_data['transaction_id'];    // 微信支付系统生成的订单编号
            $order_no     = $array_data['out_trade_no'];      // 商城系统中的订单编号
            $total_amount = $array_data['total_fee']/100;     // 收款金额
            $result_code  = $array_data['result_code'];       // 订单支付状态 SUCCESS/FAIL

            /*查询出订单*/
            $dataOrder = Db::name('order_contract')->where(['order_no'=>$order_no,'order_price'=>$total_amount,'pay_status'=>0])->field('id')->find();
            _minipay_log('order_contract 订单数据: '.json_encode($dataOrder));
            if(!empty($dataOrder)) {
                $time = time();
                try {
                    // 修改订单状态
                    $ordersData['transaction_id']             = $trade_no;
                    $ordersData['pay_type']             = 20;//微信支付
                    $ordersData['pay_time']             = $time;
                    $ordersData['update_time']          = $time;
                    if($result_code == 'SUCCESS') {
                        $ordersData['pay_status']           = 1;
                    } else {
                        $ordersData['states']           = '-1';
                    }

                    $res = Db::name('order_contract')->where('id', $dataOrder['id'])->update($ordersData);
                    $return = true;
                } catch (\Exception $e) {
                    _minipay_log('商城系统中的订单编号:'.$order_no.'微信支付系统生成的订单编号:'.$trade_no.'订单修改失败 (失败原因：'.$e->getMessage().')');
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





}