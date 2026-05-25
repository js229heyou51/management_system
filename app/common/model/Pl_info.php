<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\Pl_info
 *
 * @property array $img_sl
 * @property int $id
 * @property int $pass
 * @property int $pl_id
 * @property int $px
 * @property int $read_num
 * @property int $sy_id
 * @property string $apname
 * @property string $delete_time
 * @property string $ip
 * @property string $lang 语言
 * @property string $link_url
 * @property string $price
 * @property string $title
 * @property string $wtime
 * @property string $ym_des
 * @property string $ym_key
 * @property string $ym_tit
 * @property string $z_body
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class Pl_info extends Model{
	use SoftDelete;

	// 设置表名
	protected $table = 'pl_info';

	public $sesname = 'demo_info_id';

	// 状态常量
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 0;

	protected $type = [
		'img_sl' => 'json',
	];
}

?>