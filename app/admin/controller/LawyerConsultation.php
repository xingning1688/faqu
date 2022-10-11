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
 * 专项咨询管理
 * @auth true  # 表示需要验证权限
 * @menu true  # 添加系统菜单节点
 * @login true # 强制登录才可访问
 */
class LawyerConsultation extends Controller
{
    private $table = 'lawyer_consultation';
    public $status = [0=>'待审核','1'=>'审核成功','2'=>'审核失败'];
    /**
     * 专项咨询列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {
        $this->title = '专项咨询';
        // 加载对应数据
        $where = [];
        $status = $this->request->get('status', '');
        $name = $this->request->get('name', '');
        $consultation_title = $this->request->get('consultation_title', '');
        $create_time = $this->request->get('create_time', '');

        $user_data =  Db::table('system_user')->whereIn('username',$name)->column('username','id');
        if(!empty($user_data)){
            $userIds = array_keys($user_data);
            $where[] = ['user_id','in',$userIds];
        }

        if($status !=='' && in_array($status,[0,1,2])){
            $where[] = ['status','=',$status];
        }
        //dump(1,$consultation_title);exit;
        if(!empty($consultation_title)){
            $where[] = ['consultation_title','=',$consultation_title];
        }


        if(!empty($create_time)){
            $time_arr = explode(' - ',$create_time);
            $where[] = ['create_time','>=',strtotime($time_arr[0])];
            $where[] = ['create_time','<=',strtotime($time_arr[1])];
        }
        $user = Session::all();
        if(isset($user['user']['id']) && !empty($user['user']['id'])){
            $where[] = ['user_id','=',$user['user']['id']];
        }

        //dump($where);exit;
        $query = $this->_query($this->table)->where($where)->order('sort DESC,id DESC');
        //dump($query);exit;
        $query->page();
    }

    protected function _index_page_filter(&$data){
        $this->status;
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
    }

    /**
     * 添加专项咨询
     * @auth true
     */
    public function add()
    {
        SystemUser::mForm('form');
    }

    /**
     * 编辑专项咨询
     * @auth true
     */
    public function edit()
    {
        $this->title = '编辑律师案例';
        $this->_form($this->table, 'form');
    }

    //编辑保存
    protected function _edit_form_filter(&$data) {
        if(empty($data['id'])) {
            $this->error('参数错误！');
        }

        if ($this->request->isPost()) {
            $validate = new \app\admin\validate\LawyerConsultation();
            $returnVal = $validate->form($data);
            if($returnVal != ''){
                $this->error($returnVal);
            }

            if(!isset($data['price']) || $data['price']<0) {
                $this->error('请填写价格！');
            }


            $time = time();
            $data['status']       = 0;
            $data['update_time']       = $time;


            try {
                $this->app->db->name($this->table)->where('id', $data['id'])->update($data);
            } catch (Exception $e) {
                $this->error('服务器繁忙，请稍后重试！' . $e->getMessage());
            }

            $this->redirect('/admin/lawyer_consultation/index','301');
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
            $validate = new \app\admin\validate\LawyerConsultation();
            $returnVal = $validate->form($data);
            if($returnVal != ''){
                $this->error($returnVal);
            }

            if(!isset($data['consultation_title']) || $data['consultation_title']=='') {
                $this->error('请填写专项咨询！');
            }

            if(!isset($data['price']) || $data['price']<0) {
                $this->error('请填写合法价格！');
            }

            $user = Session::all();
            if(empty($user) || empty($user['user']['id'])){
                $this->error('用户不存在，请重新登录');
            }

            $data['consultation_title'] = htmlentities($data['consultation_title']);
            $time = time();
            $data['user_id'] = $user['user']['id'];
            $data['create_time']       = $time;
            $data['update_time']       = $time;


            try {
                $this->app->db->name($this->table)->insert($data);
            } catch (Exception $e) {
                $this->error('服务器繁忙，请稍后重试！' . $e->getMessage());
            }

            //$this->success('操作成功，请耐心等待管理员审核！');
            $this->redirect('/admin/lawyer_consultation/index','301');

        }



    }



    /**
     * 审核专项咨询
     * @auth true
     *
     * time  :
     */
    public function appoint() {
        $this->title = '审核案例信息';
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
