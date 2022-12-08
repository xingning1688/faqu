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

    public static function getMsgById($id,$fields=['*']){
        $row = self::where('id',$id)->field($fields)->find();
        return $row ? $row->toArray() :[];
    }

    public static function getMsgByRow($where,$fields=['*']){
        $row = self::where($where)->field($fields)->find();
        return $row ? $row->toArray() :[];
    }

    public static function pluckAttr($attr){
        return self::column($attr,'id');
    }

    //根据id获取对应的id,attr数组
    public static function pluckAttrByIds($ids,$attr='name'){
        $ids = is_array($ids) ? array_unique($ids) :[$ids];
        $data = self::whereIn('id',$ids)->column($attr,'id');
        return $data;
    }
}