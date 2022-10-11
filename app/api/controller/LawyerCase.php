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

use think\admin\Controller;
use think\facade\Db;
use app\api\model\LawyerCase as LawyerCases;

/**
 * Class Index
 * @package app\index\controller
 */
class LawyerCase  extends AuthController
{
    //合同详情
    public function detail(){
        $id = request()->param('id', 0);
        if(empty($id) || !is_numeric($id)){
            $this->error('参数错误');
        }

        $data = LawyerCases::detail($id);
        if(empty($data)){
            $this->success('暂无数据');
        }
        $this->success('ok',$data);
    }



}