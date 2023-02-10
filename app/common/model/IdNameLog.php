<?php
namespace app\common\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class IdNameLog extends BaseModel {
    protected $table = 'id_name_log';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';



    public static function addData($data){
        $res = self::create($data);
        if(!$res){
            return false;
        }
        return true;
    }

    //获取某个订单的详细
    public static function getOrder($oid){
        $row = self::where('order_id',$oid)->find();
        if(empty($row)){
            return [];
        }

        $row = $row->toArray();
        return $row;
    }

}




