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

class LawyerCase extends BaseModel {
    protected $table = 'lawyer_case';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    //获取某个用户的id 或者全部
    public static function list($page=1,$userId=0){

        $where[] = ['user_id','=',$userId];
        $where[] = ['status','=',1];

        $list = self::field(['id','title','author','page'])
            ->where($where)
            ->limit(5)
            ->order('sort', 'desc')
            ->page($page)
            ->select()
            ->toArray();
        return $list;
    }

    public static function getProfessiona(){

        $where[] = ['status','=',0];
        $list = Db::table('lawyer_professional')
            ->field(['id','professional','status'])
            ->where($where)
            ->order('sort', 'desc')
            ->column('professional','id');

        return $list;
    }

    //获取合同详情
    public static function detail($id){
        $row = self::where('status',1)->field('id,title,author,page,original_price,sales_price,file_url')->find($id);
        if(empty($row)){
            return [];
        }
        return $row->toArray();
    }

    public static function detailIds($ids){
        $row = self::whereIn('id',$ids)->where('status',1)->column(['id','title','author','page','original_price','sales_price','file_url'],'id');

        if(empty($row)){
            return [];
        }
        return $row;
    }

    //获取首页合同列表
    public static function getCaseIndexList(){
        $where[] = ['status','=',1];
        $where[] = ['is_recommend','=',1];

        $list = self::field('id,title,author,page,original_price,sales_price,file_url')
            ->where($where)
            ->limit(2)
            ->order('sort', 'desc')
            ->order('id', 'desc')
            ->select()
            ->toArray();

        if(empty($list)){
            return $list;
        }

        return $list;
    }



}