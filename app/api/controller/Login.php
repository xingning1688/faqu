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

namespace app\api\controller;

use app\api\model\DouYinModel;
use app\api\model\Jwt;
use think\admin\Controller;
use think\facade\Db;
use app\api\model\LawyerInformations;
use app\api\model\KuaiShouModel;
use app\api\model\WeiXinModel;
use app\api\model\PlatformUser;

/**
 * Class Index
 * @package app\index\controller
 */
class Login  extends Controller
{

    public function code2Session(){
        $code = request()->post('code');
        $platform = request()->post('platform',0);
        if(empty($code)){
            $this->error('参数错误');
        }
        if(empty($platform) && !in_array($platform,[1,2,3])){
            $this->error('参数错误2');
        }

        if($platform == 1){
            $KuaiShouModel = new KuaiShouModel();
            $data = $KuaiShouModel->code2Session($code);
        }else if($platform == 2){
            $WeiXinModel = new WeiXinModel();
            $data = $WeiXinModel->code2Session($code);
        }else if($platform == 3){
            $WeiXinModel = new DouYinModel();
            $data = $WeiXinModel->code2Session($code);
        }

        if($data === false){
            $this->error('失败');
        }

        $this->success('ok',$data);
    }

    //登录
    public function login(){

        $platform = request()->post('platform');
        $session_key = request()->post('session_key');

        $user_rawData = request()->post('user_rawData');
        $user_signature = request()->post('user_signature');
        $user_encryptedData = request()->post('user_encryptedData');
        $user_iv = request()->post('user_iv');

        $phone_encryptedData = request()->post('phone_encryptedData','');
        $phone_iv = request()->post('phone_iv','');

        $data['source_url'] = request()->post('source_url','');
        $data['source_url_name'] = request()->post('source_url_name','');
        $data['source_lawyer_id'] = request()->post('source_lawyer_id',0);
        if(!is_numeric($data['source_lawyer_id'])){
            $this->error('数据来源不合法');
        }

        //file_put_contents('./test.txt', '解析前1：'.var_export($user_rawData,true)."\r\n",FILE_APPEND | LOCK_EX);
        //file_put_contents('./test.txt', '解析前2：'.var_export($user_encryptedData,true)."\r\n",FILE_APPEND | LOCK_EX);
        //test
       /* $platform = 1;
        $session_key = '44Rszv88ezkOFV5r1hWpDQ==';

        $user_rawData = '{"gender":1,"avatarUrl":"https://p2.a.yximgs.com/uhead/AB/2019/10/21/16/BMjAxOTEwMjExNjEzMDdfMTU0MzI0MDc2MF8xX2hkNjcwXzg1Mg==_s.jpg","nickName":"Dexterxxx"}';
        $user_signature = 'd39e116f975fac7d4e20e310e7cfbe4250415e18';
        $user_encryptedData = 'VzRruwO8E9CKyDqbK081sKIC0JLwHA6gTbvU7ixWeOpBusuM+cTYTaLZwl6w7riv85FREvHcSS4qpPnTHWdp2eCTSaJt0BG05Qt0uCgZ3L3XiTCAxa9H5uO5wVWle9ZYh96IqvbBoYbNKc776lPJ549uCuzuduxtCzwwAdyl+dD9xPMm+i7LXdhQOvO6ITy0G9GC9SqTcgjR2cFygoSVXFD8xg+oxOqtzMPPxhMMUnhd5NX1pC/+1D5UzOrUksh9XEyrD1bxArtjnV66O53Qhw==';
        $user_iv = '5dsm2O+v/Z8Sxvfr2OexjA==';

        $phone_encryptedData = 'A/i9ZiZqCN2AR45aRXgheW+T4NU06sv2q+gmjhGqJ817LoxJ+sxO4Wx44pbGi3sERvBGK1vj72368jIeKdtxDg==';
        $phone_iv = 'EPi+S4KrgpPVRXgnj3wmwQ==';*/
        //test




        //用户信息验签
        if(sha1($user_rawData.$session_key) !== $user_signature){
            $this->error('用户信息验签失败');
        }

        $KuaiShouModel = new KuaiShouModel();

        $userData = $KuaiShouModel->decryptData($user_encryptedData,$user_iv,$session_key);
        //file_put_contents('./test.txt', '解析后：'.var_export($userData,true)."\r\n",FILE_APPEND | LOCK_EX);
        if($userData === false){
            $this->error('信息获取失败');
        }
        $data = [];
        if(isset($userData['openId']) && !empty($userData['openId'])){
            $data['open_id'] = $userData['openId'];
        }

        if(isset($userData['nickName']) && !empty($userData['nickName'])){
            $data['nick_name'] = $userData['nickName'];
        }

        if(isset($userData['avatarUrl']) && !empty($userData['avatarUrl'])){
            $data['avatar_url'] = $userData['avatarUrl'];
        }

        if(isset($userData['gender']) && !empty($userData['gender'])){
            $data['gender'] = $userData['gender'];
        }

        if(isset($userData['country']) && !empty($userData['country'])){
            $data['country'] = $userData['country'];
        }

        if(isset($userData['province']) && !empty($userData['province'])){
            $data['province'] = $userData['province'];
        }

        if(isset($userData['city']) && !empty($userData['city'])){
            $data['city'] = $userData['city'];
        }

        if(isset($userData['language']) && !empty($userData['language'])){
            $data['language'] = $userData['language'];
        }


        if(empty($session_key) || !isset($data['open_id']) || empty($data['open_id']) || !in_array($platform,[0,1,2,3])){
            $this->error('信息获取失败,数据不合法11');
        }
        $data['platform'] = $platform;

        if(!empty($phone_encryptedData) && !empty($phone_iv)){
            $phoneData = $KuaiShouModel->decryptData($phone_encryptedData,$phone_iv,$session_key);
            if($phoneData === false){
                $this->error('信息获取失败');
            }

            if(isset($phoneData['phoneNumber']) && !empty($phoneData['phoneNumber'])){
                $data['phone'] = $phoneData['phoneNumber'];
            }
        }

        //如果是微信登录
        if($platform == 2 && (!isset($data['nick_name']) || empty($data['nick_name']) || $data['nick_name'] == '微信用户') ){
            $data['nick_name'] = '法趣用户'.getRandNumber();
        }


        //添加登录信息
        $res = PlatformUser::updateOrCreate($data);
        if($res === false){
            $this->error('登录失败');
        }
        //file_put_contents('./test.txt', '添加前'.var_export($data,true)."\r\n",FILE_APPEND | LOCK_EX);
        //登录成功  返回token
        $jwt = new Jwt();
        $result['users']['open_id'] =  $data['open_id'];
        $result['users']['session_key'] = $session_key;
        $result['users']['nick_name'] = isset($data['nick_name'])? $data['nick_name'] : '';
        $result['users']['avatar_url'] = isset($data['avatar_url'])? $data['avatar_url'] : '';
        $result['users']['gender'] = isset($data['gender'])? $data['gender'] : 0;
        $token = $jwt->createToken($result['users']);
        $result['token'] = $token;
        $this->success('登录成功',$result);

    }

