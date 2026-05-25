<?php 
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\OrderAddress
 *
 * @property int $id
 * @property int $order_id
 * @property string $address
 * @property string $city
 * @property string $delete_time
 * @property string $district
 * @property string $name
 * @property string $phone
 * @property string $province
 * @property string $wtime
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class OrderAddress extends Model{
	use SoftDelete;
}

?>