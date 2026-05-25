<?php  
// app/controller/VideoController.php
namespace app\admin\controller;

use app\common\service\VideoService;
use app\common\service\VideoCategoryService;
use app\common\validate\VideoValidate;
use think\facade\View;

class Video extends Base
{
	/**
	 * 视频列表
	 */
	public function index()
	{
		$params = request()->param();
		$page = request()->param('page/d', 1);
		$keyword = $params['params'] ?? [];
		if(request()->isPost()){
			$limit = request()->param('limit/d', 15);
			
			$lists = VideoService::getList(request()->param(), $page, $limit);
			
			return json([
				'code' => 200,
				'lists' => $lists,
				'msg' => 'success'
			]);
		}else{
			$lists = VideoService::getList($params,$page);

			$pages = $lists->render();

			$items = $lists->items();

			$category = VideoCategoryService::getTree(0);

			$options = VideoCategoryService::getOptions();
			View::assign([
				'category' => $category,
				'options' => $options,
				'lists' => $lists,
				'params' => $params,
				'pages' => $pages,
			]);
			return View::fetch();
		}
	}

	public function create(){

		$params = request()->param();

		$category = VideoCategoryService::getOptions();
		View::assign([
			'params' => $params,
			'category' => $category,
		]);
		return View::fetch();
	}
	
	/**
	 * 视频详情
	 */
	public function detail($id)
	{
		$video = VideoService::getDetail($id);
		if (!$video) {
			return json(['code' => 404, 'msg' => '视频不存在']);
		}
		return json(['code' => 0, 'data' => $video]);
	}
	
	/**
	 * 上传视频
	 */
	public function upload()
	{
		$file = request()->file('file');
		$data = request()->post();

		$result = VideoService::upload($file, $data);
		return json($result);
	}
	
	/**
	 * 批量上传
	 */
	public function batchUpload()
	{
		$files = request()->file('videos');
		$data = request()->post();
		
		if (!$files) {
			return json(['code' => 400, 'msg' => '请选择视频文件']);
		}
		
		$result = VideoService::batchUpload($files, $data);
		return json($result);
	}
	
	/**
	 * 删除视频
	 */
	public function delete($id)
	{
		$userId = request()->userId ?? 0; // 从token获取
		$result = VideoService::delete($id, $userId);
		return json($result);
	}
	
	/**
	 * 设置推荐
	 */
	public function setRecommend($id)
	{
		$isRecommend = request()->param('is_recommend/d', 1);
		$result = VideoService::setRecommend($id, $isRecommend);
		return json($result);
	}
	
	/**
	 * 视频分类树
	 */
	public function categoryTree()
	{
		$tree = VideoCategory::getTree();
		return json(['code' => 0, 'data' => $tree]);
	}
}
?>