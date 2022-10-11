<?php
/**
 * 公共 Model
 * User: suli
 * Date: 2020/12/24
 * Time: 12:16
 */
namespace app\api\model;

use think\Model;


class Jwt extends Model {
    public  $secretKey = 'ljtz666';

    /**
     *  jwt签发者
     */
    public $iss = null;

    /**
     *  jwt所面向的用户
     */
    public $sub = null;

    /**
     *  接收jwt的一方
     */
    public $aud = null;

    /**
     *  jwt的过期时间，过期时间必须要大于签发时间
     */
    //public $exp = 172800;//两天
    public $exp = 12960000;//两天
    //public $exp = 20;

    /**
     *  nbf: 定义在什么时间之前，某个时间点后才能访问
     */
    public $nbf = 0;

    /**
     *  jwt的签发时间
     */
    public $iat = null;

    public function __construct()
    {
        //判断配置文件是否配置jwt设置
        $this->secretKey = $this->secretKey ; //秘钥
        $expireTime = $this->exp; //多久过期时间（单位：秒）
        $nbf = $this->nbf; //多久生效（单位：秒）
        $time = time();
        $this->iat = $time; //下发时间
        $this->exp = $time + $expireTime; //过期时间戳
        $this->nbf = $time + $nbf; //签发token后，多久之后才生效（默认：签发就生效）

        $this->iss = $this->iss ;
        $this->sub = $this->sub ;
        $this->aud = $this->aud;
    }

    /**创建
     * @param $extraData
     * @return string
     */
    public  function createToken($extraData)
    {
        $arr = [
            'iss'      =>  $this->iss,
            'sub'      =>  $this->sub,
            'aud'      =>  $this->aud,
            'exp'      =>  $this->exp,
            'nbf'      =>  $this->nbf,
            'iat'      =>  $this->iat,
        ];
        //额外的信息（存用户id等）

        $payload = array_merge($arr,$extraData);
        //创建token
        $jwt = \Firebase\JWT\JWT::encode($payload,$this->secretKey,'HS256');
        return $jwt;
    }

    /**解析token
     * @param $token
     * @return array
     */
    public  function decodeToken($token)
    {
        $returnData = [
            'code'  =>  false,
            'msg'   =>  '',
            'data'  =>  []
        ];
        //异常处理，判断token是否过期或者伪造
        try {
            \Firebase\JWT\JWT::$leeway = 60;//当前时间减去60，把时间留点余地
            //file_put_contents('./test.txt', '日期：'.date('Y-m-d H:i:s',time()).'token:'.$token.'||secretKey:'.$this->secretKey.'', FILE_APPEND );
            $info = \Firebase\JWT\JWT::decode($token, $this->secretKey, ['HS256']);
            $returnData = [
                'code'  =>  true,
                'msg'  =>  'token验证成功',
                'data'  =>  json_decode(json_encode($info), true) //转化为数组
            ];
        } catch (\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
            $returnData['msg'] = 'token签名错误';
        } catch (\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
            $returnData['msg'] = 'token签名未到时间点';
        } catch (\Firebase\JWT\ExpiredException $e) {  // token过期()
            $returnData['msg'] = 'token过期';
        } catch (\Exception $e) {  //其他错误
            //file_put_contents('./test.txt', '日期：'.date('Y-m-d H:i:s',time()).'msg:'.$e->getMessage(), FILE_APPEND );
            $returnData['msg'] = 'token验证失败，请重试';
        }
        return $returnData;
    }




}