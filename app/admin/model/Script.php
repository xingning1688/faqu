<?php
namespace app\admin\model;

use app\admin\model\BaseModel;

use think\facade\Db;


class Script extends BaseModel {
    protected $table = 'script';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';






}