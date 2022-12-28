<?php
namespace app\common\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class LawyerCase extends BaseModel {
    protected $table = 'lawyer_case';
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

        if (isset($search['user_id']) && $search['user_id']) {
            $query->where('user_id',$search['user_id']);
        }


        return $query;
    }

    public static  function addData($data){
        $res = self::create($data);
        if(!$res){
            return false;
        }
        return true;
    }








}