<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\Pl_image
 *
 * @property int $g_id
 * @property int $id
 * @property int $pass
 * @property int $pl_id
 * @property int $px
 * @property int $sy_id
 * @property string $delete_time
 * @property string $img_sl
 * @property string $lang 语言
 * @property string $title
 * @property string $wtime
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class Pl_image extends Model{

	use SoftDelete;

	
	
}

?>