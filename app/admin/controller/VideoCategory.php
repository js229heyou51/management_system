<?php  
namespace app\admin\controller;

use app\common\service\VideoCategoryService;
use think\facade\View;

class VideoCategory extends Base
{
	/**
	 * 分类列表（树形结构）
	 */
	public function index()
	{
		$parentId = request()->get('parent_id', 0);
		$withGalleriesCount = request()->get('with_galleries_count', 0);
		
		$tree = VideoCategoryService::getTree($parentId, $withGalleriesCount);
		
		return json([
			'code' => 200,
			'msg' => 'success',
			'data' => $tree
		]);
	}
	
	/**
	 * 创建分类
	 */
	public function create()
	{
		$data = request()->post();
		if(request()->isPost()){
			try {
				$category = VideoCategoryService::create($data);
				
				return json([
					'code' => 200,
					'msg' => '创建成功',
					'data' => $category
				]);
			} catch (\Exception $e) {
				return json([
					'code' => 500,
					'msg' => $e->getMessage()
				]);
			}
		}else{
			$params = request()->param();
			$category = VideoCategoryService::getOptions();
			View::assign([
				'category' => $category,
				'params' => $params,
			]);
			return View::fetch();
		}
	}
	
	/**
	 * 更新分类
	 */
	public function update($id)
	{
		if(request()->isPost()){
			try {
				$data = request()->post();
				$category = VideoCategoryService::update($id, $data);
				
				return json([
					'code' => 200,
					'msg' => '更新成功',
					'data' => $category
				]);
			} catch (\Exception $e) {
				return json([
					'code' => 500,
					'msg' => $e->getMessage()
				]);
			}
		}else{
			$params = request()->param();

			$find = [];
			if(!empty($params['id'])){
				$find = VideoCategoryService::getById($params['id']);
			}
			$category = VideoCategoryService::getOptions();
			View::assign([
				'params' => $params,
				'find' => $find,
				'category' => $category,
			]);
			return View::fetch('edit');
		}
	}
	
	/**
	 * 删除分类
	 */
	public function delete($id)
	{
		try {
			$force = request()->post('force', 0);
			VideoCategoryService::delete($id, $force);
			
			return json([
				'code' => 200,
				'msg' => '删除成功'
			]);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}
	
	/**
	 * 分类详情
	 */
	public function detail($id)
	{
		try {
			$category = VideoCategoryService::getDetail($id);
			
			return json([
				'code' => 200,
				'msg' => 'success',
				'data' => $category->append(['full_path'])->toArray()
			]);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}
	
	/**
	 * 移动分类
	 */
	public function move($id)
	{
		try {
			$newParentId = request()->post('parent_id', 0);
			$category = VideoCategoryService::move($id, $newParentId);
			
			return json([
				'code' => 200,
				'msg' => '移动成功',
				'data' => $category
			]);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}
	
	/**
	 * 获取分类选项
	 */
	public function options()
	{
		$excludeId = request()->get('exclude_id', 0);
		$options = VideoCategoryService::getOptions($excludeId);
		
		return json([
			'code' => 200,
			'msg' => 'success',
			'data' => $options
		]);
	}
	
	/**
	 * 更新排序
	 */
	public function updateSort()
	{
		try {
			$sortData = request()->post('sort_data', []);
			VideoCategoryService::updateSort($sortData);
			
			return json([
				'code' => 200,
				'msg' => '排序更新成功'
			]);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}
	
	/**
	 * 根据slug获取分类
	 */
	public function getBySlug($slug)
	{
		try {
			$category = VideoCategoryService::getBySlug($slug);
			
			return json([
				'code' => 200,
				'msg' => 'success',
				'data' => $category->append(['full_path'])->toArray()
			]);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}
	
	/**
	 * 批量操作
	 */
	public function batch()
	{
		try {
			$action = request()->post('action');
			$ids = request()->post('ids', []);
			
			switch ($action) {
				case 'enable':
					GalleryCategory::whereIn('id', $ids)->update(['status' => 1]);
					break;
					
				case 'disable':
					GalleryCategory::whereIn('id', $ids)->update(['status' => 0]);
					break;
					
				case 'delete':
					foreach ($ids as $id) {
						VideoCategoryService::delete($id);
					}
					break;
					
				default:
					throw new \Exception('不支持的操作');
			}
			
			return json([
				'code' => 200,
				'msg' => '操作成功'
			]);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}
}

?>