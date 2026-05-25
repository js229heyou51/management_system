<?php  
namespace app\common\service;

use app\common\model\ParamLm;
use app\common\trait\CrudCategoryTrait;

class ParamCategoryService
{
	use CrudCategoryTrait;

	public function __construct(){
		$this->categoryModel = new ParamLm();  // 给 Trait 中的 $model 赋值
	}
	
}