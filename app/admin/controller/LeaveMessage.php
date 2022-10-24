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
 * 律师咨询管理
 * @auth true  # 表示需要验证权限
 * @menu true  # 添加系统菜单节点
 * @login true # 强制登录才可访问
 */
class LeaveMessage extends Controller
{
    private $table = 'leave_message';
    public $status = ['-1'=>'搁置',0=>'未处理','1'=>'助理确认','2'=>'律师沟通','3'=>'沟通结束'];
    public $platform = [0=>'未知','1'=>'快手','2'=>'微信','3'=>'抖音'];
    /**
     * 律师咨询列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {

        //打印 auth 权限试试
        //获取session
        $this->title = '律师咨询列表';
        $this->status;
        $this->platform;

        $where = [];
        $status = $this->request->get('status', '');
        $phone = $this->request->get('phone', '');
        $wx_num = $this->request->get('wx_num', '');
        $platform = $this->request->get('platform', '');

        $create_time = $this->request->get('create_time', '');

        if(!empty($phone)){
            $where[] = ['phone','=',$phone];
        }

        if($status!==''){
            $where[] = ['status','=',$status];
        }

        if(!empty($wx_num)){
            $where[] = ['wx_num','=',$wx_num];
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

        //echo $query->db()->buildSql();exit;
        $query->page();
    }

    protected function _index_page_filter(&$data){
        $status = $this->status;
        $platform = $this->platform;
        $lawyer_user_ids = array_unique(array_column($data,'lawyer_user_id'));
        $lawyerInformations =    LawyerInformations::getByUserIds($lawyer_user_ids);
        $open_ids = array_column($data,'open_id');
        $platform_user = $this->app->db->name('platform_user')->whereIn('open_id',$open_ids)->column('id,open_id,nick_name','open_id');
        $data = array_map(function($item) use($status,$platform,$lawyerInformations,$platform_user){
             //dump($item['gender'],$gender);exit;
            $item['status'] = isset($status[$item['status']]) ? $status[$item['status']]:'';
            $item['platform'] = isset($platform[$item['platform']]) ? $platform[$item['platform']]:'';
            $item['lawyer_name'] = isset($lawyerInformations[$item['lawyer_user_id']])?$lawyerInformations[$item['lawyer_user_id']]['name'] : '';
            $item['nick_name'] = isset($platform_user[$item['open_id']]) ? $platform_user[$item['open_id']]['nick_name'] : '';
            return $item;
        },$data);
        //dump($data);exit;
         return $data;


    }

    /**
     * 审核律师咨询信息
     *  @auth true
     * time  :
     */
    public function appoint() {
        $this->title = '审核律师咨询信息';
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


    /**
     * 备注律师咨询信息
     *  @auth true
     * time  :
     */
    public function remark() {
        $this->title = '备注信息';
        $this->status;

        $this->_form($this->table, 'remark');
    }
    protected function _remark_form_filter(&$data) {

        if($this->request->isPost()) {
            $data['update_time'] = time();
        }
    }



}
