<?php  
namespace app\common\service;

use app\common\model\Order;
use app\common\trait\CrudTrait;

class OrderService
{
	use CrudTrait;

	public function __construct(){
		$this->model = new Order();  // 给 Trait 中的 $model 赋值
	}
}
?>