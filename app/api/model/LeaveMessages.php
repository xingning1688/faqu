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
        $data = self::where($where)->page($parameter['page'],50)->field('id,order_no,title,status,pay_status')->select()->toArray();
        return $data;
    }

    public static function orderValidation($data){


        if(empty($data['open_id'])){
            return 'open_id 不能为空';
        }



        if(empty($data['platform'])){
            return '平台 不能为空';
        }

        if(empty($data['type'])){
            return '咨询类型不能为空';
        }

        if(!in_array($data['type'],[1,2])){
            return '咨询类型不合法';
        }

        if(empty($data['order_price'])){
            return '咨询价格不能为空';
        }

        if(empty($data['title'])){
            return '咨询标题不能为空';
        }

        if($data['type'] == 1){
            if(empty($data['lawyer_user_id'])){
                return '律师id 不能为空';
            }

            if(empty($data['consultation_id'])){
                return '咨询参数不合法';
            }

            $consultation = LawyerConsultation::getById($data['consultation_id']);
            if(empty($consultation)){
                return '咨询数据不能为空';
            }

            if($consultation['price'] != $data['order_price']){
                return '咨询价格不合法';
            }
        }

        return true;
    }

    public static function buyOrderLeaveMessage(&$data){
        if(isset($data['consultation_id'])){
            unset($data['consultation_id']);
        }
        $leaveMessages = new self();
        $res= $leaveMessages->create($data);
        if(!isset($res->id) && empty($res->id)){
            return false;
        }
        $data['id'] = $res->id;
        $data['create_time'] =  date('Y-m-d H:i:s',time());
        return true;
    }

    public static function getByOrderNo($order_no){
        $row = self::where('order_no',$order_no)->find();
        if(empty($row)){
            return [];
        }
        $row = $row->toArray();
        return $row;
    }

    //获取某个合同订单的详情
    public static function getOrderById($oid){
        $data = self::where('id',$oid)->find();
        if(empty($data)){
            return [];
        }
        $data = $data->toArray();

        return $data;

    }

    //支付成功后，更新订单咨询订单信息
    public static function updateMessageData($data){
        $res = self::where('order_no',$data['order_no'])->where('open_id',$data['open_id'])->update($data);
        if(!$res){
            return false;
        }
        return true;
    }






}