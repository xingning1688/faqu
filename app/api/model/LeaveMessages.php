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

    public static function myConsulting($parameter){

        $where['open_id'] = $parameter['open_id'];
        $data = self::where($where)->page($parameter['page'],10)->field('id,order_no,title,status')->select()->toArray();
        return $data;
    }






}