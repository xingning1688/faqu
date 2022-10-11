<?php
/**
 * 普法资讯数据验证
 * User: Admin
 * Date: 2021/6/17
 * Time: 14:50
 */
namespace app\admin\validate;

use think\facade\Db;

class LawyerInformation {

    // 添加 / 编辑 资讯数据验证
    public function form(&$data) {
        $rule = [
            'name'   => 'require',
            'law_firm_affiliation' => 'require',
            'lawyer_introduction' => 'require',
            'honor' => 'require',
            'professional_studies' => 'require',
            'professional_title' => 'require',
            'experience' => 'require',
            'classic_case' => 'require',
        ];
        $msg = [
            'name.require'   => '请填写姓名！',
            'law_firm_affiliation.require' => '请填写律师所地址！',
            'lawyer_introduction.require' => '请填写律师简介！',
            'honor.require' => '请填写荣誉与评价！',
            'professional_studies.require' => '请填写专业研究！',
            'professional_title.require' => '请填写职业职称！',
            'experience.require' => '请填写经验！',
            'classic_case.require' => '请填写经典案例！',

        ];
        $validate = \think\facade\Validate::rule($rule)->message($msg);
        if (!$validate->check($data)) {
            return $validate->getError();
        }

        return '';
    }

}