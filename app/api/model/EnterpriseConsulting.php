<?php
namespace app\api\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class EnterpriseConsulting extends BaseModel {
    protected $table = 'enterprise_consulting';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function addData($data){ //dump($data);exit;
        $enterpriseConsulting = new EnterpriseConsulting();
        $enterpriseConsulting= $enterpriseConsulting->save($data);
        if(!$enterpriseConsulting){
            return false;
        }
        return true;
    }






}