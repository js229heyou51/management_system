<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;

/**
 * Class app\common\model\HomeCo
 *
 * @property array $img_sl
 * @property array $pic_sl
 * @property array $vid_sl
 * @property bool $ding
 * @property bool $hot
 * @property bool $pass
 * @property bool $tuijian
 * @property int $id
 * @property int $lm
 * @property int $px
 * @property int $read_num
 * @property string $apname
 * @property string $color_font
 * @property string $delete_time
 * @property string $f_body
 * @property string $fil_sl
 * @property string $info_author
 * @property string $info_from
 * @property string $ip
 * @property string $keyword
 * @property string $lang 语言
 * @property string $link_url
 * @property string $list_lm
 * @property string $title
 * @property string $wtime
 * @property string $ym_des
 * @property string $ym_key
 * @property string $ym_tit
 * @property string $z_body
 * @property-read \app\common\model\HomeLm $profile
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class HomeCo extends Model{
	use SoftDelete;
	// 定义一对一关联  
	public function profile()  
	{
		return $this->hasOne(HomeLm::class, 'id_lm', 'lm');  
	}

	protected $type = [
		'img_sl' => 'json',
		'pic_sl' => 'json',
		'vid_sl' => 'json',
	];
}
?>