<?php
namespace app\api\controller;

use app\api\model\Jwt;
use think\admin\Controller;



class AuthController  extends Controller
{
    public function __construct(){
        //return true;
        $token = request()->param('token','');
        $open_id = request()->param('open_id','');

        $jwt = new Jwt();
        $token_arr = $jwt->decodeToken($token);
        if($token_arr['code'] === false){
            $this->error($token_arr['msg']);
        }
        if($token_arr['data']['open_id'] != $open_id){
            $this->error('token数据不合法');
        }
        return true;
    }
    public function auth(){
        $token = request()->param('open_id');

    }
    /*public function authToken()
    {
        $token = request()->param('token','');
        $open_id = request()->param('open_id','');
        $jwt = new Jwt();
        $token_arr = $jwt->decodeToken($token);
        if($token_arr['code'] === false){
            return reponse(false,$token_arr['msg'] ,'201');
        }
        if($token_arr['data']['open_id'] != $open_id){
            return reponse(false,'token数据不合法' ,'201');
        }
        return true;
    }*/



}