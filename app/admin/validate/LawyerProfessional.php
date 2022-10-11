<?php
/**
 * 普法资讯数据验证
 * User: Admin
 * Date: 2021/6/17
 * Time: 14:50
 */
namespace app\admin\validate;

use think\facade\Db;

class LawyerProfessional {

    // 添加 / 编辑 资讯数据验证
    public function form(&$data) {
        $rule = [
            'professional'   => 'require',
        ];
        $msg = [
            'professional.require'   => '请填写专业领域！',

        ];
        $validate = \think\facade\Validate::rule($rule)->message($msg);
        if (!$validate->check($data)) {
            return $validate->getError();
        }


        return '';
    }

}