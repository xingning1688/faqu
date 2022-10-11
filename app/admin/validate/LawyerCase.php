<?php
/**
 * 普法资讯数据验证
 * User: Admin
 * Date: 2021/6/17
 * Time: 14:50
 */
namespace app\admin\validate;

use think\facade\Db;

class LawyerCase {

    // 添加 / 编辑 资讯数据验证
    public function form(&$data) {
        $rule = [
            'title'   => 'require',
            'author'   => 'require',
            'page'   => 'require | number',
            //'content' => 'require',
            'original_price' => 'require',
            'original_price' => 'float',
            'sales_price' => 'require',
            'sales_price' => 'float',

        ];
        $msg = [
            'title.require'   => '请填写案例标题！',
            'author.require'   => '请填拟定人！',
            'page.require'   => '请填写页数！',
            'page.number'   => '页数必须是数字！',
            'original_price.require'   => '请填写原价！',
            'original_price.float'   => '原价必须是数字！',
            'sales_price.require'   => '请填写活动价！',
            'sales_price.float'   => '活动价必须是数字！',
            //'content.require' => '请填写案例内容！',

        ];
        $validate = \think\facade\Validate::rule($rule)->message($msg);
        if (!$validate->check($data)) {
            return $validate->getError();
        }


        return '';
    }

}