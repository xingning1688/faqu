<?php


namespace app\api\controller;

use app\api\model\EmailCase;
use app\api\model\Jwt;
use think\admin\Controller;
use think\facade\Db;
use PHPMailer\PHPMailer\PHPMailer;
use app\api\model\LawyerCase;



class Email  extends AuthController
//class Email  extends Controller
{

    /*public function sendEmailCommon($recipientEmail='',$title='',$content=''){

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
    }*/

    public function sendEmail(){

        $email_case['open_id'] = request()->post('open_id');
        $email_case['lawyer_case_id'] = request()->post('lawyer_case_id');
        $email_case['email'] = request()->post('email');

        if(!preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i",$email_case['email'] )){
            $this->error('邮箱格式不正确');
        }

        if(!is_numeric($email_case['lawyer_case_id'])){
            $this->error('合同参数数据不合法');
        }
        if(empty($email_case['open_id'])){
            $this->error('用户参数不合法');
        }



        $lawyerCase = LawyerCase::detail($email_case['lawyer_case_id']);
        if(empty($lawyerCase) || empty($lawyerCase['file_url'])){
            $this->error('合同数据为空，邮件发送失败');
        }


        $localPath = $this->localPath($lawyerCase['file_url']);
        $extension = $this->extension($lawyerCase['file_url']);
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
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
            $mail->addAddress($email_case['email'], '');  // 收件人
            //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
            $mail->addReplyTo('faqu202202@163.com', '法趣网'); //回复的时候回复给哪个邮箱 建议和发件人一致
            //$mail->addCC('cc@example.com');                    //抄送
            //$mail->addBCC('bcc@example.com');                    //密送

            //发送附件
             //$mail->addAttachment('../xy.zip');         // 添加附件
            // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名
            //$mail->addAttachment($lawyerCase['file_url']);         // 添加附件

            $mail->addAttachment($localPath, $lawyerCase['title'].'.'.$extension);    // 发送附件并且重命名

            //Content
            $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
            $mail->Subject =$lawyerCase['title'];
            $mail->Body    = $lawyerCase['title'] ;
            $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容';

            $res = $mail->send();
            if(is_file($localPath)){
                unlink($localPath);
            }
            $email_case['status'] = 1;

        }catch (\Exception  $e) {
            if(empty($e->getCode()) && empty($e->getMessage())){
                if(is_file($localPath)){
                    unlink($localPath);
                }
                $email_case['status'] = 1;
            }else{
                $email_case['status'] = 2;
            }
        }


        EmailCase::addData($email_case);
        if($email_case['status'] == 1){
            $this->success('邮件发送成功');
        }else{
            $this->error('邮件发送失败');
        }


    }

    public function localPath($file){
        $extension = $this->extension($file);
        $file = file_get_contents($file);

        $time = time();
        $pic_local_path =  './upload' . '/cache/doc';
        $pic_local = $pic_local_path . '/doc-' . $time . $extension;

        if (!file_exists($pic_local_path)) {
            mkdir($pic_local_path, 0777);
            @chmod($pic_local_path, 0777);
        }
        file_put_contents($pic_local, $file);
        return $pic_local;
    }

    public function extension($file){
        $extension = explode(".",$file);
        $extension = end($extension);
        if(!in_array($extension,['docx','doc'])){
            $extension = 'doc';
        }
        return $extension;
    }

    public function sendEmail2(){
        $email_case['open_id'] = request()->post('open_id','');
        $email_case['email'] = request()->post('email','');
        $email_case['file_url'] = request()->post('file_url','');
        $email_case['file_name'] = request()->post('file_name','');

        if(!preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i",$email_case['email'] )){
            $this->error('邮箱格式不正确');
        }

        if(empty($email_case['open_id'])){
            $this->error('用户参数不合法');
        }

        if(empty($email_case['file_url'])){
            $this->error('用户参数不合法2');
        }


        $localPath = $this->localPath($email_case['file_url']);
        $extension = $this->extension($email_case['file_url']);
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
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
            $mail->addAddress($email_case['email'], '');  // 收件人
            //$mail->addAddress('ellen@example.com');  // 可添加多个收件人
            $mail->addReplyTo('faqu202202@163.com', '法趣网'); //回复的时候回复给哪个邮箱 建议和发件人一致
            //$mail->addCC('cc@example.com');                    //抄送
            //$mail->addBCC('bcc@example.com');                    //密送

            //发送附件
            //$mail->addAttachment('../xy.zip');         // 添加附件
            // $mail->addAttachment('../thumb-1.jpg', 'new.jpg');    // 发送附件并且重命名
            //$mail->addAttachment($lawyerCase['file_url']);         // 添加附件

            $mail->addAttachment($localPath, $email_case['file_name'].'.'.$extension);    // 发送附件并且重命名

            //Content
            $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
            $mail->Subject =$email_case['file_name'];
            $mail->Body    = $email_case['file_name'] ;
            $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容';

            $res = $mail->send();
            if(is_file($localPath)){
                unlink($localPath);
            }
            $email_case['status'] = 1;

        }catch (\Exception  $e) {
            if(empty($e->getCode()) && empty($e->getMessage())){
                if(is_file($localPath)){
                    unlink($localPath);
                }
                $email_case['status'] = 1;
            }else{
                $email_case['status'] = 2;
            }
        }


        if($email_case['status'] == 1){
            $this->success('邮件发送成功');
        }else{
            $this->error('邮件发送失败');
        }


    }




}