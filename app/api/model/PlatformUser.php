<?php
namespace app\api\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class PlatformUser extends BaseModel {
    protected $table = 'platform_user';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function updateOrCreate($data){
        $row = self::where('open_id',$data['open_id'])->find();
        if(empty($row)){//新增
            $res = self::create($data);
        }else{//更新
            $res = self::where('open_id',$data['open_id'])->update($data);
            if($res==0){
                return true;
            }
        }

        if(!$res){
            return false;
        }
        return true;
    }




}