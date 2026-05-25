<?php  
declare (strict_types = 1);

namespace app\admin\validate;

use think\Validate;
use think\facade\Lang;

class Info extends Validate{
	/**
	* 定义验证规则
	* 格式：'字段名'	=>	['规则1','规则2'...]
	*
	* @var array
	*/
	protected $rule = [
		'title' => 'require|max:100',
		'lm' => 'require|number|min:0',
		'px' => 'require|number|min:0',
		'pass' => 'in:0,1'
	];

	protected $message = [
		'title.require' => 'title_require',
		'lm.require' => 'lm_require',
		'px.require' => 'px_require',
	];
}
?>