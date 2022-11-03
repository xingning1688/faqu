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
 * 问答管理
 * @auth true  # 表示需要验证权限
 * @menu true  # 添加系统菜单节点
 * @login true # 强制登录才可访问
 */
class Question extends Controller
{
    private $table = 'question';
    public $recommend = [0=>'非推荐','1'=>'推荐'];
    /**
     * 问答列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {

        $this->title = '问答列表';
        $this->recommend;
        $this->question_classification = $this->app->db->name('question_classification')->where('is_delete', '0')->where('status',0)->order('sort DESC, id DESC')->field('id,classification_name')->select()->toArray();   // 资讯分类
        //dump($this->question_classification);exit;
        // 加载对应数据

       $where = [];
       $title = $this->request->get('title', '');
       $question_classification_id = $this->request->get('question_classification_id', '');
       $is_recommend = $this->request->get('is_recommend', '');
       //dump($question_classification_id);
        //$status = 0;

        $where[] = ['is_delete','=',0];
        /*if(!empty($status !=='') && in_array($status,[0,1])){
            $where[] = ['status','=',$status];
        }*/

        if(!empty($question_classification_id)){
            $where[] = ['question_classification_id','=',$question_classification_id];
        }

        if($is_recommend !== ''){
            $where[] = ['is_recommend','=',$is_recommend];
        }



        if(!empty($title)){
            $where[] = ['title','=',$title];
        }

        $query = $this->_query($this->table)->where($where)->order('id DESC');
        $query->page();
    }


    /**
     * 添加问答内容
     * @auth true
     */
    public function add()
    {

        $this->_form($this->table, 'form');
    }

    /**
     * 编辑问答内容
     * @auth true
     */
    public function edit()
    {
        $this->title = '编辑问答内容';
        $this->_form($this->table, 'form');
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
            $this->success('成功！', 'javascript:history.back()');

        }
    }

    //编辑保存
    protected function _edit_form_filter(&$data) {
        if(empty($data['id']) || !is_numeric($data['id'])) {
            $this->error('参数错误！');
        }

        if(empty($data['question_classification_id'])){
            $this->error('问答分类不能为空');
        }

        $this->question_classification = $this->app->db->name('question_classification')->where('is_delete', '0')->where('status',0)->order('sort DESC, id DESC')->field('id,classification_name')->select();   // 资讯分类
        $users = [];
        $lawyers = $this->app->db->name('lawyer_information')->field('id,user_id')->select()->toArray();
        if(!empty($lawyers)){
            $userIds = array_unique(array_column($lawyers,'user_id'));
            $users = $this->app->db->name('system_user')->whereIn('id',$userIds)->column('username,nickname','id');
        }

        $this->users = $users;
        $this->lawyers = $lawyers;

    }



    //处理分页后的数据
    protected function _page_filter(&$data){
        $question_classification_ids = array_column($data,'question_classification_id');
        $question_classification = $this->app->db->name('question_classification')->where('is_delete', '0')->where('status',0)->whereIn('id',$question_classification_ids)->order('sort DESC, id DESC')->column('id,classification_name','id');   // 资讯分类

        $data = array_map(function($item) use($question_classification){
            $item['question_classification_name'] = isset($question_classification[$item['question_classification_id']])? $question_classification[$item['question_classification_id']]['classification_name'] : '';
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
            if(empty($data['question_classification_id'])){
                $this->error('问答分类不能为空');
            }

            $time = time();
            $data['create_time']       = $time;
            $data['update_time']       = $time;

        }else{
            $this->question_classification = $this->app->db->name('question_classification')->where('is_delete', '0')->where('status',0)->order('sort DESC, id DESC')->field('id,classification_name')->select();   // 资讯分类
            $users = [];
            $lawyers = $this->app->db->name('lawyer_information')->field('id,user_id')->select()->toArray();
            if(!empty($lawyers)){
                $userIds = array_unique(array_column($lawyers,'user_id'));
                $users = $this->app->db->name('system_user')->whereIn('id',$userIds)->column('username,nickname','id');
               /* dump($userIds,$users);exit;
                $users = $this->app->db->name('system_user')->whereIn('id',$userIds)->column(['id,username,nickname'],'id');
                dump($users);exit;*/
            }

            $this->users = $users;
            $this->lawyers = $lawyers;

        }
    }


    /**
     * 是否推荐
     * @auth true
     * time  :
     */
    public function recommend() {
        $data = $this->request->all();
        if(!isset($data['is_recommend']) && !in_array($data['is_recommend'],[0,1])){
            $this->error('数据不合法');
        }

        $data['is_recommend'] =  ($data['is_recommend'] == 1) ? 0 : 1;
        $this->_save($this->table, ['is_recommend' => $data['is_recommend'],'update_time'=>time()]);
    }
    protected function _recommend_form_filter(&$data) {

        if($this->request->isPost()) {
            $this->_vali([
                'id.require'         => '参数错误1！',
                'is_recommend.require' => '请选择状态！'
            ]);
            $data['update_time'] = time();
        }
    }

    /**
     * 删除问答内容
     * @auth true
     * @login true
     */
    public function remove() {
        $this->_applyFormToken();
        $this->_save($this->table, ['is_delete' => 1,'delete_time'=>time()]);
    }








}
