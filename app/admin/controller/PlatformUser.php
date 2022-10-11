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
 * 平台用戶管理
 * @auth true  # 表示需要验证权限
 * @menu true  # 添加系统菜单节点
 * @login true # 强制登录才可访问
 */
class PlatformUser extends Controller
{
    private $table = 'platform_user';
    public $gender = [0=>'未知','1'=>'男','2'=>'女'];
    public $platform = [0=>'未知','1'=>'快手','2'=>'微信','3'=>'抖音'];
    /**
     * 平台用户列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {

        //打印 auth 权限试试
        //获取session
        $this->title = '用户列表';
        $this->gender;
        $this->platform;

        $where = [];
        //$status = $this->request->get('status', '');
        $nick_name = $this->request->get('nick_name', '');
        $phone = $this->request->get('phone', '');
        $gender = $this->request->get('gender', '');
        $platform = $this->request->get('platform', '');
        $create_time = $this->request->get('create_time', '');

        if(!empty($nick_name)){
            $where[] = ['nick_name','=',$nick_name];
        }

        if(!empty($phone)){
            $where[] = ['phone','=',$phone];
        }

        if($gender !=='' && in_array($gender,[0,1,2])){
            $where[] = ['gender','=',$gender];
        }

        if($platform !=='' && in_array($platform,[0,1,2,3])){
            $where[] = ['platform','=',$platform];
        }

        
        if(!empty($create_time)){
            $time_arr = explode(' - ',$create_time);
            $where[] = ['create_time','>=',strtotime($time_arr[0])];
            $where[] = ['create_time','<=',strtotime($time_arr[1])];
        }

        $query = $this->_query($this->table)->where($where)->order('id DESC');
        //dump(11);
        //echo $query->db()->buildSql();exit;
        $query->page();
    }

    protected function _index_page_filter(&$data){
        $gender = $this->gender;
        $platform = $this->platform;
        $data = array_map(function($item) use($gender,$platform){
             //dump($item['gender'],$gender);exit;
            $item['gender'] = isset($gender[$item['gender']]) ? $gender[$item['gender']]:'';
            $item['platform'] = isset($platform[$item['platform']]) ? $platform[$item['platform']]:'';
            //dump($item);exit;
            return $item;
        },$data);

         return $data;


    }


}
