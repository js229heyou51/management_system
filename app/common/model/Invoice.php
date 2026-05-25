<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;

/**
 * Class app\common\model\Invoice
 *
 * @property bool $type
 * @property int $id
 * @property int $user_id
 * @property string $comp_address
 * @property string $comp_bank
 * @property string $comp_bank_num
 * @property string $comp_name
 * @property string $comp_num
 * @property string $comp_phone
 * @property string $delete_time
 * @property string $lang
 * @property string $post
 * @property string $spaddress
 * @property string $spcity
 * @property string $spdistrict
 * @property string $spname
 * @property string $spphone
 * @property string $spprovince
 * @property string $wtime
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class Invoice extends Model{
	use SoftDelete;

}
?>