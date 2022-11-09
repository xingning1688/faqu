<?php
namespace app\api\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class OrderContract extends BaseModel {
    protected $table = 'order_contract';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function buyOrderContract(&$parameter){
        Db::startTrans();

        $res = self::addOrderContractData($parameter);
        if($res===false){
            Db::rollback();
            return false;
        }
        $res = OrderContractDetail::addOrderContractDetailData($parameter);
        if($res===false){
            Db::rollback();
            return false;
        }

        Db::commit();
        return true;
    }

    public static function addOrderContractData(&$parameter){
        //组装 order_contract 表数据
        $order_contract['open_id'] = $parameter['open_id'];
        $order_contract['platform'] = $parameter['platform'];
        $order_contract['order_no'] = getOrderNumber();
        $order_contract['order_price'] = $parameter['order_price'];

        $res = self::create($order_contract);
        if(!isset($res->id) && empty($res->id)){
            return false;
        }
        $parameter['order_contract_id'] = $res->id;
        $parameter['order_contract']['order_no'] =  $order_contract['order_no'];
        $parameter['order_contract']['create_time'] =  date('Y-m-d H:i:s',time());
        return true;
    }

    public static function getOrderContractById($order_no){
        $row = self::where('order_no',$order_no)->find();
        if(empty($row)){
            return [];
        }
        $row = $row->toArray();
        return $row;
    }




}