<?php  
// app/model/GalleryCategory.php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\GalleryCategory
 *
 * @property int $cover_image_id 封面图片ID
 * @property int $delete_time
 * @property int $id
 * @property int $parent_id 父级ID
 * @property int $sort_order 排序
 * @property int $status 状态:1启用,0禁用
 * @property int $type 分类类型:1系统分类,2用户分类
 * @property int $user_id 创建用户ID
 * @property string $color 分类颜色
 * @property string $create_time
 * @property string $description 分类描述
 * @property string $icon 分类图标
 * @property string $name 分类名称
 * @property string $seo_data SEO数据(JSON)
 * @property string $slug 分类标识（URL友好）
 * @property string $update_time
 * @property-read \app\common\model\Gallery $cover_image
 * @property-read \app\common\model\GalleryCategory $parent
 * @property-read \app\common\model\GalleryCategory[] $all_children
 * @property-read \app\common\model\GalleryCategory[] $children
 * @property-read \app\common\model\Gallery[] $galleries
 * @property-read mixed $full_path
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class GalleryCategory extends Model
{
	// 使用软删除
	use SoftDelete;
	protected $deleteTime = 'delete_time';

	// 设置表名
	protected $table = 'gallery_category';

	// 自动写入时间戳
	protected $autoWriteTimestamp = true;
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';

	// 设置JSON字段
	protected $json = ['seo_data'];

	// 分类类型常量
	const TYPE_SYSTEM = 1;  // 系统分类
	const TYPE_USER = 2;    // 用户自定义分类

	// 状态常量
	const STATUS_ACTIVE = 1;
	const STATUS_DISABLED = 0;

	/**
	 * 父级分类关联
	 */
	public function parent()
	{
		return $this->belongsTo(self::class, 'parent_id', 'id');
	}

	/**
	 * 子分类关联
	 */
	public function children()
	{
		return $this->hasMany(self::class, 'parent_id', 'id')
			->where('status', self::STATUS_ACTIVE)
			->order('sort_order asc, id asc');
	}

	/**
	 * 获取所有子孙分类（无限级）
	 */
	public function allChildren()
	{
		return $this->hasMany(self::class, 'parent_id', 'id')
			->with(['allChildren']);
	}

	/**
	 * 关联图库图片
	 */
	public function galleries()
	{
		return $this->hasMany(Gallery::class, 'category_id', 'id')
			->where('status', Gallery::STATUS_ACTIVE);
	}

	/**
	 * 获取分类封面图片
	 */
	public function coverImage()
	{
		return $this->belongsTo(Gallery::class, 'cover_image_id', 'id');
	}

	/**
	 * 获取分类完整路径（面包屑）
	 */
	public function getFullPathAttr()
	{
		$path = [];
		$category = $this;
		
		while ($category) {
			$path[] = [
				'id' => $category->id,
				'name' => $category->name,
				'slug' => $category->slug
			];
			$category = $category->parent;
		}
		
		return array_reverse($path);
	}

	/**
	 * 获取分类树
	 */
	public static function getTree($parentId = 0, $maxLevel = null, $currentLevel = 1)
	{
		$query = self::where('parent_id', $parentId)
			->where('status', self::STATUS_ACTIVE)
			->order('sort_order asc, id asc');
		
		$categories = $query->select();
		
		if ($maxLevel && $currentLevel >= $maxLevel) {
			return $categories;
		}
		
		foreach ($categories as &$category) {
			$category->children = self::getTree($category->id, $maxLevel, $currentLevel + 1);
		}
		
		return $categories;
	}

	/**
	 * 获取分类选项（用于下拉选择）
	 */
	public static function getCategoryOptions($parentId = 0, $prefix = '', $excludeId = null)
	{
		$options = [];
		
		$query = self::where('parent_id', $parentId)
			->where('status', self::STATUS_ACTIVE);
		
		if ($excludeId) {
			$query->where('id', '<>', $excludeId);
		}
		
		$categories = $query->order('sort_order asc, id asc')->select();
		
		foreach ($categories as $category) {
			$options[$category->id] = $prefix . $category->name;
			$childOptions = self::getCategoryOptions($category->id, $prefix . '├─ ', $excludeId);
			$options += $childOptions;
		}
		return $options;
	}

	/**
	 * 获取分类下所有子分类ID（包括自身）
	 */
	public static function getAllChildrenIds($categoryId, $includeSelf = true)
	{
		$ids = $includeSelf ? [(int)$categoryId] : [];
		
		$children = self::where('parent_id', $categoryId)
			->where('status', self::STATUS_ACTIVE)
			->column('id');
		
		foreach ($children as $childId) {
			$ids = array_merge($ids, self::getAllChildrenIds($childId, true));
		}
		
		return array_unique($ids);
	}
}
?>