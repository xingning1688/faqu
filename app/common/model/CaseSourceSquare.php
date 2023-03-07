<?php
namespace app\common\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use app\api\model\LeaveMessages;
use think\facade\Db;
use app\api\model\PlatformUser;
use app\common\model\CaseSourceSquareDetail;


class CaseSourceSquare extends BaseModel {
    protected $table = 'case_source_square';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function addData($data){
        $caseSourceSquare = new self();
        $res = $caseSourceSquare->save($data);
        if(!$res){
            return false;
        }
        return true;
    }



    public static function getList($where = []){
        $list = self::getListWhere($where)->select()->toArray();
        $list = self::assemblyData($list);
        return $list;
    }

    public static function getListWhere($where = []){
        $search['page'] = request()->param('page',1);
        $search['limit'] = request()->param('limit',15);
        $search['status'] = request()->param('status','');
        if(!empty($where)){
            $search = array_merge($search,$where);
        }
        $query = self::order('shelves_time', 'desc');
        if (isset($search['page']) && $search['page']) {
            $query->page($search['page'],$search['limit']);
        }

        if (isset($search['lawyer_information_id']) && $search['lawyer_information_id']) {
            $query->where('lawyer_information_id',$search['lawyer_information_id']);
        }

        if ($search['status'] !== '') {
            $query->where('status',$search['status']);
        }

        $query->where('is_shelves',0);
        return $query;
    }

    public static function assemblyData($data){
        $open_ids = array_unique(array_column($data,'open_id'));
        $platform_user = PlatformUser::whereIn('open_id',$open_ids)->column('open_id,avatar_url','open_id');
        $data = array_map(function($item) use($platform_user){
            $item['avatar_url'] = isset($platform_user[$item['open_id']]) ? $platform_user[$item['open_id']]['avatar_url'] : '';
            $item['img'] = empty($item['img']) ? $item['img'] : explode('|',$item['img']);
            return $item;
        },$data);
        return $data;
    }


     public static function receivingSource($data){

         //$res = self::where('is_shelves',0)->where('id',$data['id'])->update(['lawyer_information_id'=>$data['lawyer_information_id'],'allocate_time'=>date('Y-m-d H:i:s',time()),'status'=>2]);
         $res = self::where('is_shelves',0)->where('id',$data['id'])->update($data);
         if(!$res){
            return false;
         }

         return true;
    }

    public static function unsolved($data){
        Db::startTrans();
        try {
            CaseSourceSquareDetail::create($data);
            CaseSourceSquare::where('id',$data['case_source_square_id'])->update(['lawyer_information_id'=>0,'status'=>0,'allocate_time'=>null]);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        return true;
    }

    public static function completeService($data){
        Db::startTrans();
        try {
            CaseSourceSquareDetail::create($data);
            CaseSourceSquare::where('id',$data['case_source_square_id'])->update(['finish_time'=>date('Y-m-d H:i:s',time()),'status'=>3]);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        return true;
    }





}