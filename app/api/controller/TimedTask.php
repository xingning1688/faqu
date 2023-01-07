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
use app\common\model\LawyerInformation;
use app\common\model\LawyerBillService as LawyerBillServiceModel;
use app\common\model\Common;
use app\common\model\LeaveMessage;
use app\common\model\CaseSourceSquare as CaseSourceSquareModel;





class TimedTask  extends Controller
{

    //定时任务 申请结算
    public function settleKs(){
        //dump (strtotime('2023-01-05')); echo "<br/>";
        //dump (strtotime('2023-01-01')); echo "<br/>";exit;

        //超过1h时间的 (状态是 已经分配律师的，并且是上架的)
        $time = time()-80*3600;
        $data = Order::where('platform',1)->where('pay_status',1)->where('is_settle',0)->whereTime('pay_time','<=',$time)->order('pay_time','asc')->limit(50)->select()->toArray();

        if(empty($data)){
            $this->success('暂无数据');
        }
        $payKs = new PayKs();
        foreach($data as $key=>$item){
            $res =$payKs->settle($item['order_no'],3); dump($res);exit;
            if($res !== true){
                continue;
            }
        }


    }


    //定时任务 打回广场  （首先是接收的，）
    public function  backSquare(){

        //超过1h时间的 (状态是 已经分配律师的，并且是上架的)
        $data = CaseSourceSquareModel::where('status',1)->where('is_shelves',0)->whereTime('allocate_time','>=','1 hours')->order('allocate_time','asc')->limit(50)->select()->toArray();

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
                $this->error('失败');
            }
        }

        $this->success('成功');

    }








}