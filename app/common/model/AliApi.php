<?php
namespace app\common\model;
use app\api\model\LeaveMessages;
use app\api\model\OrderContract;
use app\api\model\PlatformAccess;
class AliApi extends BaseModel{

    public function __construct() {

    }



    //预下单接口  $order_type 订单类型  1 合同；
    public function idNameverify($postData){
        $host = "https://idname.market.alicloudapi.com";
        $path = "/id_name/verify";
        $method = "POST";
        $appcode = "0b800e1506904e85a96b82f280a841f5";

        $postData = http_build_query($postData);
        $url = $host . $path;

        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");

        $res = $this->doCurl($url,1,$postData,$headers);
        $res =  json_decode($res,true);
        if(!isset($res['code']) && $res['code'] != 200){
            return false;
        }
        return $res['data'];
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
        curl_setopt($ch,CURLOPT_HEADER,0);//不输出header 头
        if(!empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        }
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);// curl_exec()执行之后，不直接打印出来，把数据返回为字符串

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





