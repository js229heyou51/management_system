<?php  
namespace app\common\service;

use app\common\model\ProLm;
use app\common\trait\CrudCategoryTrait;

class ProCategoryService
{
	use CrudCategoryTrait;

	public function __construct(){
		$this->categoryModel = new ProLm();  // 给 Trait 中的 $model 赋值
	}
	
}