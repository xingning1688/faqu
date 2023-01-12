<?php


namespace app\admin\controller;

use app\common\model\DySmsApiModel;
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
use app\admin\model\CaseSourceSquare as CaseSourceSquareModel;

/**
 * 法律线索管理
 * @auth true  # 表示需要验证权限
 * @menu true  # 添加系统菜单节点
 * @login true # 强制登录才可访问
 */
class CaseSourceSquare extends Controller
{
    private $table = 'case_source_square';
    public $status = [0=>'待处理','1'=>'待接收','2'=>'已接收','3'=>'已完成'];
    public $is_shelves = [0=>'上架','1'=>'下架'];
    /**
     * 案源广场列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {
        $this->title = '法律线索列表';
        $lawyer = LawyerInformation::allLawyer();
        $this->lawyer = $lawyer;


        // 加载对应数据
        $where = [];

        /*$bill_date = $this->request->get('bill_date', '');
        if(!empty($bill_date)){
            $time_arr = explode(' - ',$bill_date);
            $where[] = ['bill_date','>=',$time_arr[0]];
            $where[] = ['bill_date','<=',$time_arr[1]];
        }*/

        $lawyer_information_id = $this->request->get('lawyer_information_id', '');
        $status = $this->request->get('status', '');
        $is_shelves = $this->request->get('is_shelves', '');
        $phone = $this->request->get('phone', '');

        if(!empty($lawyer_information_id)){
            $where[] = ['lawyer_information_id','=',$lawyer_information_id];
        }

        if($status!=''){
            $where[] = ['status','=',$status];
        }

        if($is_shelves!=''){
            $where[] = ['is_shelves','=',$is_shelves];
        }

        if($phone!=''){
            $where[] = ['phone','=',$phone];
        }


        //dump(22);exit;
        $query = $this->_query($this->table)->where($where)->order('id DESC');
        $query->page();
    }

    protected function _index_page_filter(&$data){
        $lawyer_information_ids = array_unique(array_column($data,'lawyer_information_id'));
        $lawyer = LawyerInformation::pluckAttrByIds($lawyer_information_ids,'id,name');
        $status = $this->status;
        $data = array_map(function($item) use($lawyer,$status) {
            $item['lawyer_name'] = isset($lawyer[$item['lawyer_information_id']])?   $lawyer[$item['lawyer_information_id']]['name'] : '';
            $item['status'] = isset($status[$item['status']])?   $status[$item['status']] : '';
            return $item;
        },$data);

    }

    /**
     * 添加法律线索
     * @auth true
     */
    public function add()
    {
        CaseSourceSquareModel::mForm('form');
    }

    /**
     * 编辑法律线索
     * @auth true
     */
    public function edit()
    {
        $this->title = '编辑法律线索';
        $this->status ;
        $this->_form($this->table, 'form');
    }

    public function show(){
        $this->title = '查看法律线索';
        $this->status;
        $this->_form($this->table, 'show');
    }


    /**
     * 表单数据处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _form_filter(array &$data)
    {
        $lawyer = LawyerInformation::allLawyer();
        $this->lawyer = $lawyer;
        $this-> is_shelves;
        if($this->request->isGet()){
            $this->title = '法律线索';
        }elseif($this->request->isPost()){
            if (empty($data['lawyer_information_id'])) $this->error('请选择律师！');
            if (empty($data['problem'])) $this->error('请填写问题内容！');
            if (empty($data['name'])) $this->error('请填写姓名！');
            if (empty($data['phone'])) $this->error('请填写联系电话！');
            if (empty($data['province'])) $this->error('请填选择省份！');
            if (empty($data['city'])) $this->error('请填选择城市！');
            if (empty($data['area'])) $this->error('请填选择区域！');

            if(!preg_match("/^1[3456789]\d{9}$/",$data['phone'])){
                $this->error('手机号不合法');
            }
            //$data['shelves_time'] = date('Y-m-d H:i:s',time());
        }
    }

    /**
     * 表单数据处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _add_form_filter(array &$data)
    {
        $lawyer = LawyerInformation::allLawyer();
        $this->lawyer = $lawyer;
        $this-> is_shelves;
        if($this->request->isGet()){
            $this->title = '法律线索';
        }elseif($this->request->isPost()){
            if (empty($data['lawyer_information_id'])) $this->error('请选择律师！');
            if (empty($data['problem'])) $this->error('请填写问题内容！');
            if (empty($data['name'])) $this->error('请填写姓名！');
            if (empty($data['phone'])) $this->error('请填写联系电话！');
            if (empty($data['province'])) $this->error('请填选择省份！');
            if (empty($data['city'])) $this->error('请填选择城市！');
            if (empty($data['area'])) $this->error('请填选择区域！');

            if(!preg_match("/^1[3456789]\d{9}$/",$data['phone'])){
                $this->error('手机号不合法');
            }

            $data['shelves_time'] = date('Y-m-d H:i:s',time());
            $data['status'] = 1;
        }
    }

    /**
     * 表单数据处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _edit_form_filter(array &$data)
    {
        $lawyer = LawyerInformation::allLawyer();
        $this->lawyer = $lawyer;
        $this-> is_shelves;
        if($this->request->isGet()){
            $this->title = '法律线索';
        }elseif($this->request->isPost()){
            if (empty($data['lawyer_information_id'])) $this->error('请选择律师！');
            if (empty($data['problem'])) $this->error('请填写问题内容！');
            if (empty($data['name'])) $this->error('请填写姓名！');
            if (empty($data['phone'])) $this->error('请填写联系电话！');
            if (empty($data['province'])) $this->error('请填选择省份！');
            if (empty($data['city'])) $this->error('请填选择城市！');
            if (empty($data['area'])) $this->error('请填选择区域！');

            if(!preg_match("/^1[3456789]\d{9}$/",$data['phone'])){
                $this->error('手机号不合法');
            }

        }
    }

    /**
     * 表单数据处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _show_form_filter(array &$data)
    {
        $lawyer = LawyerInformation::allLawyer();
        $this->lawyer = $lawyer;
        $this-> is_shelves;
        if($this->request->isGet()){
            $this->title = '法律线索';
        }

        //获取当前的 问题反馈详情
        $caseData = $this->app->db->name('case_source_square_detail')->where('case_source_square_id', $data['id'])->order('create_time','desc')->select();
        if(empty($caseData)){
            $data['case_data'] = [];
        }
        $data['case_data'] = $caseData->toArray();
        if(!empty($data['case_data'])){
            $status = $this->status;
            $type = [0 => '定时任务',1=> '律师操作',2=> '管理员操作'];

            $data['case_data'] = array_map(function($item) use($lawyer,$status,$type){
                $item['lawyer_user_name'] = isset($lawyer[$item['lawyer_information_id']])? $lawyer[$item['lawyer_information_id']] : '';
                $item['status'] = isset($status[$item['status']]) ? $status[$item['status']] : '';
                $item['type'] = isset($type[$item['type']]) ? $type[$item['type']] : '';
                return $item;
            },$data['case_data']);

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
            $this->success('法律线索数据成功！', 'javascript:history.back()');
        }
    }


    /**
     * 表单结果处理
     * @param boolean $result
     */
    protected function _add_form_result(bool $result,array $data)
    {
        // 这里可以获取到数据记录ID
        //  echo $data['id']
        if ($result && $this->request->isPost()) {
            //$res = DySmsApiModel::sendSms($data['lawyer_information_id']);
            $this->success('添加法律线索数据成功！', 'javascript:history.back()');
        }
    }


}
