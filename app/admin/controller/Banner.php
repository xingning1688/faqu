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

use app\api\model\LawyerInformations;
use app\admin\model\OrderContractDetail;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\admin\model\SystemAuth;
use think\admin\model\SystemBase;
use think\admin\model\SystemUser;
use think\admin\service\AdminService;
use think\Exception;
use think\model\Relation;
use think\facade\Session;
use think\facade\Db;
use app\admin\model\Banner as BannerModel;


/**
 * 轮播图管理
 * @auth true  # 表示需要验证权限
 * @menu true  # 添加系统菜单节点
 * @login true # 强制登录才可访问
 */
class Banner extends Controller{
    private $table = 'banner';
    public $status = [0=>'禁用',1=>'启用'];

    /**
     *轮播列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {

        $this->title = '轮播图列表';
        $this->status;


        $where = [];
        $status = $this->request->get('status', '');

        if($status!==''){
            $where[] = ['status','=',$status];
        }


        $query = $this->_query($this->table)->where($where)->order('id DESC');
        $query->page();
    }

    /**
     * 添加轮播图
     * @auth true
     */
    public function add()
    {
        SystemUser::mForm('form');
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

            if(empty($data['img_url'])) {
                $this->error('请上传轮播图片！');
            }


            $time = time();
            $data['img_url'] = $data['img_url'];
            $data['url'] = $data['url'];
            $data['create_time']       = $time;
            $data['update_time']       = $time;

            try {
                $this->app->db->name($this->table)->insert($data);
            } catch (\Exception $e) {
                $this->error('服务器繁忙，请稍后重试！' . $e->getMessage());
            }

            $this->redirect('index');
        }
    }

    /**
     * 编辑轮播图
     * @auth true
     */
    public function edit()
    {
        $this->title = '编辑轮播图';
        $this->_form($this->table, 'form');
    }

    //编辑保存
    protected function _edit_form_filter(&$data) {
        if(empty($data['id'])) {
            $this->error('参数错误！');
        }

        if ($this->request->isPost()) {
            if(empty($data['img_url'])) {
                $this->error('请上传轮播图片！');
            }

            $time = time();
            $data['img_url'] = $data['img_url'];
            $data['url'] = $data['url'];
            $data['update_time']       = $time;

            try {
                $this->app->db->name($this->table)->where('id', $data['id'])->update($data);
            } catch (Exception $e) {
                $this->error('服务器繁忙，请稍后重试！' . $e->getMessage());
            }

            $this->redirect('/admin/banner/index','301');
        }

    }

    /**
     * 轮播启用禁用
     *  @auth true
     * time  :
     */
    public function is_status() {
        $data = $this->request->all();
        if(!is_numeric($data['status']) && in_array($data['status'],[0,1])){
            $this->error('数据不合法1');
        }
        if(!is_numeric($data['id'])){
            $this->error('数据不合法2');
        }
        $updata['status'] = $data['status']==0 ? 1 : 0;
        $updata['id'] = $data['id'];

        try {
            $this->app->db->name($this->table)->where('id', $data['id'])->update($updata);
        } catch (Exception $e) {
            $this->error('服务器繁忙，请稍后重试！' . $e->getMessage());
        }

        $this->redirect('/admin/banner/index');

    }







}
