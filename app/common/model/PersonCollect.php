<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\PersonCollect
 *
 * @property int $id
 * @property int $pid
 * @property int $user_id
 * @property string $delete_time
 * @property string $wtime
 * @property-read \app\common\model\ProCo $product
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class PersonCollect extends Model{
	use SoftDelete;


	public function product(){
		return $this->hasOne(ProCo::class,'id','pid');
	}

}

?>