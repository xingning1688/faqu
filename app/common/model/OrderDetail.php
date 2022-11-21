<?php
namespace app\common\model;

use app\admin\model\SystemUser;
use app\api\model\BaseModel;
use app\api\model\LawyerInformations;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class OrderDetail extends BaseModel {
    protected $table = 'order_detail';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function addOrderDetail($parameter){


        foreach($parameter['order_detail'] as $key=>$item){
            $parameter['order_detail'][$key]['order_id'] = $parameter['order']['id'];
        }

        $order_detail = new self();
        $res = $order_detail->saveAll($parameter['order_detail']);
        if(!$res){
            return false;
        }
        return true;
    }



    public static function getOrderDetail($oid){
        $data = self::where('order_id',$oid)->select()->toArray();
        $lawyer_user_id = array_unique(array_column($data,'lawyer_user_id'));
        $lawyerInformations = LawyerInformations:: getByUserIds($lawyer_user_id);
        $data = array_map(function($item) use($lawyerInformations) {
            $item['lawyer_user_name'] = isset($lawyerInformations[$item['lawyer_user_id']]) ? $lawyerInformations[$item['lawyer_user_id']]['name'] : '';
            return $item;
        },$data);

        return $data;
    }

   /* public static function getOrderContractDetail($data){
        //获取订单详情
        $order_contract_ids = array_column($data,'id');
        $order_contract_details = self::whereIn('order_contract_id',$order_contract_ids)->field('id,order_contract_id,lawyer_case_id,num,price')->select()->toArray();
        $order_details = self::assembleData($order_contract_details);

        $status = [0=>'未处理',1=>'已处理','-1'=>'搁置'];
        $pay_status = [0=>'未支付',1=>'支付成功',2=>'支付失败'];
        //$pay_type = [0=>'暂无',1=>'微信',2=>'支付宝'];
        $platform = [0=>'未知','1'=>'快手','2'=>'微信','3'=>'抖音'];
        $data = array_map(function($item) use($order_details,$status,$pay_status,$platform){
            $item['order_details'] = isset($order_details[$item['id']]) ? $order_details[$item['id']]: [];
            $item['status'] = isset($status[$item['status']]) ? $status[$item['status']] : '';
            $item['pay_status'] = isset($pay_status[$item['pay_status']]) ? $pay_status[$item['pay_status']] : '';
            //$item['pay_type'] = isset($pay_type[$item['pay_type']]) ? $pay_type[$item['pay_type']] : '';
            $item['platform'] = isset($platform[$item['platform']]) ? $platform[$item['platform']] : '';
            $item['pay_time'] = !empty($item['pay_time'])? date('Y-m-d H:i:s',time()) : '';
            return $item;
        },$data);
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

    }*/




}