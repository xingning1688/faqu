<?php
/**
 * 注册会员
 * User: suli
 * Date: 2021/6/24
 * Time: 10:18
 */
namespace app\api\model;

use app\api\model\BaseModel;
use think\facade\Db;

class LawyerConsultation extends BaseModel {
    protected $table = 'lawyer_consultation';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    //获取某个用户的id 或者全部
    public static function list($page=1,$userId=0){

        $where[] = ['user_id','=',$userId];
        $where[] = ['status','=',1];

        $list = self::field(['consultation_title','price','status'])
            ->where($where)
            ->limit(5)
            ->order('sort', 'desc')
            ->page($page)
            ->select()
            ->toArray();
        return $list;
    }

    public static function getConsultation($page=1,$userId=0){

        $where[] = ['status','=',1];
        $where[] = ['user_id','=',$userId];
        $list = self::field(['id','consultation_title','price','status'])
            ->where($where)
            ->order('sort', 'desc')
            ->limit(5)
            ->page($page)
            ->select()
            ->toArray();

        return $list;
    }



}