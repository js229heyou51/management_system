<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;
use think\facade\Lang;

/**
 * Class app\common\model\BrandLm
 *
 * @property array $img_sl_lm 图片
 * @property bool $hot 热门
 * @property bool $pass 屏蔽
 * @property bool $tuijian 推荐
 * @property int $fid 上一级
 * @property int $id_lm id_lm
 * @property int $level_lm 所有父级
 * @property int $px 排序
 * @property string $add_xia 是否有下一级分类
 * @property string $add_xx 分类是否可以添加信息
 * @property string $apname_lm 页面名称
 * @property string $con_att 分类属性
 * @property string $delete_time 删除时间
 * @property string $f_body_lm 简要介绍
 * @property string $ip ip
 * @property string $lang 语言
 * @property string $list_lm 所有父级
 * @property string $pic_sl_lm 图片2
 * @property string $title_lm 标题
 * @property string $url_lm 跳转链接
 * @property string $wtime 创建时间
 * @property string $ym_des seo介绍
 * @property string $ym_key seo关键词
 * @property string $ym_tit seo标题
 * @property string $z_body_lm 详细介绍
 * @property-read \app\common\model\BrandCo[] $info
 * @property-read \app\common\model\BrandCo[] $used_info
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class BrandLm extends Model{
	use SoftDelete;

	protected $pk = 'id_lm';  
	// 状态常量
	const STATUS_ACTIVE = 1;
	const STATUS_DISABLED = 0;

	// 关联文章表（一对多）
	public function info(){
		return $this->hasMany(BrandCo::class, 'lm');
		// 'lm_id'是BrandCo中指向BrandLm的外键字段
	}
	// 关联文章表（一对多）
	public function usedInfo(){
		return $this->hasMany(BrandCo::class, 'lm');
		// 'lm_id'是BrandCo中指向BrandLm的外键字段
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