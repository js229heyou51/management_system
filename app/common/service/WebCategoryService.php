<?php  
namespace app\common\service;

use app\common\model\WebLm;
use app\common\trait\CrudCategoryTrait;

class WebCategoryService
{
	use CrudCategoryTrait;

	public function __construct(){
		$this->categoryModel = new WebLm();  // 给 Trait 中的 $model 赋值
	}
	
}