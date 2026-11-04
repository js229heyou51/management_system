<?php  
namespace app\common\service;

use app\common\model\Config;
use app\common\trait\CrudTrait;

class ConfigService
{
	use CrudTrait;

	public function __construct(){
		$this->model = new Config();  // 给 Trait 中的 $model 赋值
	}
}
?>