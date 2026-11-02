<?php  
namespace app\common\service;

use app\common\model\Person;
use app\common\trait\CrudTrait;

class PersonService
{
	use CrudTrait;

	public function __construct(){
		$this->model = new Person();  // 给 Trait 中的 $model 赋值
	}
}
?>