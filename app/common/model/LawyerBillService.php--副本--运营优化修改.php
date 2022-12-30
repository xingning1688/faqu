<?php
namespace app\common\model;

use app\common\model\BaseModel;

class LawyerBillService extends BaseModel {
    protected $table = 'lawyer_bill_service';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';


    public static function getType(){
        $type = [
            1=>'短视频脚本撰写',
            2=>'短视频挂载功能',
            3=>'短视频拍摄加导演指导',
            4=>'短视频剪辑加审核',
            5=>'账号互动维系',
            6=>'用户咨询初筛',
            7=>'高意向客户维系',
            8=>'直播脚本',
            9=>'数据复盘',
            10=>'场地音画设备指导',
            11=>'粉丝新增',
            12=>'拍摄指导',
            13=>'着装指导',
            14=>'人设定位账号规划',
            15=>'律师线上包装',
            16=>'转化工具设计',
        ];
        return $type;
    }

    public static function getType2(){
        $type = [
            1=>['type_id'=>1,'type_name'=>'短视频脚本撰写','price'=>'1.00'],
            2=>['type_id'=>2,'type_name'=>'短视频挂载功能','price'=>'2.00'],
            3=>['type_id'=>3,'type_name'=>'短视频拍摄加导演指导','price'=>'0.00'],
            4=>['type_id'=>4,'type_name'=>'短视频剪辑加审核','price'=>'0.00'],
            5=>['type_id'=>5,'type_name'=>'账号互动维系','price'=>'0.00'],
            6=>['type_id'=>6,'type_name'=>'用户咨询初筛','price'=>'0.00'],
            7=>['type_id'=>7,'type_name'=>'高意向客户维系','price'=>'0.00'],
            8=>['type_id'=>8,'type_name'=>'直播脚本','price'=>'0.00'],
            9=>['type_id'=>9,'type_name'=>'数据复盘','price'=>'0.00'],
            10=>['type_id'=>10,'type_name'=>'场地音画设备指导','price'=>'0.00'],
            11=>['type_id'=>11,'type_name'=>'粉丝新增','price'=>'0.00'],
            12=>['type_id'=>12,'type_name'=>'拍摄指导','price'=>'0.00'],
            13=>['type_id'=>13,'type_name'=>'着装指导','price'=>'0.00'],
            14=>['type_id'=>14,'type_name'=>'人设定位账号规划','price'=>'0.00'],
            15=>['type_id'=>15,'type_name'=>'律师线上包装','price'=>'0.00'],
            16=>['type_id'=>16,'type_name'=>'转化工具设计','price'=>'0.00'],

        ];
        return $type;
    }

    public static function getList($where = []){
        $list = self::getListWhere($where)->select()->toArray();
        $list = self::assemblyDataList($list);
        return $list;
    }

    public static function getListWhere($where = []){
        $search['page'] = request()->param('page',1);
        $search['limit'] = request()->param('limit',30);
        $search['bill_date'] = request()->param('bill_date','');
        if(!empty($where)){
            $search = array_merge($search,$where);
        }
        $query = self::order('bill_date', 'desc');

        if (isset($search['page']) && $search['page']) {
            $query->page($search['page'],$search['limit']);
        }

        if (isset($search['bill_date']) && $search['bill_date']) {
            $query->whereMonth('bill_date',$search['bill_date']);
        }

        if (isset($search['lawyer_information_id']) && $search['lawyer_information_id']) {
            $query->where('lawyer_information_id',$search['lawyer_information_id']);
        }


        return $query;
    }

    public static function assemblyDataList($data){
       $type = self::getType();
       $data = array_map(function($item) use ($type) {
           $item['type_name'] = isset($type[$item['type_id']])? $type[$item['type_id']] : '';
           $item['total_price'] = number_format($item['price'] * $item['num'],2);
           return $item;
       },$data);
        return $data;
    }

    public static function getMonthTotal($where = []){
        $search['bill_date'] = request()->param('bill_date','');
        if(!empty($where)){
            $search =array_merge($search,$where);
        }

        if(empty($search['bill_date'])){
            return false;
        }

        if(isset($search['lawyer_information_id']) && empty($search['lawyer_information_id'])){
            return false;
        }
        $list = self::where('lawyer_information_id',$search['lawyer_information_id'])->whereMonth('bill_date',$search['bill_date'])->select()->toArray();
        if(empty($list)){
            return number_format(0,2);
        }
        $total_price = 0;
        foreach($list as $key=>$item){
            $total_price += $item['price'] * $item['num'];
        }
        return number_format($total_price,2);
    }



}