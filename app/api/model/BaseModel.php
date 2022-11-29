<?php
/**
 * 公共 Model
 * User: suli
 * Date: 2020/12/24
 * Time: 12:16
 */
namespace app\api\model;

use think\Model;

class BaseModel extends Model {


    public static function pluckAttr($attr){
        return self::column($attr,'id');
    }

    //根据id获取对应的id,attr数组
    public static function pluckAttrByIds($ids,$attr='name'){
        $ids = is_array($ids) ? array_unique($ids) :[$ids];
        //$res = self::whereIn('id',$ids)->column('id,name','id'); dump($res);exit;
        $data = self::whereIn('id',$ids)->column($attr,'id');
        return $data;
    }
}