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
use app\api\controller\Common;
use app\api\model\LawyerCase;
use app\api\model\Banner;
use app\common\model\Order;
use app\common\model\LawyerInformation;
use app\common\model\LawyerOperationalData as LawyerOperationalDataModel;



class LawyerOperationalData  extends AuthController
//class LawyerOperationalData  extends Controller
{
    //律师运营数据
    public function myOperationalData(){
        $parameter['open_id'] = request()->get('open_id','');
        if(empty($parameter['open_id'])){
            $this->error('参数不合法');
        }

        //判断用户身份
        $where['open_id'] = $parameter['open_id'];
        $PlatformUser = PlatformUser::getMsgByRow($where,['id,open_id,phone']);
        if(empty($PlatformUser)){
            $this->error('暂无用户数据');
        }

        $data['user_identity'] = 0; //普通用户  0； 1律师用户

        if(empty($PlatformUser['phone'])){
            $this->success('ok',$data);
        }
        $wherePhone['phone'] = $PlatformUser['phone'];
         $LawyerInformation = LawyerInformation::getMsgByRow($wherePhone,['id,phone,name,profile_photo,fw_end_date']);
         if(empty($LawyerInformation)){
             $this->success('ok',$data);
         }

        //获取运营数据
        $lawyer_operational_data = LawyerOperationalDataModel::getRowData($LawyerInformation['id']);
        $data['lawyer_operational_data'] = $lawyer_operational_data;
        $data['lawyer'] = $LawyerInformation;
        $data['user_identity'] = 1; //普通用户  0； 1律师用户
        $this->success('ok',$data);
    }




}