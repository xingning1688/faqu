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
use app\api\model\LeaveMessages;
use app\api\model\PlatformUser;
use app\api\validate\Feedback as FeedbackValidate;
use app\common\model\DySmsApiModel;
use app\common\model\PayWx;
use think\admin\Controller;
use think\facade\Db;
use app\api\model\Feedback as FeedbackModel;
use app\api\controller\Common as CommonController;
use app\api\model\LawyerCase;
use app\api\model\Banner;
use app\common\model\Order;
use app\common\model\LawyerInformation;
use app\common\model\LawyerBillService as LawyerBillServiceModel;
use app\common\model\Common;
use app\common\model\LeaveMessage;
use app\common\model\CaseSourceSquare as CaseSourceSquareModel;


class CaseSourceSquare  extends AuthController
//class CaseSourceSquare  extends Controller
{
    //用户添加案源广场
    public function addCaseSourceSquare(){

        /*$data['open_id'] = request()->post('open_id','open_id');
        $data['lawyer_information_id'] = request()->post('lawyer_information_id',1);
        $data['platform'] = request()->post('platform',1);
        $data['problem'] = request()->post('problem','problem');
        $data['name'] = request()->post('name','name');
        $data['phone'] = request()->post('phone','1366666666');
        $data['province'] = request()->post('province','province');
        $data['city'] = request()->post('city','city');*/

        $data['open_id'] = request()->post('open_id','');
        $data['lawyer_information_id'] = request()->post('lawyer_information_id',0);
        $data['platform'] = request()->post('platform',0);
        $data['problem'] = request()->post('problem','');
        $data['name'] = request()->post('name','');
        $data['phone'] = request()->post('phone','');
        $data['province'] = request()->post('province','');
        $data['city'] = request()->post('city','');
        $data['area'] = request()->post('area','');
        $data['province2'] = request()->post('province2','');
        $data['city2'] = request()->post('city2','');
        $data['area2'] = request()->post('area2','');

        $data['shelves_time'] =date('Y-m-d H:i:s',time());  
        $data['status'] = 1;

        $res = $this->check($data);
        if($res!==true){
            $this->error('验证错误');
        }

        $res = CaseSourceSquareModel::addData($data);
        if($res === false){
            $this->error('验证错误');
        }
        $res = DySmsApiModel::sendSms($data['lawyer_information_id']);
        $this->success('问题留言成功');
    }

    public function check($data){
        if(empty($data['open_id'])){
            $this->error('请先授权登录');
        }

        if(empty($data['lawyer_information_id'])){
            $this->error('律师参数错误');
        }

        if(empty($data['platform'])){
            $this->error('平台参数错误');
        }

        if(empty($data['problem'])){
            $this->error('问题不能为空');
        }

        if(empty($data['name'])){
            $this->error('姓名不能为空');
        }

        if(empty($data['province'])){
            $this->error('省份不能为空');
        }

        if(empty($data['city'])){
            $this->error('城市不能为空');
        }

        if(empty($data['area'])){
            $this->error('区不能为空');
        }

        if(empty($data['province2'])){
            $this->error('被告人省份不能为空');
        }

        if(empty($data['city2'])){
            $this->error('被告人城市不能为空');
        }

        if(empty($data['area2'])){
            $this->error('被告人区不能为空');
        }

        if(empty($data['phone'])){
            $this->error('手机号不能为空');
        }

        if(!preg_match("/^1[3456789]\d{9}$/",$data['phone'])){
            $this->error('手机号不合法');
        }

        return true;
    }

    //线索广场
    public function  list(){
        $list_data = CaseSourceSquareModel::getList();
        $this->success('ok',$list_data);

    }

    //律师接收线索  lawyer_information_id  status=1
    public function receivingSource(){

        $data['open_id'] = request()->post('open_id',''); //律师的open_id
        $data['id'] = request()->post('id',0); //案源id
        $data['type'] = request()->post('type',0); //案源id  案源广场接收案源 1 ；我的待接收列表 接收案源 2
        if(empty($data['type'])){
            $this->error('参数错误：type');
        }

        $userIdentity = Common::getUserIdentity();
        if($userIdentity['code'] == 0){
            $this->error($userIdentity['msg']);
        }

        if(($userIdentity['code'] == 1) && ($userIdentity['user_identity'] == 0) ){
            $this->success('普通用户，不能接收案源',$userIdentity);
        }

        $LawyerInformation = $userIdentity['lawyerInformation'];

        $caseData['id'] = $data['id'];
        $caseData['allocate_time'] = date('Y-m-d H:i:s',time());
        $caseData['status'] = 2;
        if($data['type'] == 1){
            $num = CaseSourceSquareModel::where('lawyer_information_id',$LawyerInformation['id'])->where('is_shelves',0)->where('status',2)->count();
            if($num>=1){
                
                //test
                //file_put_contents('./log/test_log.txt', 'LawyerInformation_id：'.$LawyerInformation['id']."\r\n",FILE_APPEND | LOCK_EX);
                //$num = CaseSourceSquareModel::where('lawyer_information_id',$LawyerInformation['id'])->where('is_shelves',0)->where('status',1)->select();
                //test
                $this->error('你有未处理的案源，请处理完成再接收新的案源');
            }

            $num = CaseSourceSquareModel::where('lawyer_information_id',$LawyerInformation['id'])->whereTime('finish_time','today')->where('status',3)->count();
            if($num>=5){
                $this->error('今天已到接收案源上限，请明天再接收新的案源');
            }


            $caseData['lawyer_information_id'] = $LawyerInformation['id'];
        }

        $res = CaseSourceSquareModel::receivingSource($caseData);
        if($res === false){
            $this->error('接收案源失败');
        }

        $this->success('接收案源成功');
    }

    //我的线索-律师端
     public function lawyerCaseSourceSquare(){
         $data['open_id'] = request()->post('open_id',''); //律师的open_id
         $userIdentity = Common::getUserIdentity();
         if($userIdentity['code'] == 0){
             $this->error($userIdentity['msg']);
         }

         if(($userIdentity['code'] == 1) && ($userIdentity['user_identity'] == 0) ){
             $this->success('普通用户，不能接收案源',$userIdentity);
         }

         $LawyerInformation = $userIdentity['lawyerInformation'];
         $where['lawyer_information_id'] = $LawyerInformation['id'];
         $list_data = CaseSourceSquareModel::getList($where);
         $this->success('ok',$list_data);
     }

     //问题未解决 打回案源广场；问题已解决 都要记录
     public function  unsolved(){
        //test
         /*$data['lawyer_information_id'] = request()->post('lawyer_information_id','1');
         $data['case_source_square_id'] = request()->post('id','3'); //案源id
         $data['description'] = request()->post('description','未解决');
         $data['type'] = 1; //律师操作
         $data['status'] = 0; //为解决*/
         //test

         $data['lawyer_information_id'] = request()->post('lawyer_information_id','');
         $data['case_source_square_id'] = request()->post('id',''); //案源id
         $data['description'] = request()->post('description','');
         $data['type'] = 1; //律师操作
         $data['status'] = 0; //未解决

         if(empty($data['description'])){
             $this->error('描述不能为空');
         }


         $row = CaseSourceSquareModel::where('id',$data['case_source_square_id'])->where('status',2)->find();
         if(empty($row)){
             $this->error('失败，暂无数据');
         }
         $row = $row->toArray();
         if($row['lawyer_information_id'] !=  $data['lawyer_information_id'] ){
             $this->error('失败，参数不合法');
         }

         $res = CaseSourceSquareModel::unsolved($data);
         if($res === false){
             $this->error('失败');
         }
         $this->success('成功');
     }

    //完成服务
    public function  completeService(){
        //test
        /*$data['lawyer_information_id'] = request()->post('lawyer_information_id','1');
        $data['case_source_square_id'] = request()->post('id','3'); //案源id
        $data['description'] = request()->post('description','未解决');
        $data['type'] = 1; //律师操作
        $data['status'] = 0; //为解决*/
        //test

        $data['lawyer_information_id'] = request()->post('lawyer_information_id','');
        $data['case_source_square_id'] = request()->post('id',''); //案源id
        $data['description'] = request()->post('description','');
        $data['type'] = 1; //律师操作
        $data['status'] = 1; //已解决

        if(empty($data['description'])){
            $this->error('描述不能为空');
        }

        $row = CaseSourceSquareModel::where('id',$data['case_source_square_id'])->where('status',2)->find();
        if(empty($row)){
            $this->error('失败，暂无数据');
        }
        $row = $row->toArray();
        if($row['lawyer_information_id'] !=  $data['lawyer_information_id'] ){
            $this->error('失败，参数不合法');
        }

        $res = CaseSourceSquareModel::completeService($data);
        if($res === false){
            $this->error('失败');
        }
        $this->success('成功');
    }


    //获取某个案源详情
    public function getCaseSourceSquare(){
        $id = request()->get('id',0); //案源id
        if(empty($id)){
            $this->error('参数错误');
        }
        $row = CaseSourceSquareModel::getMsgById($id);
        if(empty($row)){
            $this->error('失败，暂无数据');
        }

        $platform_user = PlatformUser::where('open_id',$row['open_id'])->column('open_id,avatar_url','open_id');
        $row['avatar_url'] = isset($platform_user[$row['open_id']]) ? $platform_user[$row['open_id']]['avatar_url'] : '';
        $this->success('成功',$row);
    }


    //前端倒计时 打回广场  （首先是接收的，）
    public function  backSquare(){
        $data['lawyer_information_id'] = request()->post('lawyer_information_id','');
        $data['case_source_square_id'] = request()->post('id',''); //案源id

        $data['description'] = '定时任务超时-前端触发';
        $data['type'] = 0; //定时任务操作前端触发
        $data['status'] = 0; //未解决

        $row = CaseSourceSquareModel::where('id',$data['case_source_square_id'])->where('status',1)->find();
        if(empty($row)){
            $this->error('失败，暂无数据');
        }
        $row = $row->toArray();
        if($row['lawyer_information_id'] !=  $data['lawyer_information_id'] ){
            $this->error('失败，参数不合法');
        }

        $res = CaseSourceSquareModel::unsolved($data);
        if($res === false){
            $this->error('失败');
        }
        $this->success('成功');

    }

    //律师点击完成服务，给用户发送即时通知
    public function sendMessage(){
        $data['case_source_square_id'] = request()->post('id',''); //案源id
        $data['description'] = request()->post('description',''); //律师服务描述
        if(empty($data['description'])){
            $this->error('律师服务不能为空');
        }

        $row = CaseSourceSquareModel::where('id',$data['case_source_square_id'])->where('status',3)->find();
        if(empty($row)){
            $this->error('失败，暂无数据');
        }

        $row = $row->toArray();
        if(empty($row['lawyer_information_id'])){
            $this->error('失败，律师为空');
        }

        if( $row['platform'] !=2 ){
            $this->success('成功');
            //$this->error('失败，非微信平台不发送');
        }



        $lawyerInformation = LawyerInformation::getMsgById($row['lawyer_information_id'],'id,name');

        $payWx = new PayWx();
        $open_id = $row['open_id'];
        $page = 'pages/home/home';
        $template_id = 'WdCV4GzygqpVSTz8PVqSRklLhKZOWoz4MyuhBRckJBw';




        $template_data['thing2']['value'] = isset($lawyerInformation['name']) ?$lawyerInformation['name'] : '' ;//回答律师
        $template_data['thing1']['value'] = $row['problem']; //用户问题
        $template_data['thing3']['value'] = $data['description'];//备注

        //$template_data = json_encode($template_data);

        $res = $payWx->sendMessage($open_id,$template_id ,$template_data,$page);
        if($res !== true){
            $this->error('失败');
        }
        CaseSourceSquareModel::where('id',$data['case_source_square_id'] )->update(['is_send'=>1]);

        $this->success('成功');
    }






}