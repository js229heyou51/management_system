<?php  
namespace app\common\service;

use app\common\model\BrandCo;
use app\common\trait\CrudTrait;

class BrandService
{
	use CrudTrait;

	protected $categoryService;

	public function __construct(){
		$this->model = new BrandCo();  // 给 Trait 中的 $model 赋值
		$this->categoryService = new BrandCategoryService();
	}
}
?>