<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Cart extends Model{
	use SoftDelete;
}

?>
