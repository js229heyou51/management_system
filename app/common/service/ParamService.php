<?php  
namespace app\common\service;

use app\common\model\ParamCo;
use app\common\trait\CrudTrait;

class ParamService
{
	use CrudTrait;

	protected $categoryService;

	public function __construct(){
		$this->model = new ParamCo();  // 给 Trait 中的 $model 赋值
		$this->categoryService = new ParamCategoryService();
	}
}
?>