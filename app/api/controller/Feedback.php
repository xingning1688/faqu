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
use app\api\validate\Feedback as FeedbackValidate;
use think\admin\Controller;
use think\facade\Db;
use app\api\model\Feedback as FeedbackModel;
use app\api\controller\Common;
use app\api\model\LawyerCase;
use app\api\model\Banner;



class Feedback  extends Controller
{
    //意见反馈接口
    public function add(){
        //test
        /*$data['open_id'] = '';
        $data['content'] = 'content';
        $data['phone'] = '13666666666';
        $data['email'] ='email@163.com';
        $data['platform'] =1;*/
        //test

        $data['open_id'] = request()->post('open_id','');
        $data['content'] = request()->post('content','');
        $data['phone'] = request()->post('phone','');
        $data['email'] = request()->post('email','');
        $data['platform'] = request()->post('platform',0);

        //校验提交过来的数据是否合法
        if( empty($data['content']) ||   empty($data['phone']) || empty($data['platform']) ){
            $this->error('参数不能为空');
        }

        if(!is_numeric($data['platform'])){
            $this->error('参数不合法');
        }
        $validate = new FeedbackValidate();
        $returnVal = $validate->form($data);
        if($returnVal != ''){
            $this->error($returnVal);
        }

        $res = FeedbackModel::addData($data);
        if(!$res){
            $this->error('反馈失败');
        }
        $this->success('反馈成功');
    }




}