<?php
namespace app\common\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class LawyerInformation extends BaseModel {
    protected $table = 'lawyer_information';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';


    //获取所属律师
    public static function allLawyer(){
        $lawyer_names = self::pluckAttr('name');
        return $lawyer_names;
    }

    public static function addOrder(&$parameter){

    }

    public static function getOrderNo($order_no){

    }




}





