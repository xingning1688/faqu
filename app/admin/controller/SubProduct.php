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
use think\admin\Controller;
use think\admin\helper\QueryHelper;
use think\admin\model\SystemAuth;
use think\admin\model\SystemBase;
use think\admin\model\SystemUser;
use app\common\model\Product;
use think\admin\service\AdminService;
use think\Exception;
use think\model\Relation;
use think\facade\Session;
use think\facade\Db;
use app\admin\model\Banner as BannerModel;


/**
 * 子商品管理
 * @auth true  # 表示需要验证权限
 * @menu true  # 添加系统菜单节点
 * @login true # 强制登录才可访问
 */
class SubProduct extends Controller{
    private $table = 'sub_product';
    public $type = [1=>'音频',2=>'视频',3=>'文档'];

    /**
     *子商品列表
     * @auth true
     * @menu true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index() {

        $this->title = '子商品列表';

        $where = [];
        $name = $this->request->get('name', '');
        $product_name = $this->request->get('product_name', '');

        if($name!==''){
            $where[] = ['name','=',$name];
        }

        if($product_name!==''){
            $product = Product::where('name',$product_name)->find();
            if(!empty($product)){
                $product = $product->toArray();
                $where[] = ['product_id','=',$product['id']];
            }
        }

        $query = $this->_query($this->table)->where($where)->order('id DESC');  
        $query->page();
    }

    protected function _page_filter(&$data){
        $type = $this->type;
        $product_ids = array_unique(array_column($data,'product_id'));
        $products = Product::whereIn('id',$product_ids)->column('id,name','id');

        $data = array_map(function($item) use($type,$products) {
            $item['type'] = isset($type[$item['type']])?   $type[$item['type']] : '';
            $item['product_name'] = isset($products[$item['product_id']])?   $products[$item['product_id']]['name'] : '';
            return $item;
        },$data);
    }


    /**
     * 添加子商品
     * @auth true
     */
    public function add()
    {
        SystemUser::mForm('form');
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
        //获取所有组合产品
        $products = Product::where('is_combine',1)->column('id,name,is_combine','id');
        $this->products = $products;
        if ($this->request->isPost()) {
            if(empty($data['product_id'])){
                $this->error('所属商品不能为空');
            }

            if(empty($data['type'])){
                $this->error('商品类型不能为空');
            }

            if(empty($data['name'])){
                $this->error('商品名称不能为空');
            }

            if(empty($data['url'])){
                $this->error('请上传文件');
            }


            $data['create_time'] = date('Y-m-d H:i:s',time());
            $data['update_time'] = date('Y-m-d H:i:s',time());
            if(isset($data['id'])){
                unset( $data['create_time']);
            }
            try {
                if(!isset($data['id'])){
                    $this->app->db->name($this->table)->insert($data);
                }else{
                    $this->app->db->name($this->table)->where('id', $data['id'])->update($data);
                }

            } catch (\Exception $e) {
                $this->error('服务器繁忙，请稍后重试！' . $e->getMessage());
            }

        }
    }



    /**
     * 编辑子商品
     * @auth true
     */
    public function edit()
    {
        $this->title = '编辑子商品';
        $this->_form($this->table, 'form');
    }

    /**
     * 表单结果处理
     * @param boolean $result
     */
    protected function _form_result(bool $result,array $data)
    {
        // 这里可以获取到数据记录ID
        //  echo $data['id']
        if ($result && $this->request->isPost()) {
            $this->success('子商品编辑成功！', 'javascript:history.back()');
        }
    }



    /**
     * 删除子商品
     * @auth true
     */
    public function remove()
    {
        $this->_applyFormToken();
        $where = ['id' => $this->request->post('id')];
        $res = $this->app->db->name($this->table)->where($where)->delete();
        if($res){
            $this->success('删除成功！');
        }else {
            $this->error('删除失败！');
        }
    }






}
