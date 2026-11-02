<?php  
namespace app\common\service;

use app\common\model\MasterLog;
use app\common\trait\CrudTrait;

class MasterLogService
{
	use CrudTrait;

	public function __construct(){
		$this->model = new MasterLog();  // 给 Trait 中的 $model 赋值
	}
}
?>