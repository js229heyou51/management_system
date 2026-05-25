<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;

/**
 * Class app\common\model\KeyCo
 *
 * @property bool $pass
 * @property int $id
 * @property int $px
 * @property string $delete_time
 * @property string $ip
 * @property string $lang 语言
 * @property string $link_url
 * @property string $title
 * @property string $wtime
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class KeyCo extends Model{
	use SoftDelete;
	
}
?>