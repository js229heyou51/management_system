<?php  
declare (strict_types = 1);

namespace app\admin\validate;

use think\Validate;

class Master extends Validate{
	/**
	* 定义验证规则
	* 格式：'字段名'	=>	['规则1','规则2'...]
	*
	* @var array
	*/
	protected $rule = [
		'username|用户名' => 'require|min:4|max:20|regex:/^[a-zA-Z0-9_]+$/',
		'password|密码' => 'require|min:6|max:16',
	];
	protected $message = [  
		'username.require' => '用户名不能为空',  
		'username.min' => '用户名最少不能少于4个字符',  
		'username.max' => '用户名最多不能多于20个字符',  
		'username.regex'   => '用户名只能包含数字、字母和下划线',  
		'password.min' => '密码最少不能少于6个字符',  
		'password.max' => '密码最多不能多于16个字符',  
	];
}
?>