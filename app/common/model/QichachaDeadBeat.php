<?php
namespace app\common\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class QichachaDeadBeat extends BaseModel {
    protected $table = 'qichacha_dead_beat';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';



    public static function addData($data){
        $res = self::create($data);
        if(!$res){
            return false;
        }
        return true;
    }

}





