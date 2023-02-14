<?php
namespace app\api\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class PlatformUser extends BaseModel {
    protected $table = 'platform_user';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function updateOrCreate($data){
        $row = self::where('open_id',$data['open_id'])->find();
        if(empty($row)){//新增
            $res = self::create($data);
        }else{//更新
            $data['update_time'] = time();
            if( !empty($row['source_url']) || !empty($row['source_url_name']) || !empty($row['source_lawyer_id']) ||  !empty($row['source_open_id'])){
                if(isset($data['source_url'])){
                    unset($data['source_url']);
                }
                if(isset($data['source_url_name'])){
                    unset($data['source_url_name']);
                }

                if(isset($data['source_lawyer_id'])){
                    unset($data['source_lawyer_id']);
                }

                if(isset($data['source_open_id'])){
                    unset($data['source_open_id']);
                }
            }
            $res = self::where('open_id',$data['open_id'])->update($data);
            if($res==0){
                return true;
            }
        }

        if(!$res){
            return false;
        }
        return true;
    }




}