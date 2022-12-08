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
        $product_ids = array_unique(array_column($data,'product_id'));
        $products = Product::pluckAttrByIds($product_ids,'id,lawyer_information_id,name,cover,slider,description'); //产品id=》lawyer_information_id
        $lawyer_information_ids = array_unique(array_column($products,'lawyer_information_id'));
        $lawyerInformations = LawyerInformation::pluckAttrByIds($lawyer_information_ids,'id,name');

        $newData = [];
        foreach($data as $key=>$item){  //dump($item,$products);exit;
            //dump($item,$products);exit;
            //dump( isset($products[$item['product_id']]) ? (isset($lawyerInformations[$products[$item['product_id']]]) ? $lawyerInformations[$products[$item['product_id']]]['name'] : ''): '');exit;

            //isset($products[$item['product_id']]) ? (isset($lawyerInformations[$products[$item['product_id']]]) ? $lawyerInformations[$products[$item['product_id']]]['name'] : ''): '';
            //$item['lawyer_user_name'] = isset($lawyerInformations[$item['lawyer_user_id']]) ? $lawyerInformations[$item['lawyer_user_id']]['name'] : '';

            $item['product_cover'] =  isset($products[$item['product_id']]) ? $products[$item['product_id']]['cover'] : '';
            $item['product_slider'] =  isset($products[$item['product_id']]) ? explode('|',$products[$item['product_id']]['slider']) : '';
            $item['product_name2'] =  isset($products[$item['product_id']]) ? $products[$item['product_id']]['name'] : '';
            $item['product_description'] =  isset($products[$item['product_id']]) ? $products[$item['product_id']]['description'] : '';
            //$item['lawyer_user_name'] = isset($lawyerInformations[$item['lawyer_user_id']]) ? $lawyerInformations[$item['lawyer_user_id']]['name'] : '';
            $item['lawyer_user_name'] = isset($products[$item['product_id']]) ? (isset($lawyerInformations[$products[$item['product_id']]['lawyer_information_id']]) ? $lawyerInformations[$products[$item['product_id']]['lawyer_information_id']]['name'] : ''): '';
            $newData[$item['order_id']][$item['id']] = $item;
        }
        return $newData;

    }




}