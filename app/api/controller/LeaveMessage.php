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
use app\api\model\LawyerConsultation;




class LeaveMessage  extends AuthController{
//class LeaveMessage  extends Controller{

    //此接口即将废弃
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

    //此接口即将废弃
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

    //生成专项咨询支付订单
    public function leaveMessageOrder(){
        //test
        /*$data['open_id'] ='open_id';
        $data['lawyer_user_id'] = 10000;
        $data['platform'] = 1;
        $data['type'] = 2;//专项咨询 2;咨询1
        $data['order_price'] =9;
        $data['title'] = 'title';
        $data['order_no'] = getOrderNumber();
        $data['consultation_id'] = 2;*/
        //test

        $data['open_id'] = request()->post('open_id','');
        $data['platform'] = request()->post('platform',0);
        $data['type'] = request()->post('type',0);//专项咨询 2;咨询1
        $data['title'] = request()->post('title','');
        $data['order_price'] = request()->post('order_price',0.00);
        $data['order_no'] = getOrderNumber();

        if($data['type'] == 1){
            $data['lawyer_user_id'] = request()->post('lawyer_user_id',0);
            $data['consultation_id'] = request()->post('consultation_id',0);
        }

        //验证
        $res = LeaveMessages::orderValidation($data);
        if($res !== true){
            $this->error($res);
        }

        $res = LeaveMessages::buyOrderLeaveMessage($data);
        if($res === false){
            $this->error('创建订单失败');
        }
        $this->success('创建订单成功',$data);

    }



    public function addMessage(){
        //test
        /*$open_id = '2';
        $phone = '13666666666';
        $wx_num = '3';
        $platform = '4';
        $problem = '5';
        $title ='title';*/
        //test

        //获取数据
        $data['order_no'] = request()->post('order_no','');
        $data['open_id'] = request()->post('open_id','');
        $data['wx_num'] = request()->post('wx_num','');
        $data['phone'] = request()->post('phone','');
        $data['problem'] = request()->post('problem','');

        //校验提交过来的数据是否合法
        if(empty($data['open_id']) || empty($data['phone'])  || empty($data['problem']) || empty($data['order_no']) ){
            $this->error('参数不能为空');
        }

        $validate = new LeaveMessageValidate();
        $returnVal = $validate->form($data);
        if($returnVal != ''){
            $this->error($returnVal);
        }

        $res = LeaveMessages::updateMessageData($data);
        if(!$res){
            $this->error('添加失败');
        }

        //发送邮件
        $Email = new Email();
        $title = '收到订单号：'.$data['order_no'].'的咨询';
        $content = '收到订单号'.$data['order_no'].'的咨询，手机号：'.$data['phone'].'，微信：'.$data['wx_num'].'，咨询内容：'.$data['problem'];
        $res = $Email->sendEmailCommon($recipientEmail='faqu2022501@163.com',$title,$content);

        if(!$res){
            file_put_contents('./log/email_log.txt', '发送邮件失败：'.var_export($content,true)."\r\n",FILE_APPEND | LOCK_EX);
        }
        $this->success('咨询留言成功');
    }






}