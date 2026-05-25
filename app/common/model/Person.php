<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\Person
 *
 * @property float $balance 会员折扣
 * @property float $discounts 会员折扣
 * @property int $id
 * @property int $login_num
 * @property int $pass
 * @property string $address
 * @property string $compname
 * @property string $delete_time
 * @property string $eip
 * @property string $email
 * @property string $etime
 * @property string $fax
 * @property string $img_sl
 * @property string $lang
 * @property string $lip
 * @property string $ltime
 * @property string $password
 * @property string $phone
 * @property string $post
 * @property string $qq
 * @property string $rename
 * @property string $sex
 * @property string $token
 * @property string $username
 * @property string $wip
 * @property string $wtime
 * @property string $wx
 * @property string $z_body
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class Person extends Model{
	use SoftDelete;
}

?>