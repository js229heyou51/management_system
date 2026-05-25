<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\PersonCart
 *
 * @property float $price
 * @property float $total
 * @property int $id
 * @property int $num
 * @property int $pid
 * @property int $user_id
 * @property string $delete_time
 * @property string $price_lists
 * @property string $wtime
 * @property-read \app\common\model\PersonCollect $collect
 * @property-read \app\common\model\ProCo $product
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class PersonCart extends Model{
	use SoftDelete;

	// 定义一对一关联  
	public function product()  
	{
		return $this->hasOne(ProCo::class, 'id', 'pid');  
	}
	
	public function collect(){
		return $this->hasOne(PersonCollect::class, 'pid', 'pid');
	}
}

?>