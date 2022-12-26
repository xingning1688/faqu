<?php
namespace app\common\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class LawyerScript extends BaseModel {
    protected $table = 'script';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function getList($where = []){
        $list = self::getListWhere($where)->select()->toArray();
        return $list;
    }

    public static function getListWhere($where = []){
        $search['page'] = request()->param('page',1);
        $search['limit'] = request()->param('limit',30);
        $search['status'] = request()->param('status','-1');
        $search['script_date'] = request()->param('script_date','');
        if(!empty($where)){
            $search = array_merge($search,$where);
        }
        $query = self::order('create_time', 'desc');

        if (isset($search['page']) && $search['page']) {
            $query->page($search['page'],$search['limit']);
        }

        if (isset($search['status']) && $search['status'] != '-1') {
            $query->where('status',$search['status']);
        }

        if (isset($search['script_date']) && $search['script_date']) {
            $query->whereMonth('script_date',$search['script_date']);
        }

        if (isset($search['lawyer_information_id']) && $search['lawyer_information_id']) {
            $query->where('lawyer_information_id',$search['lawyer_information_id']);
        }


        return $query;
    }








}