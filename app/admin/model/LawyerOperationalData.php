<?php
namespace app\admin\model;

use app\admin\model\BaseModel;

use think\facade\Db;


class LawyerOperationalData extends BaseModel {
    protected $table = 'lawyer_operational_data';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';   






}