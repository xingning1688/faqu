<?php
namespace app\api\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class SigningLawyer extends BaseModel {
    protected $table = 'signing_lawyer';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function addData($data){ //dump($data);exit;
        $signingLawyer = new SigningLawyer();
        $signingLawyer= $signingLawyer->save($data);
        if(!$signingLawyer){
            return false;
        }
        return true;
    }






}