<?php
namespace app\common\model;
use app\api\model\LeaveMessages;
use app\api\model\OrderContract;
use app\api\model\PlatformAccess;
class PayKs extends BaseModel{
    public $table = 'email_case';

    public $data = null;

    public function __construct() {

    }

    public function getAccessToken($platformAccess){

        $platform['update_time'] = $platformAccess['update_time'] == null ? 0 : $platformAccess['update_time'];

        if( ( (time()-$platformAccess['update_time']) <  $platformAccess['expires_in'] ) &&  !empty($platformAccess['access_token']) ){
            return $platformAccess['access_token'];
        }


        $postData['app_id'] = $platformAccess['app_id'];
        $postData['app_secret'] = $platformAccess['app_secret'];
        $postData['grant_type'] = 'client_credentials';
        $url = "https://open.kuaishou.com/oauth2/access_token";
        $postData = http_build_query($postData);

        $res = $this->doCurl($url,1,$postData);
        $res =  json_decode($res,true);
        if($res['result'] !== 1){
            return false;
        }

        $updatePlatform['id'] = $platformAccess['id'];
        $updatePlatform['access_token'] = $res['access_token'];
        $updatePlatform['expires_in'] = $res['expires_in'];
        $updatePlatform['update_time'] = time();

        $result = PlatformAccess::updatePlatform($updatePlatform);  //dump(11,$res,$res['access_token']);exit;
        if(!$result){
            return false;
        }
        return $res['access_token'];
    }

    //预下单接口  $order_type 订单类型  1 合同；
    public function createOrder($oid,$order_type){
        $platformAccess = PlatformAccess::getPlatform(1);
        if(empty($platformAccess)){
            return '快手平台信息获取失败';
        }

       /* if($order_type==1){*/
            if($order_type == 1){ //合同订单
                $order = OrderContract::getContractById($oid);//获取数据
                $subject = '购买合同';
            }elseif($order_type == 2){//咨询订单
                $order = LeaveMessages::getOrderById($oid);//获取数据
                $order['order_details'] = $order['title'];
                $subject = $order['title'];
            }elseif($order_type == 3){ //服务商城
                $order = Order::getOrderDetailById($oid);//获取数据
                $subject = '购买服务商城';
            }

            if(empty($order)){
                return '获取订单数据失败';
            }

            $access_token = $this->getAccessToken($platformAccess);//获取平台的 access
            if($access_token === false){
                return 'access_token失败';
            }

            $attach = ['pay_type'=>10,'order_type'=>$order_type];//自定义字段
            $notify_url = 'https://'.$_SERVER['SERVER_NAME'].'/common/PayNotify/contractKs';

            $config['app_id'] =  $platformAccess['app_id'];
            $config['access_token'] = $access_token;

            $postData['open_id'] =   isset($order['open_id']) ? $order['open_id'] : '';
            $postData['out_order_no'] =   isset($order['order_no']) ? $order['order_no'] : '';
            $postData['total_amount'] =   intval(strval($order['order_price']*100));//金额
            $postData['subject'] =  isset($subject)? $subject : '';
            $postData['detail'] =  isset($order['order_details']) ? json_encode($order['order_details']) : '';
            $postData['type'] =  3306;
            $postData['expire_time'] =  7200;
            $postData['notify_url'] =  $notify_url;
            $postData['attach'] =  json_encode($attach);
            $postData['sign'] = $this->makeSign($config,$postData,$platformAccess['app_secret']);

            $url = 'https://open.kuaishou.com/openapi/mp/developer/epay/create_order?'.http_build_query($config);
 //file_put_contents('./log/pay_log.txt', '订单号：'.$order['order_no'].'创建支付单成功返回信息：'.var_export($order['order_price'],true)."\r\n",FILE_APPEND | LOCK_EX);
 //file_put_contents('./log/pay_log.txt', '订单号：'.$order['order_no'].'创建支付单成功返回信息：'.var_export($postData['total_amount'],true)."\r\n",FILE_APPEND | LOCK_EX);
            $postData = json_encode($postData);
            $headers = [
                'Content-Type: application/json',
            ];
            $res = $this->doCurl($url,1,$postData,$headers);
            $res =  json_decode($res,true);
            if($res['result'] !== 1){
                //file_put_contents('./log/pay_log.txt', '订单号：'.$order['order_no'].'创建支付单成功返回信息：'.var_export($res,true)."\r\n",FILE_APPEND | LOCK_EX);
                return '快手预付单请求结果返回失败';
            }

        //file_put_contents('./log/pay_log.txt', '订单号：'.$order['order_no'].'创建支付单成功返回信息：'.var_export($res,true)."\r\n",FILE_APPEND | LOCK_EX);
            return $res['order_info'];

       /* }*/

    }


