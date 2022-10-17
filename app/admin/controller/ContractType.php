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
 * 合同类型管理
 * @auth true  # 表示需要验证权限
 * @menu true  # 添加系统菜单节点
 * @login true # 强制登录才可访问
 */
class ContractType extends Controller
{
    private $table = 'contract_type';
    public $status = [0=>'启用','1'=>'禁用'];
    /**
     * 合同类型列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {

        $this->title = '合同类型列表';
        $this->status;
        // 加载对应数据

       $where = [];
       $professional = $this->request->get('type_name', '');
        $status = $this->request->get('status', '');



        if(!empty($status !=='') && in_array($status,[0,1])){
            $where[] = ['status','=',$status];
        }

        if(!empty($professional)){
            $where[] = ['type_name','=',$professional];
        }

        $query = $this->_query($this->table)->where($where)->order('sort DESC,id DESC');

        $query->page();
    }

   /* protected function _index_page_filter(&$data){
        $userIds = [];
        foreach($data as $key=>$item){
            $userIds[] = $item['user_id'];
        }

        $user_data =  Db::table('system_user')->whereIn('id',$userIds)->column('username','id');

        $data = array_map(function($item) use($user_data){
            $item['user_name'] = isset($user_data[$item['user_id']])?$user_data[$item['user_id']]:'';
            return $item;
        },$data);

        return $data;



        // 这里可以对 $data 进行二次处理，注意是引用
    }*/

    /**
     * 添加合同类型
     * @auth true
     */
    public function add()
    {
        SystemUser::mForm('form');
    }

    /**
     * 编辑合同类型
     * @auth true
     */
    public function edit()
    {
        $this->title = '编辑合同类型';
        $this->_form($this->table, 'form');
    }

    //编辑保存
    protected function _edit_form_filter(&$data) {
        if(empty($data['id']) || !is_numeric($data['id'])) {
            $this->error('参数错误！');
        }

        if(empty($data['type_name'])){
            $this->error('合同类型不能为空');
        }


        if ($this->request->isPost()) {
            $time = time();
            $data['update_time']       = $time;

            try {
                $this->app->db->name($this->table)->where('id', $data['id'])->update($data);
            } catch (\Exception $e) {
                $this->error('服务器繁忙，请稍后重试！' . $e->getMessage());
            }

            $this->redirect('/admin/contract_type/index','301');
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
            if(empty($data['type_name'])){
                $this->error('合同类型不能为空');
            }

            $time = time();
            $data['create_time']       = $time;
            $data['update_time']       = $time;


            try {
                $this->app->db->name($this->table)->insert($data);
            } catch (\Exception $e) {
                $this->error('服务器繁忙，请稍后重试！' . $e->getMessage());
            }

            $this->redirect('/admin/contract_type/index','301');
        }
    }


    /**
     * 是否启用
     * @auth true
     * time  :
     */
    public function appoint() {
        $this->title = '合同类型状态管理';
        $this->status;

        $this->_form($this->table, 'appoint');
    }
    protected function _appoint_form_filter(&$data) {

        if($this->request->isPost()) {
            $this->_vali([
                'id.require'         => '参数错误1！',
                'status.require' => '请选择状态！'
            ]);
            $data['update_time'] = time();
        }
    }





}
