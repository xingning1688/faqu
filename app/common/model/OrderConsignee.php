<?php
namespace app\common\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class OrderConsignee extends BaseModel {
    protected $table = 'order_consignee';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function addOrderConsignee($parameter){
        $parameter['order_consignee']['order_id'] = $parameter['order']['id'];
        $res = self::create($parameter['order_consignee']);
        if(!isset($res->id) && empty($res->id)){
            return false;
        }

        return true;
    }

    //获取某个订单的详细
    public static function getOrderConsignee($oid){
        $row = self::where('order_id',$oid)->find();
        if(empty($row)){
            return [];
        }

        $row = $row->toArray();
        return $row;
    }





}