<?php  
namespace app\common\service;

use app\common\model\ArticleLm;
use app\common\trait\CrudCategoryTrait;

class ArticleCategoryService
{
	use CrudCategoryTrait;

	public function __construct(){
		$this->categoryModel = new ArticleLm();  // 给 Trait 中的 $model 赋值
	}
	
}