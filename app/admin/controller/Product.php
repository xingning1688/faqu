<?php

namespace app\admin\controller;


use app\admin\model\Product as ProductModel;

use think\admin\Controller;

use app\common\model\LawyerInformation;


/**
 * 商品管理
 * Class Goods
 * @package app\data\controller\shop
 */
class Product extends Controller
{

    public $is_recommend = [0=>'正常','1'=>'推荐'];
    public $is_index = [0=>'正常','1'=>'首页'];
    public $status = [0=>'销售中','1'=>'下架'];
    public $way = [1=>'线上销售','2'=>'线下销售'];
    public $type = [1=>'法律服务工具'];
    /**
     * 商品列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $this->title = '商品列表';
        $this->status;
        $this->is_index;
        $this->is_recommend;
        $lawyer = LawyerInformation::allLawyer();
        $this->lawyer = $lawyer;

        $query = ProductModel::mQuery();

        // 列表排序并显示
        $query->like('name')->like('status')->like('lawyer_information_id')->like('is_index')->like('is_recommend');
        $query->/*equal('status,vip_entry,truck_type,rebate_type')->*/order('sort desc,id desc')->page();
    }



    /**
     * 数据列表处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _page_filter(array &$data)
    {
        $lawyer_information_ids = array_column($data,'lawyer_information_id');
        $lawyer = LawyerInformation::pluckAttrByIds($lawyer_information_ids,'id,name');
        $data = array_map(function($item) use($lawyer) {
            $item['lawyer_name'] = isset($lawyer[$item['lawyer_information_id']])?   $lawyer[$item['lawyer_information_id']]['name'] : '';
            return $item;
        },$data);
    }

    /**
     * 添加商品
     * @auth true
     */
    public function add()
    {
        $this->mode = 'add';
        $this->title = '添加商品';
        ProductModel::mForm('form');
    }

    /**
     * 编辑商品
     * @auth true
     */
    public function edit()
    {
        $this->mode = 'edit';
        $this->title = '编辑商品数据';
        ProductModel::mForm('form');
    }




    /**
     * 表单数据处理
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _form_filter(array &$data)
    {

        if ($this->request->isGet()) {
            $this->is_recommend;
            $this->is_index;
            $this->status;

            $lawyer = LawyerInformation::allLawyer();
            $this->lawyer = $lawyer;

        } elseif ($this->request->isPost()) {  //dump($data);exit;
            if (empty($data['cover'])) $this->error('商品图片不能为空！');
            if (empty($data['slider'])) $this->error('轮播图片不能为空！');
            if (empty($data['name'])) $this->error('商品名称不能为空！');
            if (empty($data['lawyer_information_id']) && !is_numeric($data['lawyer_information_id'])) $this->error('请选择所属律师！');

        }
    }

    /**
     * 表单结果处理
     * @param boolean $result
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function _form_result(bool $result)
    {
        if ($result && $this->request->isPost()) {
            $this->success('商品编辑成功！', 'javascript:history.back()');
        }
    }






}