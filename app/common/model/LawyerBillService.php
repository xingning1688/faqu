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