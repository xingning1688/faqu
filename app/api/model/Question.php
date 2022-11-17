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
use think\Request;

class  Question extends BaseModel {
    protected $table = 'question';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';


    //热门问答列表
    public static function getList(){
        $list = self::getListWhere()->field('id,title,abstract,img,content,read_number,author,type,create_time')->select()->toArray();
        return $list;
    }

    public static function getListWhere(){
        $search['page'] = request()->param('page',1);
        $search['limit'] = request()->param('limit',10);
        $search['title'] = request()->param('title','');
        $search['user_id'] = request()->param('user_id','');

        $query = self::order('create_time', 'desc')->where('delete_time',NULL);
        if (isset($search['title']) && !empty($search['title'])){
            $query->where('title', $search['title']);
        }

        if (isset($search['user_id']) && !empty($search['user_id'])){
            $query->where('user_id', $search['user_id']);
        }

        if (isset($search['page']) && $search['page']) {
            $query->page($search['page'],$search['limit']);
        }

        return $query;
    }

    public static function assemblyDataList($data){
        foreach($data as $key=>$item){
            $data[$key]['content_abstract']= mb_substr(htmlspecialchars_decode($item['content']),0,50,'utf-8').'......';
        }
        return $data;
    }

    public static function detail($id){
        $row = self::where('id',$id)->field('title,abstract,share_title,img,content,author,read_number,user_id,question_classification_id,is_recommend,type,create_time')->find();
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