<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;

/**
 * Class app\common\model\MasterMenu
 *
 * @property int $fid
 * @property int $id
 * @property int $pass
 * @property int $px
 * @property int $ty
 * @property string $delete_time
 * @property string $el_icon
 * @property string $icon
 * @property string $ip 语言
 * @property string $lang 语言
 * @property string $link_url
 * @property string $link_url2
 * @property string $title
 * @property string $title2
 * @property string $wtime
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class MasterMenu extends Model{
	use SoftDelete;
	
}
?>