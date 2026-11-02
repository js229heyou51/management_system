<?php  
declare (strict_types = 1);
namespace app\common\service;

use app\common\model\FeedRecord;
use app\common\trait\CrudTrait;

class FeedRecordService
{
	use CrudTrait;
	
	public function __construct(){
		$this->model = new FeedRecord();  // 给 Trait 中的 $model 赋值
	}
}
?>