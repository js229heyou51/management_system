<?php  
namespace app\common\service;

use app\common\model\ArticleCo;
use app\common\trait\CrudTrait;

class ArticleService
{
	use CrudTrait;

	protected $categoryService;

	public function __construct(){
		$this->model = new ArticleCo();  // 给 Trait 中的 $model 赋值
		$this->categoryService = new ArticleCategoryService();
	}
}
?>