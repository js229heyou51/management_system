<?php  
namespace app\common\service;

use app\common\model\MasterAction;
use app\common\trait\CrudTrait;

class MasterActionService
{
	use CrudTrait;

	public function __construct(){
		$this->model = new MasterAction();  // 给 Trait 中的 $model 赋值
	}
}
?>