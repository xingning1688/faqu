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

namespace app\api\controller;

use think\admin\Controller;
use think\facade\Db;
use app\api\model\LawyerCase as LawyerCases;
use app\common\model\CaseSourceSquare as CaseSourceSquareModel;
use app\api\model\PlatformUser;
class Common  extends Controller
{

    public function fabuConfig(){
        $version_number = request()->param('version_number', 0);//大于正式版本的 测试版本号
        $platform = request()->param('platform', 1);//大于正式版本的 测试版本号
        if(empty($version_number) || !is_numeric($version_number)){
            $this->error('参数错误');
        }

        if(empty($platform) || !is_numeric($platform)){
            $this->error('参数错误2');
        }
        //5是 正式版本号（最后一提交成功的版本号）
        $config_version = Db::table('config_version')->find(1);
        $config_version['version_number'] = isset($config_version['version_number'])? $config_version['version_number'] : 0;

        if($platform == 1){

            if(($version_number<= $config_version['version_number'])){
                $data['wx'] = '微信号';
            }else{
                $data['wx'] = '快手号';
            }

        }else if($platform == 2){
            if(($version_number<= $config_version['version_number'])){
                $data['wx'] = '微信号';
            }else{
                $data['wx'] = '微信号';
            }



        }else if($platform == 3){

            if(($version_number<= $config_version['version_number'])){
                $data['wx'] = '微信号';
            }else{
                $data['wx'] = '抖音号';
            }

        }else{
            $data['wx'] = '微信号';
        }


        $this->success('ok',$data['wx']);
    }

    public function fabuConfig2(){
        $version_number = request()->param('version_number', 0);
        $platform = request()->param('platform', 1);
        if(empty($version_number) || !is_numeric($version_number)){
            $this->error('参数错误');
        }

        if(empty($platform) || !is_numeric($platform)){
            $this->error('参数错误2');
        }
        //5是 正式版本号（最后一提交成功的版本号）
        $config_version = Db::table('config_version')->find(1);
        $config_version['version_number'] = isset($config_version['version_number'])? $config_version['version_number'] : 0;

        if($platform == 1){

            if(($version_number<= $config_version['version_number'])){
                $data['platform'] = '微信号';
                $data['status'] = 1;
            }else{
                $data['platform'] = '快手号';
                $data['status'] = 0;
            }

        }else if($platform == 2){
            if(($version_number<= $config_version['version_number'])){
                $data['platform'] = '微信号';
                $data['status'] = 1;
            }else{
                $data['platform'] = '微信号';
                $data['status'] = 0;
            }



        }else if($platform == 3){

            if(($version_number<= $config_version['version_number'])){
                $data['platform'] = '微信号';
                $data['status'] = 1;
            }else{
                $data['platform'] = '抖音号';
                $data['status'] = 0;
            }

        }else{
            $data['platform'] = '微信号';
            $data['status'] = 0;
        }


        $this->success('ok',$data);
    }

    //获取某个案源详情
    public function getCaseSourceSquare(){
        $id = request()->get('id',0); //案源id
        if(empty($id)){
            $this->error('参数错误');
        }
        $row = CaseSourceSquareModel::getMsgById($id);
        if(empty($row)){
            $this->error('失败，暂无数据');
        }

        $platform_user = PlatformUser::where('open_id',$row['open_id'])->column('open_id,avatar_url','open_id');
        $row['avatar_url'] = isset($platform_user[$row['open_id']]) ? $platform_user[$row['open_id']]['avatar_url'] : '';
        $row['img'] = empty($row['img']) ? $row['img'] : explode('|',$row['img']);
        $this->success('成功',$row);
    }



}