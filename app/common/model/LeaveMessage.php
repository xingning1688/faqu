<?php
namespace app\common\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class LeaveMessage extends BaseModel {
    protected $table = 'leave_message';
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
        $search['type'] = request()->param('type',0);
        if(!empty($where)){
            $search = array_merge($search,$where);
        }
        $query = self::order('create_time', 'desc');

        if (isset($search['page']) && $search['page']) {
            $query->page($search['page'],$search['limit']);
        }

        if (isset($search['type']) && $search['type']) {

            if($search['type'] == 1){
                $query/*->where('pay_status',1)*/->whereIn('status',[0,1,2]);

            }elseif($search['type'] == 2){
                $query/*->where('pay_status',1)*/->where('status',3);
            }
        }

        if (isset($search['lawyer_user_id']) && $search['lawyer_user_id']) {
            $query->where('lawyer_user_id',$search['lawyer_user_id']);
        }


        return $query;
    }


     public static function updateStatus(){
         $where['open_id'] = request()->param('open_id','');
         $where['id'] = request()->param('id',0);
         if(empty($where['open_id']) || empty($where['id']) || !is_numeric($where['id'])){
            return false;
         }

         $res = self::where($where)->update(['status'=>3]);
         if(!$res){
            return false;
         }

         return true;
    }





}