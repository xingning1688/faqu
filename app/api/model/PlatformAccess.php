<?php
namespace app\api\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class PlatformAccess extends BaseModel {
    protected $table = 'platform_access';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function getPlatform($Platform){
        $platform = self::where('platform',$Platform)->find();
        if(empty($platform)){
            return [];
        }
        $platform = $platform->toArray();
        if(!is_numeric($platform['update_time'])){
            $platform['update_time'] = strtotime($platform['update_time']);
        }
        return $platform;
    }

    public static function updatePlatform($update_data){
        $res = self::where('id',$update_data['id'])->update($update_data);
        if(!$res){
            return false;
        }
        return true;
    }




}