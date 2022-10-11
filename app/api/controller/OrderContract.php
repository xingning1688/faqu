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
use think\admin\Controller;
use think\facade\Db;
use app\api\model\LawyerInformations;
use app\api\model\LawyerCase;
use app\api\model\OrderContract as OrderContracts;

/** 合同订单
 *
 *
 */
class OrderContract  extends AuthController
{
    public function buyContract()
    {
        //获取订单信息

        //获取参数
        /* $parameter['lawyer_case_detail'] =  request()->param('lawyer_case_detail');
        $parameter['open_id'] = request()->param('open_id');
        $parameter['pay_type'] = request()->param('pay_type');
        $parameter['platform'] = request()->param('platform');//platform              平台  [0=>'未知','1'=>'快手','2'=>'微信','3'=>'抖音']*/


        //test
        //测试数据 start
        $lawyer_case_detail=[['lawyer_case_id'=>1,'price'=>0.00,'num'=>2],['lawyer_case_id'=>2,'price'=>1.00,'num'=>3]];


        $parameter['lawyer_case_detail'] =  $lawyer_case_detail;//合同详情
        $parameter['open_id'] = 'open_id';
        $parameter['pay_type'] = 1;
        $parameter['platform'] = 1;//platform              平台  [0=>'未知','1'=>'快手','2'=>'微信','3'=>'抖音']
        //测试数据 end

        //dump($lawyerCases);exit;
        //验证提交过来的数据
        if(!is_numeric($parameter['pay_type']) || !is_numeric($parameter['platform']) || empty($parameter['open_id'])){
            $this->error('参数不合法1');
        }

        $lawyer_case_ids = array_column($lawyer_case_detail,'lawyer_case_id');
        $lawyerCases = LawyerCase::detailIds($lawyer_case_ids);
        if(count($lawyerCases) != count($lawyer_case_ids)){
            $this->error('参数不合法2');
        }
        $parameter['order_price'] = 0;
        foreach($lawyer_case_detail as $key =>$value){
            if(!isset($lawyerCases[$value['lawyer_case_id']]['sales_price'])){
                $this->error('参数不合法3；'.'lawyer_case_id:'.$value['lawyer_case_id']);
            }

            if($value['price'] != $lawyerCases[$value['lawyer_case_id']]['sales_price']){
                $this->error('参数不合法4；'.'lawyer_case_id:'.$value['lawyer_case_id']);
            }

            $parameter['order_price'] += $value['price'] * $value['num'];

            //组装返回信息
            $parameter['order_contract']['order_price'] = $parameter['order_price'];

            $parameter['order_contract_detail'][$key]['price'] = $value['price'];
            $parameter['order_contract_detail'][$key]['num'] = $value['num'];
            $parameter['order_contract_detail'][$key]['title'] = isset($lawyerCases[$value['lawyer_case_id']]['title']) ? $lawyerCases[$value['lawyer_case_id']]['title'] : '';
            $parameter['order_contract_detail'][$key]['author'] = isset($lawyerCases[$value['lawyer_case_id']]['author']) ? $lawyerCases[$value['lawyer_case_id']]['author'] : '';
            $parameter['order_contract_detail'][$key]['lawyer_case_id'] = $value['lawyer_case_id'];
        }



        //test


        //添加数据库
        $res = OrderContracts::buyOrderContract($parameter);
        if($res === false){
            $this->error('订单失败');
        }
        $data['order_contract'] = $parameter['order_contract'];
        $data['order_contract_detail'] = $parameter['order_contract_detail'];
        $this->success('订单成功',$data);


        /*if($list===false){
            $this->error('参数错误');
        }
        if(empty($list)){
            $this->error('暂无数据');
        }

        $this->success('ok',$list);*/
    }




}