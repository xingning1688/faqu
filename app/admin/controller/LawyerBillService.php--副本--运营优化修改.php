<?php


namespace app\admin\controller;

use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\admin\model\SystemAuth;
use think\admin\model\SystemBase;
use think\admin\model\SystemUser;
use think\admin\service\AdminService;
use think\model\Relation;
use think\facade\Session;
use think\facade\Db;
use app\admin\model\LawyerBillService as LawyerBillServiceModel;
use app\common\model\LawyerBillService as LawyerBillServiceCommonModel;
use app\common\model\LawyerInformation;

/**
 * 服务账单管理
 * @auth true  # 表示需要验证权限
 * @menu true  # 添加系统菜单节点
 * @login true # 强制登录才可访问
 */
class LawyerBillService extends Controller
{
    private $table = 'lawyer_bill_service';
    public $status = [0=>'待审核','1'=>'审核成功','2'=>'审核失败'];
    /**
     * 服务账单列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {
        $this->title = '服务账单列表';
        $lawyer = LawyerInformation::allLawyer();
        $this->lawyer = $lawyer;
        $type = LawyerBillServiceCommonModel::getType();
        $this->type = $type;
        // 加载对应数据
        $where = [];

        $bill_date = $this->request->get('bill_date', '');
        if(!empty($bill_date)){
            $time_arr = explode(' - ',$bill_date);
            $where[] = ['bill_date','>=',$time_arr[0]];
            $where[] = ['bill_date','<=',$time_arr[1]];
        }

        $lawyer_information_id = $this->request->get('lawyer_information_id', '');
        $type_id = $this->request->get('type_id', '');

        if(!empty($lawyer_information_id)){
            $where[] = ['lawyer_information_id','=',$lawyer_information_id];
        }

        if(!empty($type_id)){
            $where[] = ['type_id','=',$type_id];
        }

        $query = $this->_query($this->table)->where($where)->order('id DESC');
        $query->page();
    }

    protected function _index_page_filter(&$data){
        $lawyer_information_ids = array_unique(array_column($data,'lawyer_information_id'));
        $lawyer = LawyerInformation::pluckAttrByIds($lawyer_information_ids,'id,name');
        $type = LawyerBillServiceCommonModel::getType();
        $data = array_map(function($item) use($lawyer,$type) {
            $item['lawyer_name'] = isset($lawyer[$item['lawyer_information_id']])?   $lawyer[$item['lawyer_information_id']]['name'] : '';
            $item['type_name'] = isset($type[$item['type_id']])?   $type[$item['type_id']] : '';
            return $item;
        },$data);

    }

    /**
     * 添加服务账单
     * @auth true
     */
    public function add()
    {
        LawyerBillServiceModel::mForm('form2');
    }

    /**
     * 编辑服务账单
     * @auth true
     */
    public function edit()
    {
        $this->title = '编辑服务账单';
        $this->_form($this->table, 'form');
    }


    /**
     * 表单数据处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _edit_form_filter(array &$data)
    {   //dump($data);exit;
        $lawyer = LawyerInformation::allLawyer();
        $type = LawyerBillServiceCommonModel::getType();
        $type2 = LawyerBillServiceCommonModel::getType2();
        $this->type2 = $type2;
        $this->lawyer = $lawyer;
        $this->type = $type;
        if($this->request->isGet()){
            $this->title = '添加服务账单';
        }elseif($this->request->isPost()){
            if (empty($data['lawyer_information_id'])) $this->error('请选择律师！');
            if (empty($data['type_id'])) $this->error('请选择服务内容！');
            if (empty($data['bill_date'])) $this->error('请选择账单日期！');
        }
    }

    protected function _add_form_filter(array &$data)
    {
        $lawyer = LawyerInformation::allLawyer();
        $type = LawyerBillServiceCommonModel::getType();
        $type2 = LawyerBillServiceCommonModel::getType2();
        $this->type2 = $type2;
        $this->lawyer = $lawyer;
        $this->type = $type;
        if($this->request->isGet()){
            $this->title = '添加服务账单';
        }elseif($this->request->isPost()){
            if (empty($data['lawyer_information_id'])) $this->error('请选择律师！');

            if (empty($data['bill_date'])) $this->error('请选择账单日期！');

            foreach($data['fuwu'] as $key=> $item){
                if(empty($item)){
                    unset($data['fuwu'][$key]);
                    continue;
                }

                $newData[$key]['lawyer_information_id'] = $data['lawyer_information_id'];
                $newData[$key]['bill_date'] = $data['bill_date'];
                $newData[$key]['type_id'] = $item;
                $newData[$key]['num'] = $data['num'][$item];
                $newData[$key]['price'] = $type2[$item]['price'];
                $newData[$key]['create_time'] = date('Y-m-d H:i:s',time());
                $newData[$key]['update_time'] =date('Y-m-d H:i:s',time());

            }

            $res =  $this->app->db->name('lawyer_bill_service')->insertAll($newData);
            if(!$res){
                $this->error('添加服务失败！');
            }
            $this->success('服务账单数据成功！', 'javascript:history.back()');

        }
    }



    /**
     * 表单结果处理
     * @param boolean $result
     */
    protected function _form_result(bool $result,array $data)
    {
        // 这里可以获取到数据记录ID
        //  echo $data['id']
        if ($result && $this->request->isPost()) {
            $this->success('服务账单数据成功！', 'javascript:history.back()');
        }
    }


}
