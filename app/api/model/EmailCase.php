<?php
/**
 * 注册会员
 * User: suli
 * Date: 2021/6/24
 * Time: 10:18
 */
namespace app\api\model;

use app\api\model\BaseModel;
use think\facade\Db;

class EmailCase extends BaseModel {
    protected $table = 'email_case';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    //获取某个用户的id 或者全部
    public static function addData($data){
        $res = self::create($data);
        return $res;
    }





}