    public function test(){
        $data['code'] = 'j05U5jOK-4pcgjAXe2PTrJMWQt-htSSvzVo4LjEdM_s_PPyG4okg33Kq_0G7jk4chBadoQbn4v1DC4MMZ285oQeI0UMIJyrB-UFZxGF6qaa-QB5O80NGfnqIEjM';
        $data['appid'] = 'tt13c3657e8cfd546101';
        $data['secret'] = '56a416ab025c99241b4d48b4df5ad1685ce893c6';
        $res = json_encode($data);
        echo $res;exit;
        $content= [['name'=>'name1','age'=>18],['name'=>'name2','age'=>19]];
        file_put_contents('./test.txt', var_export($content,true)."\r\n",FILE_APPEND | LOCK_EX);
        exit;
        $a = str_pad(mt_rand(1, 99999), 6, '0', STR_PAD_LEFT);
        dump($a);exit;
        $WeiXinModel = new WeiXinModel();
        $data = $WeiXinModel->code2Session('081LW20w3RVDsZ2t200w3d0tsT2LW20X');
        dump($data);exit;
        /*$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOm51bGwsInN1YiI6bnVsbCwiYXVkIjpudWxsLCJleHAiOjE2NjU1NTQ1NTMsIm5iZiI6MTY2NTM4MTc1MywiaWF0IjoxNjY1MzgxNzUzLCJvcGVuX2lkIjoiZjE4ZTYwZDM1N2ExZDFjNDE0YmY1NzI0ODIyZTNjN2EiLCJzZXNzaW9uX2tleSI6IlRLd053bGN1OXpVOTEySU9hbWsyblE9PSIsIm5pY2tfbmFtZSI6IkRleHRlcnh4eCIsImF2YXRhcl91cmwiOiJodHRwczovL3AyLmEueXhpbWdzLmNvbS91aGVhZC9BQi8yMDE5LzEwLzIxLzE2L0JNakF4T1RFd01qRXhOakV6TURkZk1UVTBNekkwTURjMk1GOHhYMmhrTmpjd1h6ZzFNZz09X3MuanBnIn0.noJtDTGze0bOiaiscgwPjzRVwEElS0p9W2Sg0CyhvSc';
        $jwt = new Jwt();
        $res = $jwt->decodeToken($token);
        dump($res);exit;*/
    }

