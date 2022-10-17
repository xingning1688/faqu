<?php

namespace app\api\model;

use app\api\model\BaseModel;
use think\facade\Db;

class ContractType extends BaseModel {
    protected $table = 'contract_type';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';


    public static function getContractType(){
        $list = self::where('status',0)->select()->toArray();
        return $list;
    }



}