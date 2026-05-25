<?php  
namespace app\common\service;

use app\common\model\KeyCo;
use app\common\trait\CrudTrait;

class KeyService
{
	use CrudTrait;

	public function __construct(){
		$this->model = new KeyCo();  // 给 Trait 中的 $model 赋值
	}
}
?>