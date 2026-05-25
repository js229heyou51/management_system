<?php  
declare (strict_types = 1);

namespace app\admin\validate;

use think\Validate;

class Category extends Validate{
	/**
	* 定义验证规则
	* 格式：'字段名'	=>	['规则1','规则2'...]
	*
	* @var array
	*/
	protected $rule = [
		'title_lm' => 'require|max:100',
		'px' => 'require|number|min:0',
		'pass' => 'in:0,1'
	];

	protected $message = [
		'title_lm.require' => 'title_require',
		'px.require' => 'px_require',
	];
}
?>