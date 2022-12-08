<?php
namespace app\common\model;

use app\common\model\BaseModel;

class Product extends BaseModel {
    protected $table = 'product';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static $is_recommend = [0=>'正常','1'=>'推荐'];
    public static $is_index = [0=>'正常','1'=>'首页'];
    public static  $status = [0=>'销售中','1'=>'下架'];
    public static $way = [1=>'线上销售','2'=>'线下销售'];
    public static $TYPE = [1=>'法律服务工具'];

    public static function getList(){
        $list = self::getListWhere()->select()->toArray();
        $list_data = self::assemblyData($list);
        return $list_data;
    }

    public static function getListWhere(){
        $search['page'] = request()->param('page',1);
        $search['limit'] = request()->param('limit',10);
        $search['name'] = request()->param('name','');
        $search['is_index'] = request()->param('is_index','');
        $search['is_recommend'] = request()->param('is_recommend','');
        $search['lawyer_information_id'] = request()->param('lawyer_information_id',0);


        $query = self::where('status',0)->order('sort', 'desc')->order('id', 'desc');
        if(isset($search['name']) && !empty($search['name'])){
            $query->like('name', '%'.$search['name'].'%');
        }

        if(isset($search['page']) && $search['page']) {
            $query->page($search['page'],$search['limit']);
        }

        if(isset($search['is_index']) && !empty($search['is_index'])){
            $query->where('is_index', $search['is_index']);
        }

        if(isset($search['is_recommend']) && !empty($search['is_recommend'])){
            $query->where('is_recommend', $search['is_recommend']);
        }

        if(isset($search['lawyer_information_id']) && !empty($search['lawyer_information_id'])){
            $query->where('lawyer_information_id', $search['lawyer_information_id']);
        }

        return $query;
    }

    public static function assemblyData($data){
        $way = self::$way;
        $type = self::$TYPE;
        $data = array_map(function($item)use($way,$type) {
            $item['way'] = isset($way[$item['way']]) ? $way[$item['way']] : '';
            $item['type'] = isset($type[$item['type']]) ? $type[$item['type']] : '';
            $item['slider'] = explode('|',$item['slider']);
            return $item;
        },$data);
        return $data;

    }


    public static function detail($id){
        $row = self::where('id',$id)->find();
        if(empty($row)){
            return [];
        }
        $data = $row->toArray();
        $data = self::assemblyDataDetail($data);
        return $data;
    }

    public static function assemblyDataDetail($data){

        $way = self::$way;
        $type = self::$TYPE;
        $data['way'] = isset($way[$data['way']]) ? $way[$data['way']] : '';
        $data['type'] = isset($type[$data['type']]) ? $type[$data['type']] : '';
        $data['slider'] = explode('|',$data['slider']);
        $row = LawyerInformation::getMsgById( $data['lawyer_information_id'],'id,user_id,name');
        $data['lawyer_user_id'] = !empty($row['user_id']) ? $row['user_id'] : 0;

        return $data;
    }




}