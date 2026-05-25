<?php  
// app/controller/GalleryCategoryController.php
namespace app\admin\controller;

use app\BaseController;
use app\common\service\GalleryCategoryService;
use think\facade\Request;
use think\facade\View;

class GalleryCategory extends Base
{
	/**
	 * 分类列表（树形结构）
	 */
	public function index()
	{
		$parentId = Request::get('parent_id', 0);
		$withGalleriesCount = Request::get('with_galleries_count', 0);
		
		$tree = GalleryCategoryService::getTree($parentId, $withGalleriesCount);
		
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
		$data = Request::post();
		if(Request::isPost()){
			try {
				$category = GalleryCategoryService::create($data);
				
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
			$params = Request::param();
			$category = GalleryCategoryService::getOptions();
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
		if(Request::isPost()){
			try {
				$data = Request::post();
				$category = GalleryCategoryService::update($id, $data);
				
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
			$params = Request::param();

			$find = [];
			if(!empty($params['id'])){
				$find = GalleryCategoryService::getById($params['id']);
			}
			$category = GalleryCategoryService::getOptions();
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
			$force = Request::post('force', 0);
			GalleryCategoryService::delete($id, $force);
			
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
			$category = GalleryCategoryService::getDetail($id);
			
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
			$newParentId = Request::post('parent_id', 0);
			$category = GalleryCategoryService::move($id, $newParentId);
			
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
		$excludeId = Request::get('exclude_id', 0);
		$options = GalleryCategoryService::getOptions($excludeId);
		
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
			$sortData = Request::post('sort_data', []);
			GalleryCategoryService::updateSort($sortData);
			
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
			$category = GalleryCategoryService::getBySlug($slug);
			
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
			$action = Request::post('action');
			$ids = Request::post('ids', []);
			
			switch ($action) {
				case 'enable':
					GalleryCategory::whereIn('id', $ids)->update(['status' => 1]);
					break;
					
				case 'disable':
					GalleryCategory::whereIn('id', $ids)->update(['status' => 0]);
					break;
					
				case 'delete':
					foreach ($ids as $id) {
						GalleryCategoryService::delete($id);
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