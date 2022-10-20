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
class LawyerCase  extends Controller
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

    //合同列表
    public function list(){
        $params['contract_type_id'] = request()->param('contract_type_id', 0);
        $params['title'] = request()->param('title', '');
        $params['page'] = request()->param('page', 1);

        $where['status'] = 1;
        if(!is_numeric($params['contract_type_id']) || !is_numeric($params['page'])){
            $this->error('数据不合法');
        }

        $caseList = LawyerCases::getList($params);
        $this->success('ok',$caseList);

    }



}