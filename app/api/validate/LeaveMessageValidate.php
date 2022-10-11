<?php
/**
 * 普法资讯数据验证
 * User: Admin
 * Date: 2021/6/17
 * Time: 14:50
 */
namespace app\api\validate;

use think\facade\Db;

class LeaveMessageValidate {

    // 添加 数据验证
    public function form(&$data) {
        $rule = [
            'open_id'   => 'require',
            'phone' => 'require',
            'phone' => 'mobile',
            //'wx_num' => 'require',
            'problem' => 'require',
            'platform' => 'require',

        ];
        $msg = [
            'open_id.require'   => '数据不合法！',
            'phone.require' => '请填写手机号！',
            'phone.mobile' => '手机号不合法！',
            //'wx_num.require' => '请填写微信号！',
            'problem.require' => '请填写问题描述！',
            'platform.require' => '数据不合法2！',
        ];
        $validate = \think\facade\Validate::rule($rule)->message($msg);
        if (!$validate->check($data)) {
            return $validate->getError();
        }
        return '';
    }

}