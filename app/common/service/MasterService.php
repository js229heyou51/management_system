<?php  
namespace app\common\service;

use app\common\model\Master;
use app\common\trait\CrudTrait;

class MasterService
{
	use CrudTrait;

	public function __construct(){
		$this->model = new Master();  // 给 Trait 中的 $model 赋值
	}
}
?>