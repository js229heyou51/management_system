<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\PersonAddress
 *
 * @property bool $type
 * @property int $id
 * @property int $user_id
 * @property string $address
 * @property string $city
 * @property string $delete_time
 * @property string $district
 * @property string $lang
 * @property string $phone
 * @property string $post
 * @property string $province
 * @property string $rename
 * @property string $wtime
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class PersonAddress extends Model{
	use SoftDelete;

	// public function person(){
	// 	return $this->hasOne(Person::class, 'id', 'uid');  
	// }
}

?>