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
use app\admin\model\Script as ScriptModel;

/**
 * 脚本管理
 * @auth true  # 表示需要验证权限
 * @menu true  # 添加系统菜单节点
 * @login true # 强制登录才可访问
 */
class Script extends Controller
{
    private $table = 'script';
    public $status = [0=>'待拍摄','1'=>'已拍摄'];
    /**
     * 脚本列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {
        $this->title = '脚本列表';
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

        if(!empty($lawyer_information_id)){
            $where[] = ['lawyer_information_id','=',$lawyer_information_id];
        }


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
     * 添加脚本
     * @auth true
     */
    public function add()
    {
        ScriptModel::mForm('form');
    }

    /**
     * 编辑脚本
     * @auth true
     */
    public function edit()
    {
        $this->title = '编辑脚本';
        $this->_form($this->table, 'form');
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
        $type = LawyerBillServiceCommonModel::getType();
        $this->lawyer = $lawyer;
        $this->type = $type;
        if($this->request->isGet()){
            $this->title = '添加脚本';
        }elseif($this->request->isPost()){
            if (empty($data['lawyer_information_id'])) $this->error('请选择律师！');
            if (empty($data['content'])) $this->error('请填写脚本内容！');
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
            $this->success('脚本数据成功！', 'javascript:history.back()');
        }
    }


}
