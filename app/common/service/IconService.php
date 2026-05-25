<?php  
namespace app\common\service;

use app\common\model\IconCo;
use app\common\trait\CrudTrait;

class IconService
{
	use CrudTrait;

	protected $categoryService;

	public function __construct(){
		$this->model = new IconCo();  // 给 Trait 中的 $model 赋值
		$this->categoryService = new IconCategoryService();
	}
}
?>