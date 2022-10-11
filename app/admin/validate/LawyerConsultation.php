<?php
/**
 * 普法资讯数据验证
 * User: Admin
 * Date: 2021/6/17
 * Time: 14:50
 */
namespace app\admin\validate;

use think\facade\Db;

class LawyerConsultation {

    // 添加 / 编辑 资讯数据验证
    public function form(&$data) {
        $rule = [
            'consultation_title'   => 'require',
            'price' => 'require',
            'price' => 'float',
        ];
        $msg = [
            'consultation_title.require'   => '请填写案例标题！',
            'price.require'   => '请填写价格！',
            'price.float'   => '价格必须是数字！',


        ];
        $validate = \think\facade\Validate::rule($rule)->message($msg);
        if (!$validate->check($data)) {
            return $validate->getError();
        }


        return '';
    }

}