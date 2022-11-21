<?php
namespace app\common\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class Order extends BaseModel {
    protected $table = 'order';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function createOrder(&$parameter){
        Db::startTrans();

        $res = self::addOrder($parameter);
        if($res===false){
            Db::rollback();
            return false;
        }
        $res = OrderDetail::addOrderDetail($parameter);
        if($res===false){
            Db::rollback();
            return false;
        }

        $res = OrderConsignee::addOrderConsignee($parameter);
        if($res===false){
            Db::rollback();
            return false;
        }

        Db::commit();
        return true;
    }

    public static function addOrder(&$parameter){
        //组装 order 表数据
        $order['open_id'] = $parameter['open_id'];
        $order['platform'] = $parameter['platform'];
        $order['order_no'] = getOrderNumber();
        $order['order_price'] = $parameter['order_price'];
        $order['pay_type'] = $parameter['pay_type'];
        $order['order_type'] = $parameter['order_type'];

        $res = self::create($order);
        if(!isset($res->id) && empty($res->id)){
            return false;
        }

        $parameter['order'] = $order;
        $parameter['order']['id'] =  $res->id;
        return true;
    }

    public static function getOrderNo($order_no){
        $row = self::where('order_no',$order_no)->find();
        if(empty($row)){
            return [];
        }
        $row = $row->toArray();
        return $row;
    }

        //获取某个服务商城订单的详情
        public static function getOrderDetailById($oid){
            $row = self::where('id',$oid)->find();
            if(empty($row)){
                return [];
            }
            $row = $row->toArray();
            $order_details = OrderDetail::getOrderDetail($row['id']);
            $row['order_details'] = $order_details;
            return $row;

        }


}





