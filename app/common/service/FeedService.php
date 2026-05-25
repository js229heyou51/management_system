<?php  
namespace app\common\service;

use app\common\model\FeedCo;
use app\common\trait\CrudTrait;

class FeedService
{
	use CrudTrait;

	protected $categoryService;

	public function __construct(){
		$this->model = new FeedCo();  // 给 Trait 中的 $model 赋值
		$this->categoryService = new FeedCategoryService();
	}
}
?>