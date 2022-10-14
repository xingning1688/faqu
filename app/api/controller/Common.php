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


class Common  extends Controller
{
    //合同详情
    public function fabuConfig(){
        $version_number = request()->param('version_number', 0);//大于正式版本的 测试版本号
        if(empty($version_number) || !is_numeric($version_number)){
            $this->error('参数错误');
        }
        //2是 正式版本号（最后一提交成功的版本号）
        if($version_number<= 5){
            $data['wx'] = '微信号';
        }else{
            $data['wx'] = '快手号';
        }

        $this->success('ok',$data['wx']);
    }



}