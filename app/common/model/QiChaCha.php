<?php
namespace app\common\model;
use app\api\model\LeaveMessages;
use app\api\model\OrderContract;
use app\api\model\PlatformAccess;
class QiChaCha extends BaseModel{


    //public $data = null;
    private const key = '655d2b8df3714ec3958e103d0b6379f7';
    private const   secretKey = '3F488308C9801D3E645F89FDCC9E918A';

    public function __construct() {

    }

    //查询老赖核查
    public function getDeadBeatCheck($postData=[]){
        //test
        //$postData['searchKey'] = '';
        //test

        $data['key'] = self::key;
        if(isset($postData['searchKey']) && !empty($postData['searchKey'])){
            $data['searchKey'] = $postData['searchKey'];
        }

        if(isset($postData['searchNo']) && !empty($postData['searchNo'])){
            $data['searchNo'] = $postData['searchNo'];
        }

        if(isset($postData['pageIndex']) && !empty($postData['pageIndex'])){
            $data['pageIndex'] = $postData['pageIndex'];
        }

        if(isset($postData['pageSize']) && !empty($postData['pageSize'])){
            $data['pageSize'] = $postData['pageSize'];
        }

        $url = 'https://api.qichacha.com/DeadBeatCheck/GetList?'.http_build_query($data);
        $headers = self::getHeaders();
        $res = $this->doCurl($url,0,$data=[],$headers);

        return $res;

    }

    public static function getHeaders(){
        $Timespan = time();
        $token = strtoupper(md5(self::key.$Timespan.self::secretKey));
        $headers = [
            'Content-Type: application/json',
            "Token: $token",
            "Timespan: $Timespan",
        ];
        return $headers;
    }


    public function createOrder($oid,$order_type){


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





