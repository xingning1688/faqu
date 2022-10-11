<?php


namespace app\api\model;

use app\api\model\EmailCase;
use app\api\model\Jwt;
use think\admin\Controller;
use think\facade\Db;
use PHPMailer\PHPMailer\PHPMailer;
use app\api\model\LawyerCase;
use think\Model;


//class Email  extends AuthController
class Email  extends BaseModel
{
    protected $table = 'email_case';

    public function sendEmailCommon($recipientEmail='',$title='',$content=''){

        $mail = new PHPMailer(true);

        //服务器配置
        $mail->CharSet ="UTF-8";                     //设定邮件编码
        $mail->SMTPDebug = 0;                        // 调试模式输出
        $mail->isSMTP();                             // 使用SMTP
        $mail->Host = 'smtp.163.com';                // SMTP服务器
        $mail->SMTPAuth = true;                      // 允许 SMTP 认证
        $mail->Username = 'faqu202202@163.com';      // SMTP 用户名  即邮箱的用户名
        $mail->Password = 'BHQZGUIARIALUBKG';                        // SMTP 密码  部分邮箱是授权码(例如163邮箱，不明白看下面有说明)
        $mail->SMTPSecure = 'ssl';                   // 允许 TLS 或者ssl协议
        $mail->Port = 465;                           // 服务器端口 25 或者465 具体要看邮箱服务器支持

        $mail->setFrom('faqu202202@163.com', '法趣网');  //发件人
        $mail->addAddress($recipientEmail, '');  // 收件人
        //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
        $mail->addReplyTo('faqu202202@163.com', '法趣网'); //回复的时候回复给哪个邮箱 建议和发件人一致
        //$mail->addCC('cc@example.com');                    //抄送
        //$mail->addBCC('bcc@example.com');                    //密送

        //发送附件
        //$mail->addAttachment('../xy.zip');         // 添加附件
        // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名
        //$mail->addAttachment($lawyerCase['file_url']);         // 添加附件

        //Content
        $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
        $mail->Subject = $title;
        $mail->Body    = $content ;
        $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容';

        $res = $mail->send();
        if(!$res){
            return false;
        }

        return true;
    }






}