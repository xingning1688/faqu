<?php
namespace app\common\model;

use app\api\model\BaseModel;
use app\api\model\LawyerProfessional;
use app\api\model\LeaveMessages;
use think\facade\Db;
use app\api\model\PlatformUser;


class CaseSourceSquareDetail extends BaseModel {
    protected $table = 'case_source_square_detail';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';






}