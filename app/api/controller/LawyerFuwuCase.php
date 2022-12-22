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




//class LawyerFuwuCase  extends AuthController
class LawyerFuwuCase  extends Controller
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