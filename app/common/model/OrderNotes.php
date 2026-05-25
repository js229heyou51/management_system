<?php 
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\OrderNotes
 *
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property string $delete_time
 * @property string $notes
 * @property string $wtime
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class OrderNotes extends Model{
	use SoftDelete;
}

?>