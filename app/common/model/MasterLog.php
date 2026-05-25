<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;

/**
 * Class app\common\model\MasterLog
 *
 * @property int $id
 * @property int $wtime
 * @property string $create_at
 * @property string $delete_time
 * @property string $ip
 * @property string $lang 语言
 * @property string $username
 * @property string $z_body
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class MasterLog extends Model{
	use SoftDelete;
	
}
?>