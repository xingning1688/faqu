<?php
namespace app\common\model;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use \Exception;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Utils\Utils;

use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
class DySmsApiModel extends BaseModel{


    public function __construct() {

    }

    /**
     * 使用AK&SK初始化账号Client
     * @param string $accessKeyId
     * @param string $accessKeySecret
     * @return Dysmsapi Client
     */
    public static function createClient($accessKeyId, $accessKeySecret){
        $config = new Config([
            // 必填，您的 AccessKey ID
            "accessKeyId" => $accessKeyId,
            // 必填，您的 AccessKey Secret
            "accessKeySecret" => $accessKeySecret,
        ]);
        // 访问的域名
        $config->endpoint = "dysmsapi.aliyuncs.com";
        //$config->endpoint = "ecs-cn-zhangjiakou.aliyuncs.com";
        return new Dysmsapi($config);
    }

    /**
     * @param string[] $args
     * @return void
     */
    public static function main($request_data){
        // 工程代码泄露可能会导致AccessKey泄露，并威胁账号下所有资源的安全性。以下代码示例仅供参考，建议使用更安全的 STS 方式，更多鉴权访问方式请参见：https://help.aliyun.com/document_detail/311677.html
        $client = self::createClient("LTAI5tR9iMsqUYckPamXkwZH", "jNmMipCqY3VrdaFD4wzTvRx3GcjRTk");
        //dump($request_data);exit;
        $sendSmsRequest = new SendSmsRequest($request_data);
        /*$sendSmsRequest = new SendSmsRequest([
            "PhoneNumbers" => "13662248993",
            "SignName" => "法趣",
            "templateCode" => "SMS_267700539",
            "TemplateParam" => '{"name":"xing2"}',
        ]);*/

        //dump(35,$sendSmsRequest);exit;
        try {
            // 复制代码运行请自行打印 API 的返回值
            $res = $client->sendSmsWithOptions($sendSmsRequest, new RuntimeOptions([]));
            //$res = $client->sendSmsWithOptions($sendSmsRequest, new RuntimeOptions([]));
            //dump(11,$res);exit;
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
            }
            // 如有需要，请打印 error
            $res = Utils::assertAsString($error->message);
            return false;
        }

       if($res->body->code != 'OK'){
           //dump(6666,$res->body->code,$res->body->message);exit;
            return false;
       }
       return true;

    }

    //发送通知短信
    public static function sendSms($lawyer_information_id){
        $row = LawyerInformation::getMsgById($lawyer_information_id,'id,name,phone');
        if(!empty($row) && !empty($row['phone'])){
            $name = !empty($row['name']) ? $row['name'] : '';

            $TemplateParam['name'] = $name;
            $request_data['phoneNumbers'] = $row['phone'];
            $request_data['signName'] = '法趣';
            $request_data['templateCode'] = 'SMS_267700539';
            $request_data['templateParam'] = json_encode($TemplateParam);
            $res = self::main($request_data);
            return $res;
        }
    }
}







    






