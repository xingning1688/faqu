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
use app\api\validate\EnterpriseConsultingValidate;
use think\admin\Controller;
use think\facade\Db;
use app\api\model\EnterpriseConsulting as EnterpriseConsultings;
use app\api\controller\Common;
use app\api\model\LawyerCase;
use app\api\model\Banner;



class EnterpriseConsulting  extends Controller
{
    //企业咨询
    public function add(){
        //test
        /*$data['open_id'] = '';
        $data['title'] = 'title';
        $data['company'] = 'company';
        $data['phone'] = '13666666666';
        $data['platform'] = 1;*/
        //test

        $data['open_id'] = request()->post('open_id','');
        $data['title'] = request()->post('title');
        $data['company'] = request()->post('company');
        $data['phone'] = request()->post('phone');
        $data['platform'] = request()->post('platform',0);

        //校验提交过来的数据是否合法
        if( empty($data['title']) ||   empty($data['company'])  || empty($data['phone']) || empty($data['platform']) ){
            $this->error('参数不能为空');
        }

        if(!is_numeric($data['platform'])){
            $this->error('参数不合法3');
        }
        $validate = new EnterpriseConsultingValidate();
        $returnVal = $validate->form($data);
        if($returnVal != ''){
            $this->error($returnVal);
        }

        $res = EnterpriseConsultings::addData($data);
        if(!$res){
            $this->error('添加失败');
        }
        $this->success('咨询留言成功');
        $this->success('ok');
    }




}