<?php
namespace app\api\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use think\facade\Db;


class LawyerInformations extends BaseModel {
    protected $table = 'lawyer_information';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function getInformationList(){
        $page = request()->param('page/d', 1);
        $name = request()->param('name', '');
        $typeId = request()->param('type_id', '');
        $limit =  request()->param('limit', 4);
       /* $is_recommend = request()->param('is_recommend', 0);*/
        if(!is_numeric($page)){
            return false;
        }

        if(!empty($name)){
            $where[] = ['name', '=', $name];
        }

        if(!empty($typeId)){
            if(!is_numeric($typeId)){
                return false;
            }
            $where[] = ['professional_field_id', 'like', '%,'.$typeId.',%'];
        }


        $where[] = ['status','=',1];

        $list = self::field(['id','professional_field_id','profile_photo','name','law_firm_affiliation','professional_title','experience'])
                    ->where($where)
                    ->limit($limit)
                    ->page($page)
                    ->order('id', 'desc')
                    ->select()
                    ->toArray();


        if(empty($list)){
            return $list;
        }
        $listData = self::assemblyDataList($list);
        return $listData;
    }

    public static function assemblyDataList($list){
        //获取标签
        $professionalList = LawyerProfessionals::getProfessiona();

        foreach($list as $key=>$item){
            $professional_field_ids = explode(',',trim($item['professional_field_id'],','));

            if(!empty($professional_field_ids)){
                $new_professional = [];
                foreach($professional_field_ids as $k=>$val){
                    $new_professional[] = isset($professionalList[$val])?$professionalList[$val]:'';
                }
            }

            $list[$key]['professional'] = $new_professional;
            unset($list[$key]['professional_field_id']);
        }

        return $list;
    }


    public static function detail($id){
        $data = LawyerInformations::field(['id','user_id','professional_field_id','name','law_firm_affiliation','profile_photo','lawyer_introduction ','honor','professional_studies','professional_title','experience','classic_case','share_copy'])->find($id);
        if(empty($data)){
            return [];
        }
        $data = $data->toArray();
        $DetailData = self::assemblyDataDetail($data);
        return $DetailData;
    }

    public static function assemblyDataDetail($data){

        $professionalList = LawyerProfessionals::getProfessiona(); //获取标签

        $caseList = LawyerCase::list(1,$data['user_id']);//获取合同
        $consultation = LawyerConsultation::getConsultation(1,$data['user_id']);//获取专项咨询


        $professional_field_ids = explode(',',trim($data['professional_field_id'],','));
        $new_professional = [];
        foreach($professional_field_ids as $k=>$val){
            $new_professional[] = isset($professionalList[$val])?$professionalList[$val]:'';
        }
        $data['lawyer_info']['lawyer_introduction'] = $data['lawyer_introduction'];
        $data['lawyer_info']['honor'] = $data['honor'];
        $data['lawyer_info']['professional_studies'] = $data['professional_studies'];
        $data['lawyer_info']['classic_case'] = $data['classic_case'];

        $data['professional'] = $new_professional;
        $data['case_list'] = $caseList;
        $data['consultation_list'] = $consultation;
        $data['share_copy'] = !empty($data['share_copy'])? $data['share_copy']: '快速获取我的更多法律服务';

        unset($data['professional_field_id']);
        unset($data['user_id']);
        return $data;
    }

    public static function getByUserIds($userIds){
        $data = LawyerInformations::where('status',1)->whereIn('user_id',$userIds)->column(['id','user_id','professional_field_id','name','law_firm_affiliation','profile_photo','lawyer_introduction ','honor','professional_studies','professional_title','experience','classic_case'],'user_id');
        if(empty($data)){
            return [];
        }
        return $data;
    }

    public static function getById($id){
        $data = LawyerInformations::field(['id','user_id','professional_field_id','name','law_firm_affiliation','profile_photo','lawyer_introduction ','honor','professional_studies','professional_title','experience','classic_case'])->find($id);
        if(empty($data)){
            return [];
        }
        $data = $data->toArray();
        return $data;
    }

    //获取首页律师列表
    public static function getLawyerIndexList(){
        $where[] = ['status','=',1];
        $where[] = ['is_recommend','=',1];

        $list = self::field(['id','professional_field_id','profile_photo','name','law_firm_affiliation','professional_title','experience'])
            ->where($where)
            ->limit(3)
            ->order('sort', 'desc')
            ->order('id', 'desc')
            ->select()
            ->toArray();

        if(empty($list)){
            return $list;
        }
        $listData = self::assemblyDataList($list);
        return $listData;
    }







}