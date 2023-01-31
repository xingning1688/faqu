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
use app\common\model\OrderDetail;
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\admin\model\SystemAuth;
use think\admin\model\SystemBase;
use think\admin\model\SystemUser;
use think\admin\service\AdminService;
use think\model\Relation;
use think\facade\Session;
use think\facade\Db;
use app\common\model\Order as OrderModel;



/**
 * 订单服务管理
 * @auth true  # 表示需要验证权限
 * @menu true  # 添加系统菜单节点
 * @login true # 强制登录才可访问
 */
class Order extends Controller
{
    private $table = 'order';
    public $status = [0=>'未处理',1=>'已处理','-1'=>'搁置'];
    public $pay_status = [0=>'未支付',1=>'支付成功',2=>'支付失败'];
    public $pay_type = [0=>'暂无',1=>'微信',2=>'支付宝',10=>'快手-未知',11=>'快手-微信',12=>'快手-支付宝',20=>'微信小程序支付',30=>'抖音-未知',31=>'抖音-微信',32=>'抖音-支付宝',33=>'抖音支付',];
    public $platform = [0=>'未知','1'=>'快手','2'=>'微信','3'=>'抖音'];
    /**
     *订单服务列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {

        $this->title = '订单服务列表';
        $this->status;
        $this->pay_status;
        $this->pay_type;
        $this->platform;

        $where = [];
        $order_no = $this->request->get('order_no', '');
        $transaction_id = $this->request->get('transaction_id', '');
        $status = $this->request->get('status', '');
        $pay_status = $this->request->get('pay_status', '');
        $platform = $this->request->get('platform', '');
        $create_time = $this->request->get('create_time', '');
        $product_name = $this->request->get('product_name', '');

        if(!empty($order_no)){
            $where[] = ['order_no','=',$order_no];
        }

        if(!empty($transaction_id)){
            $where[] = ['transaction_id','=',$transaction_id];
        }

        if($status!==''){
            $where[] = ['status','=',$status];
        }

        if($pay_status!==''){
            $where[] = ['pay_status','=',$pay_status];
        }

        if($platform !=='' && in_array($platform,[0,1,2,3])){
            $where[] = ['platform','=',$platform];
        }

        if(!empty($create_time)){
            $time_arr = explode(' - ',$create_time);
            $where[] = ['create_time','>=',strtotime($time_arr[0])];
            $where[] = ['create_time','<=',strtotime($time_arr[1])];
        }

        if(!empty($product_name)){
            $where2[] = ['product_name','like','%'.$product_name.'%'];
            $res  = Db::name('order_detail')->where($where2)->select()->toArray();
            if(!empty($res)){
                $order_ids = array_unique(array_column($res,'order_id'));
                $where[] = ['id','in',$order_ids];
            }
        }


        $query = $this->_query($this->table)->where($where)->order('id DESC');
        $query->page();
    }

    protected function _index_page_filter(&$data){
        //获取订单详情
        $order_ids = array_unique(array_column($data,'id'));
        $order_details = OrderDetail::getOrderDetails($order_ids);
        $status = $this->status;
        $pay_status = $this->pay_status;
        $pay_type = $this->pay_type;
        $platform = $this->platform;

        $open_ids = array_column($data,'open_id');

        $platform_user = $this->app->db->name('platform_user')->whereIn('open_id',$open_ids)->column('id,open_id,nick_name','open_id');
        $data = array_map(function($item) use($order_details,$status,$pay_status,$pay_type,$platform,$platform_user){
            $item['order_details'] = isset($order_details[$item['id']]) ? $order_details[$item['id']]: [];
            $item['status'] = isset($status[$item['status']]) ? $status[$item['status']] : '';
            $item['pay_status'] = isset($pay_status[$item['pay_status']]) ? $pay_status[$item['pay_status']] : '';
            $item['pay_type'] = isset($pay_type[$item['pay_type']]) ? $pay_type[$item['pay_type']] : '';
            $item['platform'] = isset($platform[$item['platform']]) ? $platform[$item['platform']] : '';
            $item['pay_time'] = !empty($item['pay_time'])? date('Y-m-d H:i:s',$item['pay_time']) : '';
            $item['nick_name'] = isset($platform_user[$item['open_id']]) ? $platform_user[$item['open_id']]['nick_name'] : '';

            return $item;
        },$data);
         return $data;
    }

    /**
     * 订单详情信息
     *  @auth true
     * time  :
     */
    public function detail(){
        $this->title = '订单详情';
        $order_id = $this->request->param('id/d', 0);
        if(empty($order_id)) {
            $this->error('参数错误1！');
        }

        $order = OrderModel::getOrderDetailById($order_id);
        $this->assign('order', $order);
        return $this->fetch();
    }

    /**
     * 审核订单信息
     *  @auth true
     * time  :
     */
    public function appoint() {
        $this->title = '审核订单信息';
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
