<?php
/**
 * 注册会员
 * User: suli
 * Date: 2021/6/24
 * Time: 10:18
 */
namespace app\api\model;

use app\api\model\BaseModel;
use app\api\model\LawyerCase as LawyerCases;
use think\facade\Db;

class  Question extends BaseModel {
    protected $table = 'question';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';


    //热门问答列表
    public static function getList(){
        $list = self::getListWhere()->field('id,title,img,content,read_number,author,create_time')->select()->toArray();
        $list = self::assemblyDataList($list);
        return $list;
    }

    public static function getListWhere(){
        $search = request()->all();
        $search['page'] = isset($search['page']) ? $search['page'] : 1;
        $search['limit'] = isset($search['limit']) ? $search['page'] : 10;
        $query = self::order('create_time', 'desc');
        if (isset($search['title']) && $search['title']){
            $query->where('title', $search['title']);
        }

        if (isset($search['page']) && $search['page']) {
            $query->page($search['page'],$search['limit']);
        }

        return $query;
    }

    public static function assemblyDataList($data){
        foreach($data as $key=>$item){
            $data[$key]['content_abstract']= mb_substr($item['content'],0,50,'utf-8');
        }
        return $data;
    }

    public static function detail($id){
        $row = self::where('id',$id)->field('title,share_title,img,content,author,read_number,user_id,question_classification_id,is_recommend,create_time')->find();
        if(empty($row)){
            return [];
        }
        $data = $row->toArray();
        $data = self::assemblyDataDetail($data);
        return $data;
    }

    public static function assemblyDataDetail($data){
        $lawyer_info = LawyerInformations::detailUser($data['user_id']);
        $data['lawyer_info']['id'] = isset($lawyer_info['id']) ? $lawyer_info['id'] : '';
        $data['lawyer_info']['profile_photo'] = isset($lawyer_info['profile_photo']) ? $lawyer_info['profile_photo'] : '';
        $data['lawyer_info']['name'] = isset($lawyer_info['name']) ? $lawyer_info['name'] : '';
        $data['lawyer_info']['consultation_list'] = isset($lawyer_info['consultation_list']) ? $lawyer_info['consultation_list'] : [];

        return $data;
    }



    public static function setReadNumber($id){
        $res = self::where('id',$id)->inc('read_number', 1)->update();
        if(!$res){
            return false;
        }
        return true;

    }






}