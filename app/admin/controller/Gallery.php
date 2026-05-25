<?php  
// app/controller/GalleryController.php
namespace app\admin\controller;

use think\facade\View;
use think\facade\Request;
use think\facade\Db;
use think\Response;
use app\common\service\GalleryService;
use app\common\service\GalleryCategoryService;
use app\common\model\SetupSy as MS;

class Gallery extends Base
{
	protected $middleware = ['AdminCheck','AdminPermission'];
	protected $conf = [];
	protected $route;

	protected function initialize() {
		parent::initialize();
		$this->route = request()->controller();
		View::assign([
			'route' => $this->route,
			'conf' => $this->conf,
		]);
	}

	public function index(){

		$params = request()->param();
		$page = $params['page'] ?? '1';
		$keyword = $params['params'] ?? [];
		if(request()->isPost()){
			$lists = GalleryService::getList($params,$page);
			$pages = $lists->render();
			$items = $lists->items();
			$photos = array_map(function($item){
				return [
					'alt' => $item['title'],
					'pid' => $item['id'],
					'src' => $item['path']
				];
			}, $items);
			return json(['code' => 200, 'lists' => $lists, 'pages' => $pages, 'params' => $params,'photos' => $photos,]);
		}else{
			$lists = GalleryService::getList($params,$page);
			$pages = $lists->render();

			$items = $lists->items();
			$photos = array_map(function($item){
				return [
					'alt' => $item['title'],
					'pid' => $item['id'],
					'src' => $item['path']
				];
			}, $items);

			$category = GalleryCategoryService::getTree(0);

			$options = GalleryCategoryService::getOptions();
			View::assign([
				'category' => $category,
				'options' => $options,
				'lists' => $lists,
				'params' => $params,
				'pages' => $pages,
				'photos' => $photos,
			]);
			return View::fetch();
		}
	}

	public function create(){

		$params = request()->param();

		// $category = GalleryCategoryService::getTree(0);

		$category = GalleryCategoryService::getOptions();
		View::assign([
			'params' => $params,
			'category' => $category,
		]);
		return View::fetch();
	}

	public function edit(){
		$params = request()->param();
		$id = $params['id'] ?? '';
		if(empty($id)){
			return json(['code' => 400, 'msg' => '参数错误']);
		}
		try{
			$update = GalleryService::update($params);
			return json(['code' => 200, 'msg' => '修改成功']);
		}catch(\Exception $e){
			return json(['code'=> 400, 'msg' => $e->getMessage()]);
		}
	}

	/**
	 * 上传图片
	 */
	public function upload()
	{
		$files = request()->file();
		if (empty($files)) {
			return json(['code' => 400, 'msg' => '请选择要上传的图片']);
		}
		
		$data = request()->post();
		$data['user_id'] = request()->middleware('user_id') ?? 0;
		// 单文件上传
		if (count($files) === 1) {
			$file = current($files);
			$result = GalleryService::upload($file, $data);
			
			if ($result) {
				return json([
					'code' => 200,
					'msg' => '上传成功',
					'data' => [
						'id' => $result->id,
						'title' => $result->title,
						'url' => $result->full_url,
						'thumbnail_url' => $result->thumbnail_url
					]
				]);
			}
		} else {
			// 多文件上传
			$results = GalleryService::batchUpload($files, $data);
			$success = array_filter($results);
			
			return json([
				'code' => 200,
				'msg' => sprintf('上传完成，成功%s张，失败%s张', count($success), count($results) - count($success)),
				'data' => array_map(function($item) {
					return [
						'id' => $item->id,
						'url' => $item->full_url,
						'thumbnail_url' => $item->thumbnail_url
					];
				}, $success)
			]);
		}
		
		return json(['code' => 500, 'msg' => '上传失败']);
	}
	
	/**
	 * 图片列表
	 */
	public function list()
	{
		$params = request()->get();
		$page = request()->get('page', 1);
		$limit = request()->get('limit', 20);
		
		$result = GalleryService::getList($params, $page, $limit);
		
		return json([
			'code' => 200,
			'msg' => 'success',
			'data' => [
				'list' => $result->items(),
				'total' => $result->total(),
				'page' => $result->currentPage(),
				'limit' => $result->listRows()
			]
		]);
	}
	
