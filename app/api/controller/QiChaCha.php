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
use think\admin\Controller;
use app\common\model\QiChaCha as QiChaChaModel;
use app\common\model\QichachaDeadBeat;

class QiChaCha  extends AuthController
//class QiChaCha  extends Controller
{

    //核查老赖接口
    public function getDeadBeatCheck(){

        $parameter['order_id'] = request()->param('order_id',0);

        $postData = [];
        if(!empty(request()->param('searchKey',''))){
            $postData['searchKey'] = request()->param('searchKey','');
        }

        if(!empty(request()->param('searchNo',''))){
            $postData['searchNo'] = request()->param('searchNo','');
        }

        if(!empty(request()->param('pageIndex',''))){
            $postData['pageIndex'] = request()->param('pageIndex','');
        }

        if(!empty(request()->param('pageSize',''))){
            $postData['pageSize'] = request()->param('pageSize','');
        }

        //test
       /* $parameter['order_id'] = 2;
        $postData['searchKey'] = '孙即庆';*/
        //test

        if(empty($parameter['order_id']) || empty($postData)){
            $this->error('请求前，数据不合法');
        }

        $QiChaCha = new QiChaChaModel();
        $res = $QiChaCha->getDeadBeatCheck($postData);//返回json 数据
        //$res = '{"Status":"200","Message":"【有效请求】查询成功","OrderNumber":"DEADBEATCHECK2022121511233610378498","Paging":null,"Result":{"VerifyResult":0,"Data":null}}';
        $res = json_decode($res,true);
        //添加数据
        $data['order_id'] = $parameter['order_id'];
        $data['search'] = json_encode($postData);
        $data['result'] = json_encode($res);
        if($res['Status'] != 200){
            //失败添加数据 到数据库
            $data['status'] = 0;
            QichachaDeadBeat::addData($data);

            $this->error($res['Message']);
        }
        //成功到添加数据库
        $data['status'] = 0;
        QichachaDeadBeat::addData($data);
        $this->success('ok',$res);


    }

    //查询企业自身风险扫描
    public function getEnterpriseRiskScanning(){

        $parameter['order_id'] = request()->param('order_id',0);

        $postData = [];
        if(!empty(request()->param('searchKey',''))){
            $postData['searchKey'] = request()->param('searchKey','');
        }

        //test
        /* $parameter['order_id'] = 2;
         $postData['searchKey'] = '山东壹玖捌贰电子技术有限公司';*/
        //test

        if(empty($parameter['order_id']) || empty($postData)){
            $this->error('请求前，数据不合法');
        }

        $QiChaCha = new QiChaChaModel();
        $res = $QiChaCha->getEnterpriseRiskScanning($postData);//返回json 数据
        //$res = '{"Status":"200","Message":"【有效请求】查询成功","OrderNumber":"DEADBEATCHECK2022121511233610378498","Paging":null,"Result":{"VerifyResult":0,"Data":null}}';
        $res = json_decode($res,true);
        //添加数据
        $data['order_id'] = $parameter['order_id'];
        $data['search'] = json_encode($postData);
        $data['result'] = json_encode($res);
        if($res['Status'] != 200){
            //失败添加数据 到数据库
            $data['status'] = 0;
            QichachaDeadBeat::addData($data);

            $this->error($res['Message']);
        }
        //成功到添加数据库
        $data['status'] = 0;
        QichachaDeadBeat::addData($data);
        $this->success('ok',$res);


    }




}