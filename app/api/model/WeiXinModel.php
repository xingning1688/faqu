<?php
/**
 * 公共 Model
 * User: suli
 * Date: 2020/12/24
 * Time: 12:16
 */
namespace app\api\model;

use app\business\controller\Base;
use think\Model;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class WeiXinModel extends BaseModel {
    private $appid = 'wxf2fcfff84cdec127';
    private $appsecret = '6a2596ec9af2a3782c8473b9706de455';


    /**
     * 构造函数
     * @param $sessionKey string 用户在小程序登录后获取的会话密钥
     * @param $appid string 小程序的appid
     */
    public function __construct(){

    }
    /*public function __construct( $appid='', $sessionKey='',$appsecret='')
    {
        dump($this->appid);
        $this->sessionKey = $sessionKey;
        $this->appid = $appid;
        $this->appsecret = $appsecret;

    }*/


    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData( $encryptedData, $iv, $sessionKey )
    {
        /*if (strlen($this->appid) != 20) {
            return 'appid 不正确';
        }

        if (strlen($this->sessionKey) != 22) {
            return 'sessionKey 不正确';
        }*/

        $aesKey=base64_decode($sessionKey);


        /*if (strlen($iv) != 24) {
            return 'iv 不正确';
        }*/
        $aesIV=base64_decode($iv);

        $aesCipher=base64_decode($encryptedData);

        $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $data=json_decode( $result,true );
        if(empty($data)){
            return false;
        }

       return $data;
    }

    public function code2Session($code){
        $data['js_code'] = $code;
        $data['appid'] = $this->appid;
        $data['secret'] = $this->appsecret;
        $data['grant_type'] = 'authorization_code';
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $headers = [
            'Authorization: someAuth',
            'x-api-key: someKey',
            'Content-Type: application/x-www-form-urlencoded',
        ];

        $data = http_build_query($data);//application/x-www-form-urlencoded  设置成这个时，必须转化
        $url = $url.'?'.$data;
        $res = $this->doCurl($url,0,[],$headers);
        $res = json_decode($res,true);

        if(isset($res['errcode']) && $res['errcode'] !== 0){  //dump($res);exit;
            return false;
        }
        return $res;

    }

    public function getCode(){

        $code = request()->post('code');
        return $code;
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