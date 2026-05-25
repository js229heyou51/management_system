<?php  
namespace app\common\service;

use app\common\model\Book;
use app\common\trait\CrudTrait;

class BookService
{
	use CrudTrait;

	public function __construct(){
		$this->model = new Book();  // 给 Trait 中的 $model 赋值
	}
}
?>