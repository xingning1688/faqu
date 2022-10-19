<?php
/**
 * 普法资讯数据验证
 * User: Admin
 * Date: 2021/6/17
 * Time: 14:50
 */
namespace app\api\validate;

use think\facade\Db;

class Feedback  {

    // 添加 数据验证
    public function form(&$data) {
        $rule = [
            'content' => 'require',
            'phone' => 'require',
            'phone' => 'mobile',
            'platform' => 'require',
            'email' => 'email',

        ];
        $msg = [
            'content.require'   => '请填写反馈意见！',
            'phone.require' => '请填写手机号！',
            'phone.mobile' => '手机号不合法！',
            'company.require'   => '公司名称必填！',
            'platform.require' => '数据不合法2！',
            'email.email' => 'email 格式不正确！',
        ];
        $validate = \think\facade\Validate::rule($rule)->message($msg);
        if (!$validate->check($data)) {
            return $validate->getError();
        }
        return '';
    }

}