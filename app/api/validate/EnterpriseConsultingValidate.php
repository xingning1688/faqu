<?php
/**
 * 普法资讯数据验证
 * User: Admin
 * Date: 2021/6/17
 * Time: 14:50
 */
namespace app\api\validate;

use think\facade\Db;

class EnterpriseConsultingValidate  {

    // 添加 数据验证
    public function form(&$data) {
        $rule = [
            'title' => 'require',
            'company' => 'require',
            'platform' => 'require',
            'phone' => 'require',
            'phone' => 'mobile',
        ];
        $msg = [
            'title.require'   => '数据不合法1！',
            'company.require'   => '公司名称必填！',
            'phone.require' => '请填写手机号！',
            'phone.mobile' => '手机号不合法！',
            'platform.require' => '数据不合法2！',
        ];
        $validate = \think\facade\Validate::rule($rule)->message($msg);
        if (!$validate->check($data)) {
            return $validate->getError();
        }
        return '';
    }

}