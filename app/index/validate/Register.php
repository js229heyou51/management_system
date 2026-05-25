<?php  
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Register extends Validate{
	/**
	* 定义验证规则
	* 格式：'字段名'	=>	['规则1','规则2'...]
	*
	* @var array
	*/
	protected $rule = [
		'username|用户名' => 'require',
		'smscode|验证码' => 'require',
		'password|密码' => 'require',
		'conformpassword|确认密码' => 'require|confirm:password',
	];

	protected $message = [  
		'username.require' => '手机号/用户名不能为空',  
		'smscode.require' => '验证码不能为空',  
		'password.require' => '登录密码不能为空',  
		'conformpassword.require' => '确认密码不能为空',  
		'conformpassword.confirm' => '两次输入的密码不一致',  
	];
}
?>