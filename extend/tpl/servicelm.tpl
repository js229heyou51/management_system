<?php  
namespace app\common\service;

use app\common\model\{{$table}}Lm;
use app\common\trait\CrudCategoryTrait;

class {{$table}}CategoryService
{
	use CrudCategoryTrait;

	public function __construct(){
		$this->categoryModel = new {{$table}}Lm();  // 给 Trait 中的 $model 赋值
	}
	
}