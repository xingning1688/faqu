<?php
/**
 * 公共 Model
 * User: suli
 * Date: 2020/12/24
 * Time: 12:16
 */
namespace app\common\model;

use think\Model;
use app\api\model\PlatformUser;
use app\common\model\LawyerInformation;
class Common extends Model {
    public static function getUserIdentity(){
        $parameter['open_id'] = request()->get('open_id','');
        $data['code'] = 0;
        if(empty($parameter['open_id'])){
            $data['msg'] = '参数不合法';
            return $data;
        }

        //判断用户身份
        $where['open_id'] = $parameter['open_id'];
        $PlatformUser = PlatformUser::getMsgByRow($where,['id,open_id,phone']);
        if(empty($PlatformUser)){
            $data['msg'] = '暂无用户数据';
            return $data;
        }

        $data['user_identity'] = 0; //普通用户  0； 1律师用户

        if(empty($PlatformUser['phone'])){
            $data['code'] = 1;
            $data['msg'] = '普通用户，暂无手机号';
            return $data;
        }
        $wherePhone['phone'] = $PlatformUser['phone'];
        $LawyerInformation = LawyerInformation::getMsgByRow($wherePhone,['id,phone,name,profile_photo,fw_end_date']);
        if(empty($LawyerInformation)){
            $data['code'] = 1;
            $data['msg'] = '普通用户，有手机号，暂无律师信息';
            return $data;
        }
        $data['code'] = 1;
        $data['user_identity'] = 1; //普通用户  0； 1律师用户
        $data['lawyerInformation'] = $LawyerInformation;
        return $data;

    }



}