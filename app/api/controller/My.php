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
use app\api\validate\Feedback as FeedbackValidate;
use think\admin\Controller;
use think\facade\Db;
use app\api\model\Feedback as FeedbackModel;
use app\api\controller\Common;
use app\api\model\LawyerCase;
use app\api\model\Banner;



class My  extends AuthController
//class My  extends Controller
{
    //我的购买合同列表
    public function myContract(){
        $parameter['open_id'] = request()->get('open_id','');
        $parameter['page'] = request()->get('page',1);
        if(empty($parameter['open_id']) || !is_numeric($parameter['page'])){
            $this->error('参数不合法');
        }
        $data = OrderContract::getMyList($parameter);
        $this->success('ok',$data);
    }

    public function myConsulting(){
        $parameter['open_id'] = request()->get('open_id','');
        $parameter['page'] = request()->get('page',1);
        if(empty($parameter['open_id']) || !is_numeric($parameter['page'])){
            $this->error('参数不合法');
        }
        $data = LeaveMessages::myConsulting($parameter);
        $this->success('ok',$data);

    }






}