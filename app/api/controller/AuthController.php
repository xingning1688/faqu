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
        //dump('token:'.$token,'open_id:'.$open_id);exit;
        $jwt = new Jwt();
        $token_arr = $jwt->decodeToken($token);
        if($token_arr['code'] === false){
            $this->error($token_arr['msg']);
        }
        if($token_arr['data']['open_id'] != $open_id){
            $this->error('token数据不合法');
        }

    }
    public function auth(){
        $token = request()->param('open_id');

    }
    public function authToken()
    {
        //test
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOm51bGwsInN1YiI6bnVsbCwiYXVkIjpudWxsLCJleHAiOjE2ODEwMDMzNDAsIm5iZiI6MTY2ODA0MzM0MCwiaWF0IjoxNjY4MDQzMzQwLCJvcGVuX2lkIjoib2w3NkQ1RlhyT0hUZkJXRjc2Z21UOThmQW42VSIsInNlc3Npb25fa2V5IjoiSm9wNkFKWjMwUXdtMlBWSlFYTDdaUT09Iiwibmlja19uYW1lIjoiXHU2Y2Q1XHU4ZGEzXHU3NTI4XHU2MjM3MDQxMjkxIiwiYXZhdGFyX3VybCI6Imh0dHBzOlwvXC90aGlyZHd4LnFsb2dvLmNuXC9tbW9wZW5cL3ZpXzMyXC9QT2dFd2g0bUlITzRuaWJIMEtsTUVDTmpqR3hRVXEyNFpFYUdUNHBvQzZpY1JpY2NWR0tTeVh3aWJjUHE0QldtaWFJR3VHMWljd3hhUVg2Z3JDOVZlbVpvSjhyZ1wvMTMyIiwiZ2VuZGVyIjowfQ.uPzZNXcI3KrnmeicmUlnWmFJQZeih_tsH_Ahl8nK_Qk';
        $open_id = 'ol76D5FXrOHTfBWF76gmT98fAn6U';
        //test
        /*$token = request()->param('token','');
        $open_id = request()->param('open_id','');*/
        $jwt = new Jwt();
        $token_arr = $jwt->decodeToken($token);
        if($token_arr['code'] === false){
            $this->error($token_arr['msg']);
        }
        if($token_arr['data']['open_id'] != $open_id){
            $this->error('token数据不合法');
        }

    }



}