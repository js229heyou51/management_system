<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;

/**
 * Class app\common\model\Master
 *
 * @property bool $pass
 * @property int $error_num
 * @property int $error_time
 * @property int $id
 * @property int $login_num
 * @property string $action_list
 * @property string $avatar 头像
 * @property string $delete_time
 * @property string $eip
 * @property string $etime
 * @property string $lang 语言
 * @property string $lip
 * @property string $ltime
 * @property string $menu_list
 * @property string $password
 * @property string $remember_token 保持登陆
 * @property string $rename
 * @property string $username
 * @property string $wip
 * @property string $wtime
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class Master extends Model{
	use SoftDelete;
	
}
?>