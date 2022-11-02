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

use app\api\model\Question as QuestionModel;


class Question  extends Controller
{
    //热门问答详情
    public function detail(){
        $id = request()->param('id', 0);
        if(empty($id) || !is_numeric($id)){
            $this->error('参数错误');
        }

        $data = QuestionModel::detail($id);
        if(empty($data)){
            $this->success('暂无数据');
        }
        $res = QuestionModel::setReadNumber($id);
        $this->success('ok',$data);
    }

    //热门问答列表
    public function getList(){
        $list = QuestionModel::getList();
        $this->success('ok',$list);
    }



}