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

use app\common\model\AliApi;
use think\admin\Controller;



class Ali  extends   Controller{


    //身份证 与 姓名 核验
    public function idNameverify(){
       /* $postData['idcard'] = '120101195904231023';
        $postData['name'] = '赵秀玲';*/

        $postData['idcard'] = request()->param('idcard','');
        $postData['name'] = request()->param('name','');
        if(empty($postData['idcard']) || empty($postData['name'])){
            $this->error('参数错误');
        }

        $AliApi = new AliApi();
        $res = $AliApi->idNameverify($postData);
        if( $res === false ){
            $this->error('失败');
        }
        $this->success('ok',$res);
    }










}