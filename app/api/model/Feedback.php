<?php
namespace app\api\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class Feedback extends BaseModel {
    protected $table = 'feedback';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function addData($data){ //dump($data);exit;
        $feedback = new Feedback();
        $feedback= $feedback->save($data);
        if(!$feedback){
            return false;
        }
        return true;
    }






}