<?php  
namespace app\common\service;

use app\common\model\NewsLm;
use app\common\trait\CrudCategoryTrait;

class NewsCategoryService
{
	use CrudCategoryTrait;

	public function __construct(){
		$this->categoryModel = new NewsLm();  // 给 Trait 中的 $model 赋值
	}
	
}