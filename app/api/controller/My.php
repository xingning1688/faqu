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
use app\api\model\LawyerProfessionals;
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



class My  extends AuthController
//class My  extends Controller
{
    //我的购买合同列表
    public function myContract(){
        $parameter['open_id'] = request()->get('open_id','');
        $parameter['page'] = request()->get('page',1);
        $parameter['pay_status'] = request()->get('pay_status',-1);
        if(empty($parameter['open_id']) || !is_numeric($parameter['page'])){
            $this->error('参数不合法');
        }
        $data = OrderContract::getMyList($parameter);
        $this->success('ok',$data);
    }

    public function myConsulting(){
        $parameter['open_id'] = request()->get('open_id','');
        $parameter['page'] = request()->get('page',1);
        $parameter['pay_status'] = request()->get('pay_status',-1);
        if(empty($parameter['open_id']) || !is_numeric($parameter['page'])){
            $this->error('参数不合法');
        }
        $data = LeaveMessages::myConsulting($parameter);
        $this->success('ok',$data);

    }

    public function myOrder(){
        $parameter['open_id'] = request()->get('open_id','');
        $parameter['page'] = request()->get('page',1);
        $parameter['pay_status'] = request()->get('pay_status',-1);
        if(empty($parameter['open_id']) || !is_numeric($parameter['page'])){
            $this->error('参数不合法');
        }
        $data = Order::myOrder($parameter);
        $this->success('ok',$data);
    }

    //获取律师个人资料
    public function  myInformation(){

        $parameter['open_id'] = request()->param('open_id','');
        if(empty($parameter['open_id'])){
            $this->error('参数不合法');
        }

        $where['open_id'] = $parameter['open_id'];
        $PlatformUser = PlatformUser::getMsgByRow($where,['id,open_id,phone']);
        if(empty($PlatformUser)){
            $this->error('暂无用户数据');
        }

        if(empty($PlatformUser['phone'])){
            $this->error('普通用户，暂不能修改信息');
        }
        $wherePhone['phone'] = $PlatformUser['phone'];
        $LawyerInformation = LawyerInformation::getMsgByRow($wherePhone);
        if(empty($LawyerInformation)){
            $this->error('普通用户，有手机号，暂无律师信息');
        }

        $LawyerProfessionals =  LawyerProfessionals::getProfessiona();
        if(!empty($LawyerInformation['professional_field_id'])){
            $LawyerInformation['professional_field_id'] = explode(',',trim($LawyerInformation['professional_field_id'],','));
            if(!empty($LawyerInformation['professional_field_id'])){
                foreach($LawyerInformation['professional_field_id'] as $key=>$item){
                    $LawyerInformation['professional_field_id'][$key] = isset($LawyerProfessionals[$item])?[$item=>$LawyerProfessionals[$item]]:'';
                }
            }

        }

        $this->success('ok',$LawyerInformation);
    }

    //获取律师个人资料
    public function  editMyInformation(){

        $data['profile_photo'] = request()->param('profile_photo','');
        $data['name'] = request()->param('name','');
        $data['professional_title'] = request()->param('professional_title','');
        $data['experience'] = request()->param('experience',0);//必须是数字
        $data['professional_field_id'] = request()->param('professional_field_id','');  //, 拼接
        $data['law_firm_affiliation'] = request()->param('law_firm_affiliation','');
        $data['province'] = request()->param('province','');
        $data['city'] = request()->param('city','');
        $data['area'] = request()->param('area','');
        $data['certificate'] = request()->param('certificate','');//律师证
        $data['lawyer_introduction'] = request()->param('lawyer_introduction','');//律师简介
        $data['honor'] = request()->param('honor','');// 荣誉评价
        $data['professional_studies'] = request()->param('professional_studies','');// 专业研究
        $data['classic_case'] = request()->param('classic_case','');// 经典案例

        //验证
        if(empty($data['name'])){
            $this->error('姓名不能为空');
        }

        if(empty($data['professional_title'])){
            $this->error('职业职称不能为空');
        }

        if(!is_numeric($data['experience']) || ($data['experience'] < 0)){
            $this->error('经验必须是数值');
        }


        $LawyerProfessionals =  LawyerProfessionals::getProfessiona();
        $keys = array_keys($LawyerProfessionals);
        foreach($data['professional_field_id'] as $key=>$item){
            if(!in_array($item,$keys)){
                $this->error('请选择专题研究');
            }
        }
        $data['professional_field_id'] = ','.(implode(',',$data['professional_field_id'])).',';

        if(empty($data['law_firm_affiliation'])){
            $this->error('律所不能为空');
        }

        if(empty($data['province'])){
            $this->error('请选择工作地点');
        }

        if(empty($data['city'])){
            $this->error('城市不能为空');
        }

        if(empty($data['area'])){
            $this->error('区不能为空');
        }

        if(empty($data['lawyer_introduction'])){
            $this->error('律师简介不能为空');
        }

        if(empty($data['honor'])){
            $this->error('律师荣誉不能为空');
        }

        if(empty($data['professional_studies'])){
            $this->error('专业研究不能为空');
        }

        if(empty($data['classic_case'])){
            $this->error('经典案例不能为空');
        }


        $parameter['id'] = request()->param('id',0);//律师信息表id
        $parameter['open_id'] = request()->param('open_id','');
        if(empty($parameter['open_id'])){
            $this->error('参数不合法');
        }

        $where['open_id'] = $parameter['open_id'];
        $PlatformUser = PlatformUser::getMsgByRow($where,['id,open_id,phone']);
        if(empty($PlatformUser)){
            $this->error('暂无用户数据');
        }

        if(empty($PlatformUser['phone'])){
            $this->error('普通用户，暂不能修改信息');
        }
        $wherePhone['phone'] = $PlatformUser['phone'];
        $LawyerInformation = LawyerInformation::getMsgByRow($wherePhone);
        if(empty($LawyerInformation)){
            $this->error('普通用户，有手机号，暂无律师信息');
        }

        if($LawyerInformation['id'] != $parameter['id']){
            $this->error('律师信息不合法');
        }

        $res = LawyerInformation::where('id',$LawyerInformation['id'])->update($data);
        if(!$res){
            $this->error('律师信息修改失败');
        }
        $this->success('ok');
    }




}