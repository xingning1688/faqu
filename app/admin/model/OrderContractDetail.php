<?php
namespace app\admin\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use app\admin\model\LawyerCase;
use think\facade\Db;


class OrderContractDetail extends BaseModel {
    protected $table = 'order_contract_detail';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function getOrderContractDetails($order_contract_ids){
        $order_contract_details = self::whereIn('order_contract_id',$order_contract_ids)->field('id,order_contract_id,lawyer_case_id,num,price')->select()->toArray();

        $data = self::assembleData($order_contract_details);
        return $data;

    }

    public static function assembleData($data){
        $lawyer_case_ids = array_unique(array_column($data,'lawyer_case_id'));
        $lawyerCases = LawyerCase::detailIds($lawyer_case_ids);

        $newData = [];
        foreach($data as $key=>$item){ //dump($item,$lawyerCases);exit;
            $item['lawyer_case_title'] = isset($lawyerCases[$item['lawyer_case_id']])? $lawyerCases[$item['lawyer_case_id']]['title']: '';
            $item['lawyer_case_author'] = isset($lawyerCases[$item['lawyer_case_id']])? $lawyerCases[$item['lawyer_case_id']]['author']: '';
            $newData[$item['order_contract_id']][$item['id']] = $item;
        }
        return $newData;

    }






}