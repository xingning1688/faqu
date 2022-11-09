<?php
/**
 * 公共 Model
 * User: suli
 * Date: 2020/12/24
 * Time: 12:16
 */
namespace app\common\model;

use AliPay\App;
use app\common\model\BaseModel;
use WeChat\Exceptions\InvalidResponseException;

class Pay extends BaseModel {
    protected $table = 'email_case';
    // 小程序支付配置信息
    public function _mimiConfig() {
        $config = [
            'token'          => '',
            'appid'          => 'wxf2fcfff84cdec127',
            'appsecret'      => '6a2596ec9af2a3782c8473b9706de455',
            'encodingaeskey' => '',
            // 配置商户支付参数（可选，在使用支付功能时需要）
            'mch_id'         => '1632244949',
            'mch_key'        => 'UdIYI4Qdj1WkQ8i8nowyS55nvvLbMlac',
            // 配置商户支付双向证书目录（可选 p12 | key,cert 二选一，两者都配置时p12优先，在使用退款|打款|红包时需要）
            'ssl_p12'        => '',
            // 'ssl_key'        => \WeChat\Contracts\Tools::pushFile(md5(sysconf('wechat_mch_key_text')) . '.pem', sysconf('wechat_mch_key_text')),
            // 'ssl_cer'        => \WeChat\Contracts\Tools::pushFile(md5(sysconf('wechat_mch_cert_text')) . '.pem', sysconf('wechat_mch_cert_text')),
            // 缓存目录配置（可选，需拥有读写权限）
            'cache_path'     => '',
        ];
        return $config;
    }


    /**
     * notes : 小程序统一下单
     *
     *
     * @param $options
     * @return array|false
     */
    public  function miniCreateOrder($options) {


        try {
            include_once "./../vendor/zoujingli/wechat-developer/include.php";
            $config = $this->_mimiConfig();
            // 创建接口实例
            $pay = new \WeChat\Pay($config);

            // 统一下单
            $result = $pay->createOrder($options); //dump($result);exit;

            if($result['return_code']=='SUCCESS' && $result['result_code']=='SUCCESS') {
                // 创建JSAPI参数签名
                $options = $pay->createParamsForJsApi($result['prepay_id']);
            } else {
                throw new InvalidResponseException("参数签名生成失败！");
            }
        } catch (\Exception $e){
            return $e->getMessage();
        }
        return $options;
    }




}