    //发起结算单
    public function settle($orderNo,$order_type,$amount=0){
        $platformAccess = PlatformAccess::getPlatform(1);
        if(empty($platformAccess)){
            return '快手平台信息获取失败';
        }

        $access_token = $this->getAccessToken($platformAccess);//获取平台的 access
        if($access_token === false){
            return 'access_token失败';
        }

        $notify_url = 'https://'.$_SERVER['SERVER_NAME'].'/common/PayNotify/settleNotify';
        $attach = ['order_type'=>$order_type];//自定义字段 订单类型
        $config['app_id'] =  $platformAccess['app_id'];
        $config['access_token'] = $access_token;

        $postData['out_order_no'] =   $orderNo;
        $postData['out_settle_no'] =   $orderNo;
        $postData['reason'] =   '申请结算';
        $postData['notify_url'] =   $notify_url;
        $postData['attach'] =  json_encode($attach);
        $postData['sign'] = $this->makeSign($config,$postData,$platformAccess['app_secret']);

        $url = 'https://open.kuaishou.com/openapi/mp/developer/epay/settle?'.http_build_query($config);

        //file_put_contents('./log/pay_log.txt', '订单号：'.$order['order_no'].'创建支付单成功返回信息：'.var_export($order['order_price'],true)."\r\n",FILE_APPEND | LOCK_EX);
        //file_put_contents('./log/pay_log.txt', '订单号：'.$order['order_no'].'创建支付单成功返回信息：'.var_export($postData['total_amount'],true)."\r\n",FILE_APPEND | LOCK_EX);
        $postData = json_encode($postData);
        $headers = [
            'Content-Type: application/json',
        ];
        $res = $this->doCurl($url,1,$postData,$headers);
        $res =  json_decode($res,true);
        if($res['result'] !== 1){   dump($res);exit;
            //file_put_contents('./log/pay_log.txt', '订单号：'.$order['order_no'].'创建支付单成功返回信息：'.var_export($res,true)."\r\n",FILE_APPEND | LOCK_EX);
            return false;
        }

        //file_put_contents('./log/pay_log.txt', '订单号：'.$order['order_no'].'创建支付单成功返回信息：'.var_export($res,true)."\r\n",FILE_APPEND | LOCK_EX);
        return true;
    }


    public function makeSign($config,$postData,$appSecret){
        unset($config['access_token']);
        $arr = array_merge($config,$postData);
        foreach($arr as $k=>$item){
            if(empty($item)){
                unset($arr[$k]);
            }
        }
        ksort($arr,2);

        $str = '';
        foreach($arr as $k =>$v ){
            $str .= $k . '=' . $v . '&';
        }

        $str = substr($str,0,strlen($str)-1);
        $md5 = $str . $appSecret;
        return md5($md5);

    }



    /*
   $url
   $type 0代表get ; 1代表 post
   $data 代表提交数据
*/
    function doCurl($url,$type=0,$data=[],$headers = []){
        //打开一个会话 (初始化)
        $ch= curl_init();

        //设置curl 传输项
        curl_setopt($ch,CURLOPT_URL,$url);//设置提交网址

        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);// curl_exec()执行之后，不直接打印出来，把数据返回为字符串
        curl_setopt($ch,CURLOPT_HEADER,0);//不输出header 头
        if(!empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        }

        if($type==1){
            curl_setopt($ch,CURLOPT_POST,1);//设置数据提交方式
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);//设置提交数据
        }

        //执行curl 会话,并获取内容
        $output=curl_exec($ch);
        //关闭会话
        curl_close($ch);

        return $output;
    }

    
}





