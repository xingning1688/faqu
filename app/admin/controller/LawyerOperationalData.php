<?php

namespace app\admin\controller;


use app\admin\model\LawyerOperationalData as LawyerOperationalDataModel;

use think\admin\Controller;

use app\common\model\LawyerInformation;


/**
 * 律师运营数据管理
 *
 * @package
 */
class LawyerOperationalData extends Controller
{

    /**
     * 运营数据列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {

        $lawyer = LawyerInformation::allLawyer();
        $this->lawyer = $lawyer;

        $query = LawyerOperationalDataModel::mQuery();

        // 列表排序并显示
        //$query->like('name')->like('status')->like('lawyer_information_id')->like('is_index')->like('is_recommend');
        $query->equal('lawyer_information_id')->order('id desc')->page();
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
        $lawyer_information_ids = array_unique(array_column($data,'lawyer_information_id'));
        $lawyer = LawyerInformation::pluckAttrByIds($lawyer_information_ids,'id,name');

        $data = array_map(function($item) use($lawyer) {
            $item['lawyer_name'] = isset($lawyer[$item['lawyer_information_id']])?   $lawyer[$item['lawyer_information_id']]['name'] : '';
            return $item;
        },$data);
        //dump($data);exit;
    }

    /**
     * 添加运营数据
     * @auth true
     */
    public function add()
    {
        $this->mode = 'add';
        $this->title = '添加运营数据';
        LawyerOperationalDataModel::mForm('form');
    }

    /**
     * 编辑运营数据
     * @auth true
     */
    public function edit()
    {
        $this->mode = 'edit';
        $this->title = '编辑运营数据';
        LawyerOperationalDataModel::mForm('form');
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
            $this->title = '律师运营数据';
            $lawyer_information_id = $this->request->param('id/d', 0);
            if(empty($lawyer_information_id)) {
                $this->error('参数错误1！');
            }
            $this->lawyer_information_id = $lawyer_information_id;

        } elseif ($this->request->isPost()) {
            if (empty($data['data_end'])) $this->error('请选择数据截止日期！');
            if(!is_numeric($data['dy_num']) || !is_numeric($data['dy_fans']) || !is_numeric($data['wx_num']) || !is_numeric($data['wx_fans']) || !is_numeric($data['ks_num']) || !is_numeric($data['ks_fans']) ){
                $this->error('数据不合法');
            }
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
            $this->success('运营数据添加成功！', 'javascript:history.back()');
        }
    }






}