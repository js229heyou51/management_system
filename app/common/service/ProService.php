<?php  
namespace app\common\service;

use app\common\model\ProCo;
use app\common\trait\CrudTrait;

class ProService
{
	use CrudTrait;

	protected $categoryService;

	public function __construct(){
		$this->model = new ProCo();  // 给 Trait 中的 $model 赋值
		$this->categoryService = new ProCategoryService();
	}
}
?>