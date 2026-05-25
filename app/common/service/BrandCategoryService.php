<?php  
namespace app\common\service;

use app\common\model\BrandLm;
use app\common\trait\CrudCategoryTrait;

class BrandCategoryService
{
	use CrudCategoryTrait;

	public function __construct(){
		$this->categoryModel = new BrandLm();  // 给 Trait 中的 $model 赋值
	}
	
}