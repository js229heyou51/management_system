<?php  
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Address extends Validate{
	/**
	* 定义验证规则
	* 格式：'字段名'	=>	['规则1','规则2'...]
	*
	* @var array
	*/
	protected $rule = [
		'province|所在省市' => 'require',
		'city|所在省市' => 'require',
		'district|所在省市' => 'require',
		'address|详细地址' => 'require',
		'rename|收货人' => 'require',
		'phone|手机号码' => 'require',
	];

	protected $message = [  
		'province.require' => '所在省份不能为空',  
		'city.require' => '所在城市不能为空',  
		'district.require' => '所在区不能为空',  
		'address.require' => '详细地址不能为空',  
		'rename.require' => '收货人不能为空',  
		'phone.require' => '手机号码不能为空',
	];
}
?>