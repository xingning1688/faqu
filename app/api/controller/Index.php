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
use app\api\controller\Common;
use app\api\model\LawyerCase;
use app\api\model\Banner;

/**
 * Class Index
 * @package app\index\controller
 */

class Index  extends Controller
{
    //首页接口
    public function index(){
        $data['lawyer_list'] = LawyerInformations::getLawyerIndexList();//首页律师列表
        $data['lawyer_case_list'] = LawyerCase::getCaseIndexList();//首页合同列表
        $data['banner_list'] = Banner::getBannerList();//首页banner
        $this->success('ok',$data);
    }




}