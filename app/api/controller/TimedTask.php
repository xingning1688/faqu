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

use app\api\model\Jwt;
use app\api\model\LeaveMessages;
use app\api\model\PlatformUser;
use app\api\validate\Feedback as FeedbackValidate;
use app\common\model\PayKs;
use think\admin\Controller;
use think\facade\Db;
use app\api\model\Feedback as FeedbackModel;
use app\api\controller\Common as CommonController;
use app\api\model\LawyerCase;
use app\api\model\Banner;
use app\common\model\Order;
use app\api\model\OrderContract;
use app\common\model\LawyerInformation;
use app\common\model\LawyerBillService as LawyerBillServiceModel;
use app\common\model\Common;
use app\common\model\LeaveMessage;
use app\common\model\Product;
use app\common\model\CaseSourceSquare as CaseSourceSquareModel;





class TimedTask  extends Controller
{


    //定时任务 打回广场  （首先是接收的，）
    public function  backSquare(){

        //超过1h时间的 (状态是 已经分配律师的，并且是上架的)  ->whereTime('birthday', ['1970-10-1', '2000-10-1'])
        $date = date('Y-m-d H:i:s',time()-3600);
        $data = CaseSourceSquareModel::where('status',1)->where('is_shelves',0)->whereTime('shelves_time','<=',$date)->order('shelves_time','asc')->limit(50)->select()->toArray();

        if(empty($data)){
            $this->success('暂无数据');
        }

        foreach($data as $key=>$item){
            $caseData['lawyer_information_id'] = $item['lawyer_information_id'];
            $caseData['case_source_square_id'] = $item['id'];
            $caseData['description'] = '定时任务超时-后端触发';
            $caseData['type'] = 0;
            $caseData['status'] = 0;
            $res = CaseSourceSquareModel::unsolved($caseData);
            if($res === false){
                //$this->error('失败');
                continue;
            }
        }

        $this->success('成功');

    }



    //上传图片，同步订单时用到  每天一次
    public function syncKs(){
        $data = Product::where('img_id','')->where('cover','<>','')->limit(20)->field('id,img_id,cover')->select()->toArray();
        if(empty($data)){
            $this->success('暂无数据');
        }
        $payKs = new PayKs();
        foreach($data as $key=>$item){
            $res = $payKs->uploadWithUrl($item['cover']);
            if(!is_array($res)){
                continue;
            }
            $upData['img_id'] = $res['data']['imgId'];
            Product::where('id',$item['id'])->update($upData);
        }
        $this->success('ok');
    }

    // 同步订单 order 改变订单状态 为核销状态   每小时1次
    public function syncOrder(){
        $data = Order::where('platform',1)->where('pay_status',1)->whereNull('check_order_time')->limit(50)->select()->toArray();
        if(empty($data)){
            $this->success('暂无数据');
        }

        $payKs = new PayKs();
        foreach($data as $key=>$item){
            $order = Order::getOrderDetailById($item['id']);//获取数据
            if(empty($order['order_details'][0]['img_id'])){
                continue;
            }
            $res = $payKs->report($item['order_no'],$item['open_id'],strtotime($item['create_time']),$order['order_details'][0]['img_id']);
            if($res === true){
                $upData['check_order_time'] = date('Y-m-d H:i:s',time());
                Order::where('id',$item['id'])->update($upData);
            }
        }
        $this->success('ok');
    }

    //同步 合同订单 改变订单状态 为核销状态   每小时1次
    public function syncOrderContract(){
        $data = OrderContract::where('platform',1)->where('pay_status',1)->whereNull('check_order_time')->limit(50)->select()->toArray();
        if(empty($data)){
            $this->success('暂无数据');
        }

        $payKs = new PayKs();
        foreach($data as $key=>$item){

            $res = $payKs->report($item['order_no'],$item['open_id'],strtotime($item['create_time']),'5acfa39a90cc2513ad42de600cad6e9770083e8141ff5970');
            if($res === true){
                $upData['check_order_time'] = date('Y-m-d H:i:s',time());
                OrderContract::where('id',$item['id'])->update($upData);
            }
        }
        $this->success('ok');
    }

    //同步 合同订单 改变订单状态 为核销状态   每小时1次
    public function syncLeaveMessage(){
        $data = LeaveMessage::where('platform',1)->where('pay_status',1)->whereNull('check_order_time')->limit(50)->select()->toArray();
        if(empty($data)){
            $this->success('暂无数据');
        }

        $payKs = new PayKs();
        foreach($data as $key=>$item){

            $res = $payKs->report($item['order_no'],$item['open_id'],strtotime($item['create_time']),'5acfa2c190c32513ad42de600cad36922a0f3f8342fa5973');
            if($res === true){
                $upData['check_order_time'] = date('Y-m-d H:i:s',time());
                LeaveMessage::where('id',$item['id'])->update($upData);
            }
        }
        $this->success('ok');
    }



    //定时任务 申请结算（核销状态后 满足3天） 每4小时
    public function settleKs(){
        //超过1h时间的 (状态是 已经分配律师的，并且是上架的)
        $time = date('Y-m-d H:i:s',time()-73*3600);
        $data = Order::where('platform',1)->where('pay_status',1)->where('is_settle',0)->whereTime('check_order_time','<=',$time)->order('pay_time','asc')->limit(50)->select()->toArray();
        if(empty($data)){
            $this->success('暂无数据');
        }
        $payKs = new PayKs();
        foreach($data as $key=>$item){
            $res =$payKs->settle($item['order_no'],3);
            if($res !== true){
                continue;
            }
        }
        $this->success('ok');
    }

    //结算合同订单
    public function settleOrderContract(){
        //超过1h时间的 (状态是 已经分配律师的，并且是上架的)
        $time = date('Y-m-d H:i:s',time()-73*3600);
        $data = OrderContract::where('platform',1)->where('pay_status',1)->where('is_settle',0)->whereTime('check_order_time','<=',$time)->order('pay_time','asc')->limit(50)->select()->toArray();
        if(empty($data)){
            $this->success('暂无数据');
        }
        $payKs = new PayKs();
        foreach($data as $key=>$item){
            $res =$payKs->settle($item['order_no'],1);
            if($res !== true){
                continue;
            }
        }
        $this->success('ok');
    }

    //结算 留言订单
    public function settleLeaveMessage(){
        //超过1h时间的 (状态是 已经分配律师的，并且是上架的)
        $time = date('Y-m-d H:i:s',time()-73*3600);
        $data = LeaveMessage::where('platform',1)->where('pay_status',1)->where('is_settle',0)->whereTime('check_order_time','<=',$time)->order('pay_time','asc')->limit(50)->select()->toArray();
        if(empty($data)){
            $this->success('暂无数据');
        }
        $payKs = new PayKs();
        foreach($data as $key=>$item){
            $res =$payKs->settle($item['order_no'],2);
            if($res !== true){
                continue;
            }
        }
        $this->success('ok');
    }






}