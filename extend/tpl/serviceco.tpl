<?php  
declare (strict_types = 1);
namespace app\common\service;

use app\common\model\{{$table}}Co;
use app\common\trait\CrudTrait;

class {{$table}}Service
{
	use CrudTrait;

	protected $categoryService;
	
	public function __construct(){
		$this->model = new {{$table}}Co();  // 给 Trait 中的 $model 赋值
		$this->categoryService = new {{$table}}CategoryService();
	}
}
?>