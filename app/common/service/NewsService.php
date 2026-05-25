<?php  
namespace app\common\service;

use app\common\model\NewsCo;
use app\common\trait\CrudTrait;

class NewsService
{
	use CrudTrait;

	protected $categoryService;

	public function __construct(){
		$this->model = new NewsCo();  // 给 Trait 中的 $model 赋值
		$this->categoryService = new NewsCategoryService();
	}
}
?>