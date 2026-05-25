<?php  
namespace app\common\service;

use app\common\model\IconLm;
use app\common\trait\CrudCategoryTrait;

class IconCategoryService
{
	use CrudCategoryTrait;

	public function __construct(){
		$this->categoryModel = new IconLm();  // 给 Trait 中的 $model 赋值
	}
	
}