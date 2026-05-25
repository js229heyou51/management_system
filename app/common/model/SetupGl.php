<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;
use think\facade\Lang;

/**
 * Class app\common\model\SetupGl
 *
 * @property array $icon
 * @property array $logo
 * @property bool $key
 * @property bool $log
 * @property bool $rewrite
 * @property bool $sy_seo
 * @property int $id
 * @property string $address
 * @property string $delete_time
 * @property string $f_body
 * @property string $lang 语言
 * @property string $mlang
 * @property string $r_email
 * @property string $s_email
 * @property string $s_password
 * @property string $s_server
 * @property string $s_username
 * @property string $title
 * @property string $ym_bcode
 * @property string $ym_bot
 * @property string $ym_des
 * @property string $ym_hcode
 * @property string $ym_key
 * @property string $ym_tit
 * @property string $zuobiao
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class SetupGl extends Model{
	use SoftDelete;
	
	public function getById($id){
		return self::where('lang',Lang::getLangSet())->find($id);
	}

	protected $type = [
		'logo' => 'json',
		'icon' => 'json',
	];
}
?>