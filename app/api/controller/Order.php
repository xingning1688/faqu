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

namespace app\api\controller;

use app\admin\model\OrderContractDetail;
use app\api\model\Jwt;
use think\admin\Controller;
use think\facade\Db;
use app\api\model\LawyerInformations;
use app\api\model\LawyerCase;
use app\api\model\OrderContract as OrderContracts;
use app\common\model\Order as OrderModel;


class Order  extends AuthController
//class Order  extends Controller
{

    //创建订单信息
    public function createOrder()
    {
        //获取订单信息
        //file_put_contents('./text.txt', '接收信息：'.var_export( request()->all(),true)."\r\n",FILE_APPEND | LOCK_EX);

        //test
        //测试数据 start
       /* $order_detail=[['product_id'=>0,'price'=>0.00,'num'=>2,'product_name'=>'产品名称1','lawyer_user_id'=>10000],['product_id'=>0,'price'=>1.00,'num'=>3,'product_name'=>'产品名称2','lawyer_user_id'=>10000]];
        $order_detail = json_encode($order_detail);

        $order_consignee=['name'=>'name','phone'=>'13666666666','content'=>'content'];
        $order_consignee = json_encode($order_consignee);

        $parameter['order_detail'] =  json_decode($order_detail,true);
        $parameter['order_consignee'] =  json_decode($order_consignee,true);
        $parameter['open_id'] = 'open_id';
        $parameter['platform'] = 1;//platform              平台  [0=>'未知','1'=>'快手','2'=>'微信','3'=>'抖音']
        $parameter['order_type'] = 1;*/
        //测试数据 end

        //获取参数
        if(is_array(request()->param('order_detail'))){
            $parameter['order_detail'] =  request()->param('order_detail');
        }else{
            $parameter['order_detail'] =  json_decode(request()->param('order_detail'),true);
        }

        $order_consignee = request()->param('order_consignee','');
        if(!empty($order_consignee)){
            if(is_array($order_consignee)){
                $parameter['order_consignee'] =  $order_consignee;
            }else{
                $parameter['order_consignee'] =  json_decode($order_consignee,true);
            }
        }


        $parameter['open_id'] = request()->param('open_id');
        $parameter['pay_type'] = request()->param('pay_type');
        $parameter['platform'] = request()->param('platform');//platform              平台  [0=>'未知','1'=>'快手','2'=>'微信','3'=>'抖音']
        $parameter['order_type'] = request()->param('order_type');
        $parameter['type'] = request()->param('type',0);             

        //验证提交过来的数据
        if( (isset($parameter['pay_type']) && !is_numeric($parameter['pay_type']) ) || !is_numeric($parameter['platform']) || empty($parameter['open_id']) || empty($parameter['order_detail'])  /*|| empty($parameter['order_consignee'])*/){
            $this->error('参数不合法1');
        }

        if( empty($parameter['order_detail'])){
            $this->error('参数不合法2');
        }

        if(empty($parameter['type']) && empty($parameter['order_consignee'])){
            $this->error('参数不合法3');
        }


        $parameter['order_price'] = 0;
        foreach($parameter['order_detail'] as $key =>$value){
            $parameter['order_price'] += $value['price'] * $value['num'];
        }

        //添加数据库
        $res = OrderModel::createOrder($parameter);
        if($res === false){
            $this->error('订单失败');
        }

        $this->success('订单成功',$parameter);

    }


    //支付成功后，查询某个订单
    public function getByIdOrder(){
        $oid = request()->param('id');
        $order = OrderModel::getOrderDetailById($oid);//获取数据
        if(empty($order)){
            $this->error('失败，暂无数据');
        }
        $this->success('订单成功',$order);
    }

    public function getByIdOrder2(){
        $oid = request()->param('id',4);
        $order = OrderModel::getOrderDetailById2($oid);//获取数据
        if(empty($order)){
            $this->error('失败，暂无数据');
        }
        $this->success('订单成功',$order);
    }


}