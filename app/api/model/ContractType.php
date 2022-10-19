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
        $list = array_map(function($item){
            $item['name'] = $item['type_name'];
            return $item;
        },$list);

        $list = array_merge([['id'=>0,'name'=>'全部']],$list);
        return $list;
    }



}