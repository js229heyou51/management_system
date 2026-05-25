<?php  
namespace app\common\service;

use app\common\model\VideoCategory;
use app\common\model\Video;
use think\facade\Validate;
use think\exception\ValidateException;

class VideoCategoryService
{
	/**
	 * 创建分类
	 */
	public static function create(array $data)
	{
		// 验证数据
		$validate = Validate::rule([
			'name'     => 'require|max:100',
			'slug'     => 'regex:/^[a-z0-9\-]+$/|max:100',
			'parent_id' => 'number|min:0',
			'type'     => 'in:1,2',
			'status'   => 'in:0,1'
		]);
		
		if (!$validate->check($data)) {
			throw new ValidateException($validate->getError());
		}
		
		// 创建分类
		$category = new VideoCategory();
		$category->save(array_merge($data, [
			'user_id' => request()->middleware('user_id') ?? 0
		]));
		
		return $category;
	}
	
	/**
	 * 更新分类
	 */
	public static function update($id, array $data)
	{
		$category = VideoCategory::find($id);
		if (!$category) {
			throw new \Exception('分类不存在');
		}
		
		// 不能将分类设置为自己的子分类
		if (isset($data['parent_id'])) {
			if ($data['parent_id'] == $id) {
				throw new \Exception('不能将分类设置为自己的子分类');
			}
			
			// 检查是否设置了子孙分类为父级
			$allChildrenIds = VideoCategory::getAllChildrenIds($id, false);
			if (in_array($data['parent_id'], $allChildrenIds)) {
				throw new \Exception('不能将分类设置到子孙分类下');
			}
		}
		
		// 如果更新slug，检查是否重复
		if (isset($data['slug']) && $data['slug'] != $category->slug) {
			$exists = VideoCategory::where('slug', $data['slug'])
				->where('id', '<>', $id)
				->find();
			if ($exists) {
				throw new \Exception('分类标识已存在');
			}
		}
		
		$category->save($data);
		return $category;
	}
	
	/**
	 * 删除分类
	 */
	public static function delete($id, $force = false)
	{
		$category = VideoCategory::find($id);
		if (!$category) {
			throw new \Exception('分类不存在');
		}
		
		// 检查是否有子分类
		$childrenCount = VideoCategory::where('parent_id', $id)
			->where('status', VideoCategory::STATUS_ACTIVE)
			->count();
		
		if ($childrenCount > 0) {
			throw new \Exception('请先删除子分类');
		}
		
		// 检查分类下是否有图片
		$VideoCount = Video::where('category_id', $id)
			->where('status', Video::STATUS_ACTIVE)
			->count();
		
		if ($VideoCount > 0 && !$force) {
			throw new \Exception('分类下存在图片，无法删除');
		}
		
		if ($force) {
			// 物理删除
			$category->delete(true);
		} else {
			// 软删除
			$category->delete();
		}
		
		return true;
	}
	
	/**
	 * 获取分类树
	 */
	public static function getTree($parentId = 0, $withGalleriesCount = false)
	{
		$categories = VideoCategory::getTree($parentId);
		
		if ($withGalleriesCount) {
			self::attachGalleriesCount($categories);
		}
		
		return $categories;
	}
	
	/**
	 * 为分类树附加图片数量统计
	 */
	private static function attachGalleriesCount(&$categories)
	{
		foreach ($categories as &$category) {
			// 获取分类及其所有子分类的ID
			$categoryIds = VideoCategory::getAllChildrenIds($category->id);
			
			// 统计图片数量
			$VideoCount = Video::whereIn('category_id', $categoryIds)
				->where('status', Video::STATUS_ACTIVE)
				->count();
			
			$category->Video_count = $VideoCount;
			
			// 递归处理子分类
			if (!empty($category->children)) {
				self::attachGalleriesCount($category->children);
			}
		}
	}
	
	/**
	 * 获取分类选项
	 */
	public static function getOptions($excludeId = null)
	{
		return VideoCategory::getCategoryOptions(0, '', $excludeId);
	}
	
	/**
	 * 移动分类
	 */
	public static function move($id, $newParentId)
	{
		$category = VideoCategory::find($id);
		if (!$category) {
			throw new \Exception('分类不存在');
		}
		
		if ($newParentId == $id) {
			throw new \Exception('不能将分类移动到自己下面');
		}
		
		// 检查是否移动到子孙分类
		$allChildrenIds = VideoCategory::getAllChildrenIds($id, false);
		if (in_array($newParentId, $allChildrenIds)) {
			throw new \Exception('不能将分类移动到子孙分类下');
		}
		
		$category->parent_id = $newParentId;
		$category->save();
		
		return $category;
	}
	
	/**
	 * 获取分类详情（带统计信息）
	 */
	public static function getDetail($id)
	{
		$category = VideoCategory::with(['parent', 'coverImage'])
			->find($id);
		
		if (!$category) {
			throw new \Exception('分类不存在');
		}
		
		// 获取子分类数量
		$category->children_count = VideoCategory::where('parent_id', $id)
			->where('status', VideoCategory::STATUS_ACTIVE)
			->count();
		
		// 获取图片数量（仅当前分类）
		$category->Video_count = Video::where('category_id', $id)
			->where('status', Video::STATUS_ACTIVE)
			->count();
		
		// 获取所有子孙分类图片总数
		$allCategoryIds = VideoCategory::getAllChildrenIds($id);
		$category->total_Video_count = Video::whereIn('category_id', $allCategoryIds)
			->where('status', Video::STATUS_ACTIVE)
			->count();
		
		return $category;
	}
	
	/**
	 * 批量更新分类排序
	 */
	public static function updateSort(array $sortData)
	{
		foreach ($sortData as $item) {
			if (isset($item['id']) && isset($item['sort_order'])) {
				VideoCategory::where('id', $item['id'])
					->update(['sort_order' => $item['sort_order']]);
			}
		}
		
		return true;
	}
	
	/**
	 * 根据slug获取分类
	 */
	public static function getBySlug($slug)
	{
		$category = VideoCategory::where('slug', $slug)
			->where('status', VideoCategory::STATUS_ACTIVE)
			->find();
		
		if (!$category) {
			throw new \Exception('分类不存在');
		}
		
		return $category;
	}

	/**
	 * 根据id获取分类
	 */
	public static function getById($id)
	{
		$category = VideoCategory::where('id', $id)
			->where('status', VideoCategory::STATUS_ACTIVE)
			->find();
		
		if (!$category) {
			throw new \Exception('分类不存在');
		}
		
		return $category;
	}
}
?>