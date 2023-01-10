<?php
namespace app\common\model;
use app\api\model\LeaveMessages;
use app\api\model\OrderContract;
use app\api\model\PlatformAccess;
class PayWx extends BaseModel{
    public $table = 'email_case';

    public $data = null;

    public function __construct() {

    }

    public function getAccessToken($platformAccess){

        $platform['update_time'] = $platformAccess['update_time'] == null ? 0 : $platformAccess['update_time'];

        if( ( (time()-$platformAccess['update_time']) <  $platformAccess['expires_in'] ) &&  !empty($platformAccess['access_token']) ){
            return $platformAccess['access_token'];
        }


        $postData['appid'] = $platformAccess['app_id'];
        $postData['secret'] = $platformAccess['app_secret'];
        $postData['grant_type'] = 'client_credential';
        $postData = http_build_query($postData);

        $url = "https://api.weixin.qq.com/cgi-bin/token?".$postData;
        //dump($url);exit;

        $res = $this->doCurl($url,0);
        $res =  json_decode($res,true);  //dump($res);exit;
        if(!isset($res['access_token'])){
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

    //发送订阅消息  $data:模板内容
    public function sendMessage($open_id,$template_id,$template_data,$page=''){
        $platformAccess = PlatformAccess::getPlatform(2);  //dump($platformAccess);exit;
        if(empty($platformAccess)){
            return '微信平台信息获取失败';
        }
        $payWx = new PayWx();
        $access_token = $payWx->getAccessToken($platformAccess);//获取平台的 access
        //dump($access_token);exit;
        //$postData['access_token'] = $access_token;
        $postData['template_id'] = $template_id;
        $postData['page'] = $page;
        $postData['touser'] = $open_id;  //接收者（用户）的 openid
        $postData['data'] = $template_data;
        $postData['miniprogram_state'] = 'formal';
        $postData['lang'] = 'zh_CN';

        $url = "https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=".$access_token;
        //dump($url);exit;
        //dump($url,$postData);exit;
        $res = $this->doCurl($url,1,json_encode($postData));
        $res =  json_decode($res,true);
        if($res['errcode'] != 0){
            return false;
        }
        return true;

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





