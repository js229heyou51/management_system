<?php  
namespace app\common\service;

use app\common\model\HomeCo;
use app\common\trait\CrudTrait;

class HomeService
{
	use CrudTrait;

	protected $categoryService;

	public function __construct(){
		$this->model = new HomeCo();  // 给 Trait 中的 $model 赋值
		$this->categoryService = new HomeCategoryService();
	}
}
?>