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

use app\api\model\Jwt;
use think\admin\Controller;
use think\facade\Db;
use app\api\model\LawyerInformations;
use app\api\model\KuaiShouModel;
use app\api\model\LeaveMessages;
use app\api\validate\LeaveMessageValidate;
use app\api\model\Email;




class LeaveMessage  extends AuthController{
//class LeaveMessage  extends Controller{

    public function add(){
        //获取数据
        $open_id = request()->post('open_id');
        $phone = request()->post('phone');
        $wx_num = request()->post('wx_num','');
        $platform = request()->post('platform');
        $problem = request()->post('problem');
        $lawyer_id = request()->post('lawyer_id');
        $title = request()->post('title','');
        //test
        /*$open_id = '1';
        $phone = '13666666666';
        $wx_num = '3';
        $platform = '4';
        $problem = '5';
        $lawyer_id =7;*/
        //test

        //校验提交过来的数据是否合法
        $data['open_id'] = $open_id;
        $data['phone'] = $phone;
        $data['wx_num'] = $wx_num;
        $data['problem'] = $problem;
        $data['platform'] = $platform;
        $data['title'] = $title;
        $data['order_no'] = getOrderNumber();

        if(empty($data['open_id']) || empty($data['phone'])  || empty($data['problem']) || empty($data['platform']) || empty($lawyer_id) /*|| empty($data['title'])*/  /*|| empty($data['wx_num'])*/){
            $this->error('参数不能为空');
        }
        if(!is_numeric($lawyer_id)){
            $this->error('参数不合法');
        }
        $validate = new LeaveMessageValidate();
        $returnVal = $validate->form($data);
        if($returnVal != ''){
            $this->error($returnVal);
        }
        $lawyerInformations = LawyerInformations::getById($lawyer_id);
        if(empty($lawyerInformations)){
            $this->error('参数不合法2');
        }
        $data['lawyer_user_id'] = $lawyerInformations['user_id'];
        $res = LeaveMessages::addData($data);
        if(!$res){
            $this->error('添加失败');
        }

        //发送邮件

        $Email = new Email();
        $title = '收到'.$lawyerInformations['name'].'的快手用户咨询';
        $content = '收到'.$lawyerInformations['name'].'的快手用户咨询，手机号：'.$data['phone'].'，微信：'.$data['wx_num'].'，咨询内容：'.$data['problem'];
        $res = $Email->sendEmailCommon($recipientEmail='faqu2022501@163.com',$title,$content);

        $this->success('留言成功');

    }

    public function addConsulting(){
        //获取数据
        $open_id = request()->post('open_id');
        $phone = request()->post('phone');
        $wx_num = request()->post('wx_num','');
        $platform = request()->post('platform');
        $problem = request()->post('problem');
        $title = request()->post('title');


        //test
        /*$open_id = '2';
        $phone = '13666666666';
        $wx_num = '3';
        $platform = '4';
        $problem = '5';
        $title ='title';*/
        //test

        //校验提交过来的数据是否合法
        $data['open_id'] = $open_id;
        $data['phone'] = $phone;
        $data['wx_num'] = $wx_num;
        $data['problem'] = $problem;
        $data['platform'] = $platform;
        $data['title'] = $title;
        $data['order_no'] = getOrderNumber();
        $data['type'] = 2;


        if(empty($data['open_id']) || empty($data['phone'])  || empty($data['problem']) || empty($data['platform']) || empty($data['title']) ){
            $this->error('参数不能为空');
        }

        $validate = new LeaveMessageValidate();
        $returnVal = $validate->form($data);
        if($returnVal != ''){
            $this->error($returnVal);
        }

        $res = LeaveMessages::addData($data);
        if(!$res){
            $this->error('添加失败');
        }
        $this->success('咨询留言成功');
    }
    /*public function getCode()
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOm51bGwsInN1YiI6bnVsbCwiYXVkIjpudWxsLCJleHAiOjE2NjQyNDE2NzcsIm5iZiI6MTY2NDI0MTY0NywiaWF0IjoxNjY0MjQxNjQ3LCJ1c2VyIjp7Im5hbWUiOiJ4aW5nIiwiYWdlIjoxOH19.K_Hi80LayRlsG3fkxbW99kbuVA_SjurptOJUs-zmXXA';
        $jwt = new Jwt();
        $user['user']['name'] = 'xing';
        $user['user']['age'] = 18;
        //$token = $jwt->createToken($user);
        $res = $jwt->decodeToken($token);
        dump($token,$res);

        return 888;
        $KuaiShouModel = new KuaiShouModel();
        $code = $KuaiShouModel->getCode();
        return $code;
        dump(22,$code);

    }*/






}