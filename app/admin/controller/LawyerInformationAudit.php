<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2022 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: https://thinkadmin.top
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// | 免费声明 ( https://thinkadmin.top/disclaimer )
// +----------------------------------------------------------------------
// | gitee 代码仓库：https://gitee.com/zoujingli/ThinkAdmin
// | github 代码仓库：https://github.com/zoujingli/ThinkAdmin
// +----------------------------------------------------------------------

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

/**
 * 律师信息审核
 * @auth true  # 表示需要验证权限
 * @menu true  # 添加系统菜单节点
 * @login true # 强制登录才可访问
 */
class LawyerInformationAudit extends Controller
{
    private $table = 'lawyer_information';
    public $status = [0=>'待审核','1'=>'审核成功','2'=>'审核失败'];
    public $recommends = [0=>'未推荐','1'=>'推荐'];

    /**
     * 审核律师管理
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {

        $this->title = '律师列表';
        $this->status;
        $this->recommends;

        $where = [];
        $status = $this->request->get('status', '');
        $name = $this->request->get('name', '');
        $create_time = $this->request->get('create_time', '');

        if(!empty($name)){
            $where[] = ['name','=',$name];

        }

        if($status !=='' && in_array($status,[0,1,2])){
            $where[] = ['status','=',$status];
        }

        if(!empty($create_time)){
            $time_arr = explode(' - ',$create_time);
            $where[] = ['create_time','>=',strtotime($time_arr[0])];
            $where[] = ['create_time','<=',strtotime($time_arr[1])];
        }

        $query = $this->_query($this->table)->where($where)->order('sort DESC,id DESC');

        //echo $query->db()->buildSql();exit;
        $query->page();
    }

    /*protected function _index_page_filter(&$data){



    }*/


    /**
     * 添加审核律师
     * @auth true
     */
    public function add()
    {
        SystemUser::mForm('form');
    }

    /**
     * 审核编辑律师
     * @auth true
     */
    public function edit()
    {
        $this->title = '编辑律师';
        $this->_form($this->table, 'form');
    }

    //编辑保存
    protected function _edit_form_filter(&$data) {
        if(empty($data['id'])) {
            $this->error('参数错误！');
        }

        if ($this->request->isPost()) {
            $validate = new \app\admin\validate\LawyerInformation();
            $returnVal = $validate->form($data);
            if($returnVal != ''){
                $this->error($returnVal);
            }
            if(empty($data['professional_field_id'])){
                $this->error('请选择专业领域');
            }

            $time = time();
            $data['professional_field_id'] = ','.(implode(',',$data['professional_field_id'])).',';
            $data['update_time']       = $time;
            //$data['status']       = 0;

            try {
                $this->app->db->name($this->table)->where('id', $data['id'])->update($data);
            } catch (Exception $e) {
                $this->error('服务器繁忙，请稍后重试！' . $e->getMessage());
            }

            $this->redirect('/admin/lawyer_information_audit/index','301');

        }else{
            $professional_field_ids = explode(',',$data['professional_field_id']);
            $this->professional_field_ids = $professional_field_ids;

            $professional_data =  Db::table('lawyer_professional')->where('status',0)->select()->toArray();
            $this->type_arr = $professional_data;
            /*echo "<pre>";
            print_r($data['professional_field_ids']);exit;*/
        }

    }

    //处理分页后的数据
    protected function _page_filter(&$data){
        $data = array_map(function($item){
         $item['status'] = $this->status[ $item['status']];
            return  $item;
        },$data);
        return $data;
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


        if ($this->request->isPost()) {


            $validate = new \app\admin\validate\LawyerInformation();
            $returnVal = $validate->form($data);
            if($returnVal != ''){
                $this->error($returnVal);
            }

            if(empty($data['professional_field_id'])){
                $this->error('请选择专业领域');
            }



            $user = Session::all();
            if(empty($user) || empty($user['user']['id'])){
                $this->error('用户不存在，请重新登录');
            }


            $time = time();
            $data['professional_field_id'] = ','.(implode(',',$data['professional_field_id'])).',';
            $data['user_id'] = $user['user']['id'];
            $data['create_time']       = $time;
            $data['update_time']       = $time;
            /*echo "<pre>";
            print_r();exit;*/

            $row = $this->app->db->name($this->table)->where('user_id',$data['user_id'])->find();
            if($row){
                $this->error('用户信息已存在，只能重新编辑');
            }

            try {
                $this->app->db->name($this->table)->insert($data);
            } catch (Exception $e) {
                $this->error('服务器繁忙，请稍后重试！' . $e->getMessage());
            }

            //$this->success('操作成功，请耐心等待管理员审核！');
            $this->redirect('/admin/lawyer_information/index','301');
        }else {
            //添加页面初始赋值变量
            $professional_data =  Db::table('lawyer_professional')->where('status',0)->select()->toArray();
            $this->type_arr = $professional_data;
        }

    }


    /**
     * 审核律师信息
     *  @auth true
     * time  :
     */
    public function appoint() {
        $this->title = '审核律师信息';
        $this->status;

        $this->_form($this->table, 'appoint');
    }
    protected function _appoint_form_filter(&$data) {

        //var_dump($data);exit;
        if($this->request->isPost()) {
            $this->_vali([
                'id.require'         => '参数错误1！',
                'status.require' => '请选择状态！'
            ]);
            $data['update_time'] = time();

            /*try {
                $this->app->db->name($this->table)->where('id', $data['id'])->update($data);
            } catch (Exception $e) {
                $this->error('服务器繁忙，请稍后重试！' . $e->getMessage());
            }*/
            //var_dump(11);exit;
            //$this->redirect('/admin/lawyer_information/index','301');

        }
    }


    public function recommend_sure() {
        $this->_applyFormToken();
        $this->_save($this->table, ['is_recommend' => 1,'update_time'=>time()]);
    }

    public function recommend_cancel() {
        $this->_applyFormToken();
        $this->_save($this->table, ['is_recommend' => 0,'update_time'=>time()]);
    }







    /**
     * 修改用户状态
     *
     */
    public function state()
    {
        $this->_checkInput();
        SystemUser::mSave($this->_vali([
            'status.in:0,1'  => '状态值范围异常！',
            'status.require' => '状态值不能为空！',
        ]));
    }

    /**
     * 删除系统用户
     *
     */
    public function remove()
    {
        $this->_checkInput();
        SystemUser::mDelete();
    }


}
