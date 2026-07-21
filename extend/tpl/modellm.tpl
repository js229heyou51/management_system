<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;
use think\facade\Lang;

class {{$table}}Lm extends Model{
	use SoftDelete;

	protected $pk = 'id_lm';  
	// 状态常量
	const STATUS_ACTIVE = 1;
	const STATUS_DISABLED = 0;

	// 关联文章表（一对多）
	public function info(){
		return $this->hasMany({{$table}}Co::class, 'lm');
		// 'lm_id'是{{$table}}Co中指向{{$table}}Lm的外键字段
	}
	// 关联文章表（一对多）
	public function usedInfo(){
		return $this->hasMany({{$table}}Co::class, 'lm');
		// 'lm_id'是{{$table}}Co中指向{{$table}}Lm的外键字段
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