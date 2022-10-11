<?php
namespace app\api\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class OrderContractDetail extends BaseModel {
    protected $table = 'order_contract_detail';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function addOrderContractDetailData($parameter){

        //组装 order_contract_detail 表数据
        $order_contract_detail = [];
        foreach($parameter['lawyer_case_detail'] as $key=>$item){
            $order_contract_detail[$key]['order_contract_id'] = $parameter['order_contract_id'];
            $order_contract_detail[$key]['lawyer_case_id'] = $item['lawyer_case_id'];
            $order_contract_detail[$key]['num'] = $item['num'];
            $order_contract_detail[$key]['price'] = $item['price'];
        }

        $order_detail = new self();
        $res = $order_detail->saveAll($order_contract_detail);
        if(!$res){
            return false;
        }
        return true;
    }




}