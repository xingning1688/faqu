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
use app\common\model\LawyerScript as LawyerScriptModel;




class LawyerScript  extends AuthController
//class LawyerScript  extends Controller
{
    //律师的 脚本
    public  function  lawyerScript(){
        $userIdentity = Common::getUserIdentity();
        if($userIdentity['code'] == 0){
            $this->error($userIdentity['msg']);
        }

        if(($userIdentity['code'] == 1) && ($userIdentity['user_identity'] == 0) ){
            $this->error('普通用户，暂无其他数据',$userIdentity);
        }

        $LawyerInformation = $userIdentity['lawyerInformation'];
        $where['lawyer_information_id'] = $LawyerInformation['id'];
        $list_data = LawyerScriptModel::getList($where);
        $this->success('成功',$list_data);

    }






    




}