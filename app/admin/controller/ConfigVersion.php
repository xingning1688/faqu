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
 * 版本配置
 * @auth true  # 表示需要验证权限
 * @menu true  # 添加系统菜单节点
 * @login true # 强制登录才可访问
 */
class ConfigVersion extends Controller
{
    private $table = 'config_version';

    /**
     * 版本配置列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {
        $this->title = '版本配置';
        $query = $this->_query($this->table)->order('id DESC');
        $query->page();
    }

    /**
     * 编辑版本配置
     * @auth true
     */
    public function edit()
    {
        $this->title = '编辑版本配置';
        $this->_form($this->table, 'form');
    }

    //编辑保存
    protected function _edit_form_filter(&$data) {
        if(empty($data['id']) || !is_numeric($data['id'])) {
            $this->error('参数错误！');
        }

        if(!is_numeric($data['version_number'])){
            $this->error('版本号不合法');
        }


        if ($this->request->isPost()) {
            $data['update_time'] = date('Y-m-d H:i:s',time());

            try {
                $this->app->db->name($this->table)->where('id', $data['id'])->update($data);
            } catch (\Exception $e) {
                $this->error('服务器繁忙，请稍后重试！' . $e->getMessage());
            }

        }

    }


}
