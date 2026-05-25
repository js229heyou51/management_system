<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;

/**
 * Class app\common\model\Book
 *
 * @property int $chakan
 * @property int $huifu
 * @property int $id
 * @property int $id_re
 * @property int $pass
 * @property string $address
 * @property string $compname
 * @property string $delete_time
 * @property string $email
 * @property string $fax
 * @property string $ip
 * @property string $lang
 * @property string $num
 * @property string $phone
 * @property string $post
 * @property string $qq
 * @property string $rename
 * @property string $sex
 * @property string $title
 * @property string $wtime
 * @property string $wx
 * @property string $z_body
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class Book extends Model{
	use SoftDelete;
	
}
?>