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

class LawyerProfessionals extends BaseModel {
    protected $table = 'lawyer_professional';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';


    public static function getProfessionalList(){

        $where[] = ['status','=',0];
        $list = self::field(['id','professional','status'])
                    ->where($where)
                    //->limit(20)
                    ->order('sort', 'desc')
                    ->select()
                    ->toArray();

        return $list;
    }

    public static function getProfessiona(){

        $where[] = ['status','=',0];
        $list = self::field(['id','professional','status'])
            ->where($where)
            ->order('sort', 'desc')
            ->column('professional','id');

        return $list;
    }



}