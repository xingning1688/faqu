<?php
namespace app\admin\model;

use app\admin\model\BaseModel;

use think\facade\Db;


class Banner extends BaseModel {
    protected $table = 'banner';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /*public static function getBannerList(){
        $list = self::where('status',1)->select()->toArray();
        return $list;
    }*/




}