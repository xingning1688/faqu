<?php
namespace app\common\model;

use app\common\model\BaseModel;

class LawyerOperationalData extends BaseModel {
    protected $table = 'lawyer_operational_data';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    //获取最近运营数据
    public static function getRowData($lawyer_information_id){
        $row = self::where('lawyer_information_id',$lawyer_information_id)->order('create_time','desc')->find();
        return $row ? $row->toArray() :[];

    }




}