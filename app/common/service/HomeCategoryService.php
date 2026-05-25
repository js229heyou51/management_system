<?php  
namespace app\common\service;

use app\common\model\HomeLm;
use app\common\trait\CrudCategoryTrait;

class HomeCategoryService
{
	use CrudCategoryTrait;

	public function __construct(){
		$this->categoryModel = new HomeLm();  // 给 Trait 中的 $model 赋值
	}
	
}