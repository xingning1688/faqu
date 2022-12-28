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
use think\admin\Controller;
use think\facade\Db;
use app\api\model\Feedback as FeedbackModel;
use app\api\controller\Common as CommonController;

use app\api\model\Banner;
use app\common\model\Order;
use app\common\model\LawyerInformation;
use app\common\model\LawyerBillService as LawyerBillServiceModel;
use app\common\model\Common;
use app\common\model\LawyerCase;




class LawyerFuwuCase  extends AuthController
//class LawyerFuwuCase  extends Controller
{
    //律师的 合同
    public  function  lawyerCase(){
        $userIdentity = Common::getUserIdentity();
        if($userIdentity['code'] == 0){
            $this->error($userIdentity['msg']);
        }

        if(($userIdentity['code'] == 1) && ($userIdentity['user_identity'] == 0) ){
            $this->error('普通用户，暂无其他数据',$userIdentity);
        }

        $LawyerInformation = $userIdentity['lawyerInformation'];
        $where['user_id'] = $LawyerInformation['user_id'];
        $list_data = LawyerCase::getList($where);
        $this->success('成功',$list_data);

    }


    //添加合同
    public function addCase(){
        //test
        /*$data['user_id'] = 0;
        $data['title'] = request()->param('title','title');
        $data['contract_type_id'] = request()->param('contract_type_id',1);
        $data['file_url'] = request()->param('file_url','file_url');
        $data['author'] = request()->param('author','author');
        $data['page'] = request()->param('page',0);
        $data['original_price'] = request()->param('original_price',0.00);
        $data['sales_price'] = request()->param('sales_price',0.00);*/
        //test

        $userIdentity = Common::getUserIdentity();
        if($userIdentity['code'] == 0){
            $this->error($userIdentity['msg']);
        }

        if(($userIdentity['code'] == 1) && ($userIdentity['user_identity'] == 0) ){
            $this->error('普通用户，暂无其他数据',$userIdentity);
        }

        $LawyerInformation = $userIdentity['lawyerInformation'];
        $data['user_id'] = $LawyerInformation['user_id'];
        $data['title'] = request()->param('title','');
        $data['contract_type_id'] = request()->param('contract_type_id',0);
        $data['file_url'] = request()->param('file_url','');
        $data['author'] = request()->param('author','');
        $data['page'] = request()->param('page',0);
        $data['original_price'] = request()->param('original_price',0.00);
        $data['sales_price'] = request()->param('sales_price',0.00);

        if(empty($data['title'])){
            $this->error('合同标题不能为空');
        }

        if(empty($data['contract_type_id'] )){
            $this->error('合同类型不能为空');
        }

        if(empty($data['file_url'] )){
            $this->error('请上传合同');
        }

        if(empty($data['author'] )){
            $this->error('拟定人不能为空');
        }

        if(!is_numeric($data['original_price']) || !is_numeric($data['sales_price'])){
            $this->error('价格类型不正确');
        }

        $res = LawyerCase::addData($data);
        if($res === false){
            $this->error('合同添加失败');
        }
        $this->success('合同添加成功');

    }



    /*public function myCase(){

        $userIdentity = Common::getUserIdentity();
        if($userIdentity['code'] == 0){
             $this->error($userIdentity['msg']);
        }

        if(($userIdentity['code'] == 1) && ($userIdentity['user_identity'] == 0) ){
            $this->error('普通用户，暂无其他数据',$userIdentity);
        }

        $LawyerInformation = $userIdentity['lawyerInformation'];
        //$where['user_id'] = $LawyerInformation['user_id'];
        $res = Db::table('order_contract')
            ->alias('o')
            ->join('order_contract_detail od','o.id = od.order_contract_id')
            ->join('lawyer_case c','od.lawyer_case_id = c.id')
            ->where('c.user_id',$LawyerInformation['user_id'])
            ->select()->toArray();
        dump($res);exit;

        $where['lawyer_user_id'] = $LawyerInformation['user_id'];
        $list_data = LeaveMessage::getList($where); //获取当前律师 咨询订单

        $this->success('ok',$list_data);
    }*/






}