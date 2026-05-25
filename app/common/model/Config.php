<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\Config
 *
 * @property int $id
 * @property string $delete_time
 * @property string $lists
 * @property string $table_name
 * @property string $type
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class Config extends Model{
	use SoftDelete;

}

?>