    /*public function getCode()
    {

        file_put_contents("./test.txt", '数据'.var_export(1234,TRUE).PHP_EOL, FILE_APPEND);exit;
        $phone_encryptedData = 'e0OmFCXZOxCxyg6JfOpriHo39vQXWpVgUxGrtsEtIPo3c+I07+MUkeLmGFDPSvAa/DQupTb/DHdB1ahxZ7vqpw==';
        $phone_iv = 'teti6FB79yP1KAxh/aqqVw==';
        $session_key = '44Rszv88ezkOFV5r1hWpDQ==';
        $KuaiShouModel = new KuaiShouModel();
        $phoneData = $KuaiShouModel->decryptData($phone_encryptedData,$phone_iv,$session_key);
        dump($phoneData);exit;
        /*$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOm51bGwsInN1YiI6bnVsbCwiYXVkIjpudWxsLCJleHAiOjE2NjQyNDE2NzcsIm5iZiI6MTY2NDI0MTY0NywiaWF0IjoxNjY0MjQxNjQ3LCJ1c2VyIjp7Im5hbWUiOiJ4aW5nIiwiYWdlIjoxOH19.K_Hi80LayRlsG3fkxbW99kbuVA_SjurptOJUs-zmXXA';
        $jwt = new Jwt();
        $user['user']['name'] = 'xing';
        $user['user']['age'] = 18;
        //$token = $jwt->createToken($user);
        $res = $jwt->decodeToken($token);
        dump($token,$res);*/

       /* $rawData = '{"gender":0,"avatarUrl":"https://p2.a.yximgs.com/uhead/AB/2022/09/19/12/BMjAyMjA5MTkxMjAxMTZfMzAxODgyODY5M18yX2hkNDUzXzc0MA==_s.jpg","nickName":"AAA"}';
        $signature = '9a601a0b95cd1a3fcdd26750d9af5ac436e3028b';
        $encryptedData = '"jpUo4KZ3C8lS9AojNRP/srq/cEOHe0gvEUj0jUlz+ocKgX/4hez08bK4var+NJH3FuX7ZWd4M/vkgkB5g/Y5u1M/LCdDSF0glDkrIF3IR+cnp/5FZOGQHYTKoz5A9/Z/2n2eSGF23oTfRa3fQ4pNE0/YuQQKee9dIAYUcdjDcqnClY4kZPbVQRdONhv35Q/VqxyroG/6XYleiOOoGXF84uDkC3tLXQiYAalO9vnrzBbeFarpCf8JLLqjGyg8YJxhS6+HYg24lR4k9eKK1Nf2TQ=="';
        $iv = '"WcDjnD2H9OwQedDFSqapQA=="';
        $sessionKey = 'UAKpF++yqcF0V3m8jDMx1g==';
        $KuaiShouModel = new KuaiShouModel();
        $deData = $KuaiShouModel->decryptData($encryptedData,$iv,$sessionKey);
        if($deData === false){
            return reponse(false,'信息获取失败','201');
        }*/



    /*}*/






}