<?php
namespace app\api\controller;

use app\api\model\Jwt;
use think\admin\Controller;
use app\common\model\Product as ProductModel;



class Product  extends Controller
{
    //获取产品列表
    public function getList(){
        $list = ProductModel::getList();
        $this->success('ok',$list);
    }

    //详情
    public function detail(){
        $id = request()->param('id', 0);
        if(empty($id) || !is_numeric($id)){
            $this->error('参数错误');
        }

        $data = ProductModel::detail($id);
        if(empty($data)){
            $this->success('暂无数据');
        }

        $this->success('ok',$data);
    }





}