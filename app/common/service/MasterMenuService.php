<?php  
namespace app\common\service;

use app\common\model\MasterMenu;
use app\common\trait\CrudTrait;

class MasterMenuService
{
	use CrudTrait;

	public function __construct(){
		$this->model = new MasterMenu();  // 给 Trait 中的 $model 赋值
	}
}
?>