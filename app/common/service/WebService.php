<?php  
namespace app\common\service;

use app\common\model\WebCo;
use app\common\trait\CrudTrait;

class WebService
{
	use CrudTrait;

	protected $categoryService;

	public function __construct(){
		$this->model = new WebCo();  // 给 Trait 中的 $model 赋值
		$this->categoryService = new ArticleCategoryService();
	}
}
?>