<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;
use think\facade\Lang;

/**
 * Class app\common\model\HomeLm
 *
 * @property array $img_sl_lm
 * @property bool $hot
 * @property bool $pass
 * @property bool $tuijian
 * @property int $fid
 * @property int $id_lm
 * @property int $level_lm
 * @property int $px
 * @property string $add_xia
 * @property string $add_xx
 * @property string $apname_lm
 * @property string $con_att
 * @property string $delete_time
 * @property string $f_body_lm
 * @property string $info_apname
 * @property string $info_duotu
 * @property string $info_f_body
 * @property string $info_fil_sl
 * @property string $info_file
 * @property string $info_from
 * @property string $info_img_sl
 * @property string $info_img_sm
 * @property string $info_img_txt
 * @property string $info_info
 * @property string $info_keyword
 * @property string $info_link
 * @property string $info_pic_sl
 * @property string $info_pic_sm
 * @property string $info_pic_txt
 * @property string $info_vid_sl
 * @property string $info_video
 * @property string $info_wtime
 * @property string $info_z_body
 * @property string $info_zu
 * @property string $ip
 * @property string $lang 语言
 * @property string $list_lm
 * @property string $title_lm
 * @property string $url_lm
 * @property string $wtime
 * @property string $ym_des
 * @property string $ym_key
 * @property string $ym_tit
 * @property string $z_body_lm
 * @property-read \app\common\model\HomeCo[] $info
 * @property-read \app\common\model\HomeCo[] $used_info
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class HomeLm extends Model{
	use SoftDelete;
	
	protected $pk = 'id_lm';

	// 状态常量
	const STATUS_ACTIVE = 1;
	const STATUS_DISABLED = 0;

	// 关联文章表（一对多）
	public function info(){
		return $this->hasMany(HomeCo::class, 'lm');
		// 'lm'是HomeCo中指向HomeLm的外键字段
	}
	// 关联文章表（一对多）
	public function usedInfo(){
		return $this->hasMany(HomeCo::class, 'lm');
		// 'lm'是HomeCo中指向HomeLm的外键字段
	}

	/**
	 * 获取分类树
	 */
	public static function getTree($parentId = 0, $maxLevel = null, $currentLevel = 1)
	{
		$query = self::where('fid', $parentId)
			->where('lang', Lang::getLangSet())
			->order('px desc, id_lm asc');
		
		$categories = $query->select();
		
		if ($maxLevel && $currentLevel >= $maxLevel) {
			return $categories;
		}
		
		foreach ($categories as &$category) {
			$category->children = self::getTree($category->id_lm, $maxLevel, $currentLevel + 1);
		}
		
		return $categories;
	}

	/**
	 * 获取分类下所有子分类ID（包括自身）
	 */
	public static function getAllChildrenIds($categoryId, $includeSelf = true)
	{
		$ids = $includeSelf ? [(int)$categoryId] : [];
		
		$children = self::where('fid', $categoryId)
			->column('id');
		
		foreach ($children as $childId) {
			$ids = array_merge($ids, self::getAllChildrenIds($childId, true));
		}
		
		return array_unique($ids);
	}

	protected $type = [
		'img_sl_lm' => 'json',
	];
	
}
?>