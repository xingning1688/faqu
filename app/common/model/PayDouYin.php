<?php
namespace app\common\model;
class PayDouYin extends BaseModel{
    public $table = 'email_case';

    public $api_url='https://developer.toutiao.com/api/apps/ecpay/v1/';
    public $app_id;
    public $token;
    public $salt;

    public function __construct() {
        $this->app_id = 'tt13c3657e8cfd546101';
        $this->token='UdIYI4Qdj1WkQ8i8nowyS55nvvLbMlac';
        $this->salt='oU5zFBfEpKCHoaNGlbyf2ceSu1GHoqsBlgq9X8Xz';
    }        
        
   /* public function run(){
         $action=addslashes($_GET['ac']);
         $action=$action?$action:'order';
         if(!in_array($action,['order','query','refund','settle','notify','set'])){
            echo '非法请求';die;
        }
        call_user_func(array($this,$action));
    }*/
    
    //合同下单
    public function  order1(){

        $data=[
            'out_order_no'=>$this->order_number(), //商户订单号
            'total_amount'=>1,
            'subject'=>'6666666',
            'body'=>'99999999',
            'valid_time'=>7200,
        ];
        $res=$this->post('create_order',$data);
        echo json_encode($res);die;
    }
    
    //查询订单
    /*public function  query(){
        $data=[
            'out_order_no'=>'2021110117254573565'
        ];
        $res=$this->post('query_order',$data,false);
        echo json_encode($res);die;
    }*/
    
    //订单退款
    /*public function refund(){
        $data=[
            'out_order_no'=>'2021110118351347832',
            'out_refund_no'=>$this->order_number(),
            'reason'=>'退款原因',
            'refund_amount'=>1,
        ];
        $res=$this->post('create_refund',$data);
        echo json_encode($res);die;
    }*/
    
    //订单分账
   /* public function settle(){
        $data=[
            'out_order_no'=>'2021110118301265990',
            'out_settle_no'=>$this->order_number(),
            'settle_desc'=>'分账描述',
            'settle_params'=>json_encode([]),//分润方参数 如[['merchant_uid'=>'商户号','amount'=>'10']]  可以有多个分账商户
        ];
        $res=$this->post('settle',$data);
        echo json_encode($res);die;
    }*/
    
    //支付设置回调测试
    /*public function set(){
        $content=file_get_contents('php://input');
        $this->log('log.txt',$content);
    }*/
    
    //回调
    /*public function notify(){
        $content=file_get_contents('php://input');
        if(empty($content)) return false;
        $this->log('notify.txt',$content);
        $content=json_decode($content,true);
        $sign=$this->handler($content);
        if($sign==$content['msg_signature']){
            $msg=json_decode($content['msg'],true); 
            echo '回调----'.$content['type']."\n";
            //这里更新应用业务逻辑代码，使用$msg跟应用订单比对更新订单,可以用 $content['type']判断是支付回调还是退款回调，payment支付回调 refund退款回调。
            $res=['err_no'=>0,'err_tips'=>'success'];
            echo json_encode($res);
        }
    }*/
    
    
    /**
    * 测试订单号，实际应用根据自己应用实际生成
    * @return string
    */

    public function order_number(){
        return date('YmdHis').rand(10000,99999);
    }
    
     /**
     * 请求小程序平台服务端
     * @param string $url 接口地址
     * @param array $data 参数内容
     * @param boolean $notify 是否有回调
     * @return array
    */

    public function post($method,$data,$notify=true){
        $data['app_id']=$this->app_id;
        if(!empty($notify)){
            //$data['notify_url']='https://tt.csweigou.com/pay.php?ac=notify';//也可以在调用的时候分别设置
            $data['notify_url']='https://test.faquwang.com';//也可以在调用的时候分别设置
        }
        //dump($data);exit;
        $data['sign']=$this->sign($data);
        $url=$this->api_url.$method;
        $res=$this->http('POST',$url,json_encode($data),['Content-Type: application/json'],true);
        dump($res);exit;
        return json_decode($res,true);
    }
    

    /**
     * 回调验签
     * @param array $map 验签参数
     * @return stirng
    */

   /* public function handler($map){
        $rList = array();
        array_push($rList, $this->token);
        foreach($map as $k =>$v) {
            if ( $k == "type" || $k=='msg_signature')
                continue;
            $value = trim(strval($v));
            if ($value == "" || $value == "null")
                continue;
            array_push($rList, $value);
        }
        sort($rList,2);
        return sha1(implode($rList));
    }*/
    
    /**
     * 请求签名
     * @param array $map 请求参数
     * @return stirng
    */

    public function sign($map) {
        $rList = array();
        foreach($map as $k =>$v) {
            if ($k == "other_settle_params" || $k == "app_id" || $k == "sign" || $k == "thirdparty_id")
                continue;
            $value = trim(strval($v));
            $len = strlen($value);
            if ($len > 1 && substr($value, 0,1)=="\"" && substr($value,$len, $len-1)=="\"")
                $value = substr($value,1, $len-1);
            $value = trim($value);
            if ($value == "" || $value == "null")
                continue;
            array_push($rList, $value);
        }
        array_push($rList, $this->salt);
        sort($rList, 2);
        return md5(implode('&', $rList));
    }

     /**
     * 写日志
     * @param string $path 日志路径
     * @param string $content 内容
    */

    public function log($path, $content){
        $file=fopen($path,  "a");
        fwrite($file, date('Y-m-d H:i:s').'-----'.$content."\n");
        fclose($file);
    }
    
    
    
    
    /**
     * 网络请求
     * @param stirng $method 请求模式
     * @param stirng  $url请求网关
     * @param array $params 请求参数
     * @param stirng  $header 自定义头
     * @param boolean  $multi 文件上传
     * @return array
     */

    public function http( $method = 'GET', $url,$params,$header = array(), $multi = false){
    	
        $opts = array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $header
        );
        /* 根据请求类型设置特定参数 */

        switch(strtoupper($method)){
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
    	
        /* 初始化并执行curl请求 */

        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) throw new Exception('请求发生错误：' . $error);
        return  $data;
    }
    
    
}


/*$ttPay=new ttPay();
$ttPay->run();*/



