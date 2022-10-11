<?php
namespace app\api\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class LeaveMessages extends BaseModel {
    protected $table = 'leave_message';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function addData($data){
        $leaveMessages = new leaveMessages();
        $leaveMessages= $leaveMessages->save($data);
        if(!$leaveMessages){
            return false;
        }
        return true;
    }






}