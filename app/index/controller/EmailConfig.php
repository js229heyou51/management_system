<?php  
namespace app\index\controller;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailConfig extends Base{

	public $port = '465';
	public $host = 'smtp.qiye.163.com';
	public $emailConfig = [];
	public $title = '';
	public $content = '';

	public function __construct($port='',$host='',$emailConfig='',$title='',$content=''){
		$this->port = $port != '' ? $port : $this->port;
		$this->host = $host != '' ? $host : $this->host;
		$this->emailConfig = $emailConfig;
		$this->title = $title;
		$this->content = $content;
	}


	public function sendEmail(){
		// 创建 PHPMailer 实例
		$mail = new PHPMailer(true);

		try {
			// 服务器设置
			$mail->SMTPDebug = 0;                       // 启用详细调试输出
			$mail->CharSet = 'UTF-8';
			$mail->isSMTP();                            // 设置邮件发送方式为 SMTP
			$mail->Host       = $this->host;     // SMTP 服务器地址
			$mail->SMTPAuth   = true;                   // 启用 SMTP 认证
			$mail->Username   = $this->emailConfig['username']??''; // SMTP 用户名
			$mail->Password   = $this->emailConfig['password']??'';  // SMTP 密码
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // 启用 TLS 加密，`ssl` 也可用
			$mail->Port       = $this->port;                   // SMTP 服务器端口

			$mail->setFrom($this->emailConfig['outbox']??'','');
			// 收件人
			$mail->addAddress($this->emailConfig['inbox']??'');     // 添加一个收件人
			// 内容
			$mail->isHTML(true);                                  // 设置邮件格式为 HTML
			$mail->Subject = $this->title;
			$mail->Body    = $this->content;
			$mail->send();
			return true;
		}catch (Exception $e) {
			return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	}
}
?>