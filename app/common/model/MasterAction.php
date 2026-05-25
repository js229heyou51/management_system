<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;

/**
 * Class app\common\model\MasterAction
 *
 * @property int $fid
 * @property int $id
 * @property int $pass
 * @property int $px
 * @property string $delete_time
 * @property string $ip 语言
 * @property string $lang 语言
 * @property string $title
 * @property string $title_val
 * @property string $wtime
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class MasterAction extends Model{
	use SoftDelete;
	
}
?>