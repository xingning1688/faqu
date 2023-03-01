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

    public static $status = [0=>'未处理',1=>'已处理','-1'=>'搁置'];
    public static $pay_status = [0=>'未支付',1=>'支付成功',2=>'支付失败'];
    public static $pay_type = [0=>'暂无',1=>'微信',2=>'支付宝',10=>'快手-未知',11=>'快手-微信',12=>'快手-支付宝',20=>'微信小程序支付'];
    public static $platform = [0=>'未知','1'=>'快手','2'=>'微信','3'=>'抖音'];

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

        if(isset($parameter['order_consignee'])){
            $res = OrderConsignee::addOrderConsignee($parameter);
            if($res===false){
                Db::rollback();
                return false;
            }
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
            $order_consignee =  OrderConsignee::getOrderConsignee($row['id']);
            $order_qichacha =  QichachaDeadBeat::getOrder($row['id']);
            if(!empty($order_qichacha)){
                $order_qichacha['result'] = json_decode($order_qichacha['result'],true);
            }

            $row['order_details'] = $order_details;
            $row['order_consignee'] = $order_consignee;
            $row['order_qichacha'] = $order_qichacha;
            return $row;

        }

    //获取某个服务商城订单的详情
    public static function getOrderDetailById2($oid){
        $row = self::where('id',$oid)->find();
        if(empty($row)){
            return [];
        }
        $row = $row->toArray();
        $order_details = OrderDetail::getOrderDetail($row['id']);
        $product_ids = array_column($order_details,'product_id');
        $subProduct = SubProduct::whereIn('product_id',$product_ids)->select()->toArray();
        $newSubProduct = [];
        if(!empty($subProduct)){
            foreach($subProduct as $key=>$item){
                $newSubProduct[$item['product_id']][] = $item;
            }
        }

        foreach($order_details as $key=>$item){
            if(isset($newSubProduct[$item['product_id']])){
                $order_details[$key]['sub_product'] = $newSubProduct[$item['product_id']];
                $order_details[$key]['sub_product_total'] = count($newSubProduct[$item['product_id']]);
            }
        }
        $row['order_details'] = $order_details;
        return $row;

    }


    //获取我的服务订单
    public static function myOrder($parameter){
        $open_id = $parameter['open_id'];
        $page = $parameter['page'];
        $where['open_id'] = $open_id;

        $data = self::where($where)->order('create_time','desc')->page($page,50)->select()->toArray();
        if(empty($data)){
            return [];
        }
        $data = self::getOrderDetail($data);
        return $data;
    }

    public static function getOrderDetail($data){
        //获取订单详情
        $order_ids = array_unique(array_column($data,'id'));
        $order_details = OrderDetail::getOrderDetails($order_ids);


        $status = self::$status;
        $pay_status = self::$pay_status;
        $pay_type = self::$pay_type;
        $platform = self::$platform;

        $data = array_map(function($item) use($order_details,$status,$pay_status,$pay_type,$platform){
            $item['order_details'] = isset($order_details[$item['id']]) ? $order_details[$item['id']]: [];
            $item['status2'] = $item['status'];
            $item['status'] = isset($status[$item['status']]) ? $status[$item['status']] : '';
            $item['pay_status2'] = $item['pay_status'];
            $item['pay_status'] = isset($pay_status[$item['pay_status']]) ? $pay_status[$item['pay_status']] : '';
            $item['pay_type'] = isset($pay_type[$item['pay_type']]) ? $pay_type[$item['pay_type']] : '';
            $item['platform'] = isset($platform[$item['platform']]) ? $platform[$item['platform']] : '';
            $item['pay_time'] = !empty($item['pay_time'])? date('Y-m-d H:i:s',$item['pay_time']) : '';

            return $item;
        },$data);
        return $data;
    }


}





