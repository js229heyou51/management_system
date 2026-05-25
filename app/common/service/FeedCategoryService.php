<?php  
namespace app\common\service;

use app\common\model\FeedLm;
use app\common\trait\CrudCategoryTrait;

class FeedCategoryService
{
	use CrudCategoryTrait;

	public function __construct(){
		$this->categoryModel = new FeedLm();  // 给 Trait 中的 $model 赋值
	}
	
}