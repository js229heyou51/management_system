<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;
use think\facade\Lang;

/**
 * Class app\common\model\SetupSy
 *
 * @property int $id
 * @property int $sy_id
 * @property string $config
 * @property string $delete_time
 * @property string $lang 语言
 * @property string $pre
 * @property string $r_email
 * @property string $title
 * @property string $ym_des
 * @property string $ym_key
 * @property string $ym_tit
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class SetupSy extends Model{
	use SoftDelete;
	
	public function getConfig($sy_id){
		$find = self::where('sy_id',$sy_id)->where('lang',Lang::getLangSet())->find();
		if(!empty($find->config)){
			return unserialize($find->config);
		}
		return [];
	}

	public function getBySyId($sy_id){
		return self::where('sy_id',$sy_id)->where('lang',Lang::getLangSet())->find();
	}

	public function getByWhere(?array $where = []){
		$query = self::where('lang',Lang::getLangSet());
		if(!empty($where)){
			$query->where($where);
		}
		// 排序
		$orderBy = $params['order'] ?? 'sy_id asc';
		$query->order($orderBy);
		return $query->select();
	}
}
?>