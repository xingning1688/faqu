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

    public static function getOrderDetails($oids){
        $order_details = self::whereIn('order_id',$oids)->select()->toArray();
        $order_details = self::assembleData($order_details);
        return $order_details;
    }

    public static function assembleData($data){
        $lawyer_user_ids = array_unique(array_column($data,'lawyer_user_id'));
        $lawyerInformations = LawyerInformations:: getByUserIds($lawyer_user_ids);

        $newData = [];
        foreach($data as $key=>$item){
            $item['lawyer_user_name'] = isset($lawyerInformations[$item['lawyer_user_id']]) ? $lawyerInformations[$item['lawyer_user_id']]['name'] : '';
            $newData[$item['order_id']][$item['id']] = $item;
        }
        return $newData;

    }




}