	/**
	 * 获取图片详情
	 */
	public function detail($id)
	{
		$gallery = \app\common\model\Gallery::find($id);
		
		if (!$gallery || $gallery->status != \app\common\model\Gallery::STATUS_ACTIVE) {
			return json(['code' => 404, 'msg' => '图片不存在']);
		}
		
		return json([
			'code' => 200,
			'msg' => 'success',
			'data' => $gallery->append(['full_url', 'thumbnail_url'])->toArray()
		]);
	}
	
	/**
	 * 删除图片（软删除）
	 */
	public function delete($id)
	{
		$gallery = \app\common\model\Gallery::find($id);
		
		if (!$gallery) {
			return json(['code' => 404, 'msg' => '图片不存在']);
		}
		
		$gallery->status = \app\common\model\Gallery::STATUS_DELETED;
		$gallery->delete_time = time();
		$gallery->save();
		
		return json(['code' => 200, 'msg' => '删除成功']);
	}
	/**
	 * 批量删除图片（事务处理）
	 */
	public function batchDelete(){
		// 开启事务
		Db::startTrans();
		try {
			$ids = request()->param('ids/a');
			if (empty($ids) || !is_array($ids)) {
				return json(['code' => 400, 'msg' => '请选择要删除的图片']);
			}
			// 验证每个ID是否存在
			$existsIds = \app\common\model\Gallery::whereIn('id', $ids)
			->where('status', '<>', \app\common\model\Gallery::STATUS_DELETED)
			->column('id');
			if (empty($existsIds)) {
				return json(['code' => 404, 'msg' => '未找到可删除的图片']);
			}
			// 批量软删除
			$result = \app\common\model\Gallery::whereIn('id', $existsIds)
				->update([
					'status' => \app\common\model\Gallery::STATUS_DELETED,
					'delete_time' => time()
				]);
			// 提交事务
			Db::commit();

			return json([
				'code' => 200, 
				'msg' => '删除成功',
				'count' => $result
			]);
			
		} catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
			return json([
				'code' => 500, 
				'msg' => '删除失败: ' . $e->getMessage()
			]);
		}
	}

	/**
	 * 批量移动图片（事务处理）
	 */
	public function batchMove(){
		// 开启事务
		Db::startTrans();
		try {
			$ids = request()->param('ids/a');
			$category_id = request()->param('category_id');
			if (empty($ids) || !is_array($ids)) {
				return json(['code' => 400, 'msg' => '请选择要移动的图片']);
			}
			// 验证每个ID是否存在
			$existsIds = \app\common\model\Gallery::whereIn('id', $ids)
			->where('status', '<>', \app\common\model\Gallery::STATUS_DELETED)
			->column('id');

			if (empty($existsIds)) {
				return json(['code' => 404, 'msg' => '未找到可移动的图片']);
			}
			// 批量移动
			$result = \app\common\model\Gallery::whereIn('id', $existsIds)
				->update([
					'category_id' => $category_id
				]);
			// 提交事务
			Db::commit();

			return json([
				'code' => 200, 
				'msg' => '移动成功',
				'count' => $result
			]);

		} catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
			return json([
				'code' => 500, 
				'msg' => '移动失败: ' . $e->getMessage()
			]);
		}
	}
	
	/**
	 * 直接访问图片（防止盗链）
	 */
	public function image($path)
	{
		$fullPath = public_path() . '' . $path;
		
		if (!file_exists($fullPath) || !is_file($fullPath)) {
			abort(404, '图片不存在');
		}
		
		// 验证访问权限（可根据需要添加）
		$referer = request()->header('referer');
		if (!$this->checkReferer($referer)) {
			// 返回默认图片或403
			$fullPath = public_path() . 'storage/gallery/default.jpg';
		}
		
		return Response::create(file_get_contents($fullPath))
			->contentType(mime_content_type($fullPath))
			->cacheControl('max-age=31536000'); // 缓存一年
	}
	
	private function checkReferer($referer)
	{
		// 检查来源域名是否合法
		// 这里可以添加自己的验证逻辑
		return true; // 暂时全部允许
	}
}
?>