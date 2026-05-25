<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\PersonMessage
 *
 * @property int $check
 * @property int $id
 * @property int $user_id
 * @property string $content
 * @property string $delete_time
 * @property string $ip
 * @property string $title
 * @property string $wtime
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class PersonMessage extends Model{
	use SoftDelete;
}

?>