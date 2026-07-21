<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;
use think\facade\Lang;

class {{$table}}Co extends Model{
	use SoftDelete;

	// 状态常量
	const STATUS_ACTIVE = 1;
	const STATUS_DISABLED = 0;

	// 定义一对一关联  
	public function profile()  
	{
		return $this->hasOne({{$table}}Lm::class, 'id_lm', 'lm');  
	}

	protected $type = [
		'img_sl' => 'json',
		'pic_sl' => 'json',
		'vid_sl' => 'json',
	];
}
?>