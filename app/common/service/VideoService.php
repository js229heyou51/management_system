<?php  
// app/common/service/VideoService.php
namespace app\common\service;

use app\common\model\Video;
use app\common\model\VideoCategory;
use think\facade\Filesystem;
use think\facade\Db;

class VideoService
{
	/**
	 * 上传视频
	 * @param \think\File $file 视频文件
	 * @param array $data 额外数据（标题、分类等）
	 * @return array
	 */
	public static function upload($file, array $data = [])
	{
		try {
			if (!$file) {
				throw new \Exception('请选择视频文件');
			}
			// 验证文件类型
			$allowedExt = ['mp4', 'mov', 'avi', 'wmv', 'flv', 'mkv', 'webm'];
			$ext = strtolower($file->extension());
			if (!in_array($ext, $allowedExt)) {
				throw new \Exception('不支持的视频格式，允许格式：' . implode(',', $allowedExt));
			}
			// 验证文件大小（默认200MB）
			$maxSize = config('video.max_size', 200 * 1024 * 1024);

			$result = UploadService::upload($file, [
				'allowed_exts' => $allowedExt,
				'max_size'     => $maxSize,
				'save_path'    => 'video',
			]);

			$relativePath = $result['url'];
			// 生成存储路径
			// $saveName = Filesystem::disk('public')->putFile('video/' . date('Ymd'), $file);
			
			// 获取视频信息（可选，需要安装ffmpeg）
			$videoInfo = self::getVideoInfo($file->getPathname());
			
			// 保存到数据库
			$video = new Video();
			$video->title = $data['title'] ?? $file->getOriginalName();
			$video->description = $data['description'] ?? '';
			$video->video_path = $relativePath;
			$video->video_url = $relativePath;
			$video->size = $file->getSize();
			$video->format = $ext;
			$video->duration = $videoInfo['duration'] ?? 0;
			$video->width = $videoInfo['width'] ?? 0;
			$video->height = $videoInfo['height'] ?? 0;
			$video->category_id = $data['category_id'] ?? 0;
			$video->user_id = $data['user_id'] ?? 0;
			$video->status = 1;
			$video->save();
			
			// 处理封面图（如果有）
			if (!empty($data['cover_image'])) {
				$video->cover_image = $data['cover_image'];
				$video->save();
			} elseif (isset($videoInfo['frame'])) {
				// 可以使用第一帧作为封面
				$coverPath = self::extractCover($video->video_path, $video->id);
				$video->cover_image = $coverPath;
				$video->save();
			}
			
			return [
				'code' => 200,
				'msg'  => '上传成功',
				'data' => $video->toArray()
			];
			
		} catch (\Exception $e) {
			return [
				'code' => 500,
				'msg'  => $e->getMessage(),
				'data' => []
			];
		}
	}
	
	/**
	 * 批量上传
	 */
	public static function batchUpload(array $files, array $data = [])
	{
		$results = [];
		$successCount = 0;
		$failCount = 0;
		
		foreach ($files as $file) {
			$result = self::upload($file, $data);
			if ($result['code'] == 200) {
				$successCount++;
				$results[] = $result['data'];
			} else {
				$failCount++;
			}
		}
		
		return [
			'code' => $successCount > 0 ? 200 : 500,
			'msg'  => "上传完成：成功{$successCount}个，失败{$failCount}个",
			'data' => [
				'success' => $successCount,
				'fail' => $failCount,
				'list' => $results
			]
		];
	}
	
	/**
	 * 删除视频
	 */
	public static function delete($id, $userId = 0)
	{
		Db::startTrans();
		try {
			$video = Video::find($id);
			if (!$video) {
				throw new \Exception('视频不存在');
			}
			
			// 权限检查
			if ($userId > 0 && $video->user_id != $userId) {
				throw new \Exception('无权删除该视频');
			}
			
			// 删除物理文件
			if ($video->video_path && file_exists(public_path() . $video->video_path)) {
				@unlink(public_path() . $video->video_path);
			}
			if ($video->cover_image && file_exists(public_path() . $video->cover_image)) {
				@unlink(public_path() . $video->cover_image);
			}
			
			// 软删除
			$video->delete(true);
			
			Db::commit();
			return ['code' => 200, 'msg' => '删除成功', 'data' => ['id' => $id]];
			
		} catch (\Exception $e) {
			Db::rollback();
			return ['code' => 500, 'msg' => $e->getMessage()];
		}
	}
	
	/**
	 * 获取视频列表
	 */
	public static function getList(array $params = [], $page = 1, $limit = 9)
	{
		$query = Video::where('status', 1);
		// 分类筛选
		if (!empty($params['category_id'])) {
			// 包含子分类
			$categoryIds = VideoCategory::getTree($params['category_id']);
			$categoryIds[] = $params['category_id'];
			$query->whereIn('category_id', $categoryIds);
		}
		
		// 推荐筛选
		if (isset($params['is_recommend']) && $params['is_recommend'] == 1) {
			$query->where('is_recommend', 1);
		}
		
		// 关键词搜索
		if (!empty($params['keyword'])) {
			$query->whereLike('title|description', '%' . $params['keyword'] . '%');
		}
		
		// 标签搜索（如果tags是JSON）
		if (!empty($params['tag'])) {
			$query->whereRaw("JSON_CONTAINS(tags, '\"{$params['tag']}\"')");
		}
		
		// 排序
		$orderBy = $params['order'] ?? 'create_time desc';
		$query->order($orderBy);
		
		return $query->paginate([
			'list_rows' => $limit,
			'page' => $page
		]);
	}
	
	/**
	 * 获取视频详情（增加播放计数）
	 */
	public static function getDetail($id)
	{
		$video = Video::with(['category', 'user'])->find($id);
		if ($video) {
			$video->incrementViewCount();
		}
		return $video;
	}
	
	/**
	 * 设置推荐
	 */
	public static function setRecommend($id, $isRecommend = 1)
	{
		$video = Video::find($id);
		if (!$video) {
			return ['code' => 404, 'msg' => '视频不存在'];
		}
		$video->is_recommend = $isRecommend;
		$video->save();
		return ['code' => 200, 'msg' => '操作成功'];
	}
	
	// 以下为辅助方法（需要服务器安装ffmpeg）
	
	/**
	 * 获取视频信息（需要ffmpeg）
	 */
	protected static function getVideoInfo($filePath)
	{
		if (!extension_loaded('ffmpeg')) {
			// 如果没有ffmpeg扩展，可以返回默认值
			return ['duration' => 0, 'width' => 0, 'height' => 0];
		}
		
		// 使用FFmpeg获取信息（具体实现略）
		// 可以通过执行命令行获取：ffprobe -v quiet -print_format json -show_format -show_streams
		return [
			'duration' => 0,
			'width' => 0,
			'height' => 0,
		];
	}
	
	/**
	 * 提取视频封面（需要ffmpeg）
	 */
	protected static function extractCover($videoPath, $videoId)
	{
		// 生成封面文件名
		$coverPath = 'video/covers/' . $videoId . '_' . time() . '.jpg';
		$fullCoverPath = public_path() . '/uploads/' . $coverPath;
		
		// 确保目录存在
		$coverDir = dirname($fullCoverPath);
		if (!is_dir($coverDir)) {
			mkdir($coverDir, 0755, true);
		}
		
		// 执行ffmpeg命令：ffmpeg -i input.mp4 -ss 00:00:01 -vframes 1 output.jpg
		$fullVideoPath = public_path() . $videoPath;
		$cmd = "ffmpeg -i {$fullVideoPath} -ss 00:00:01 -vframes 1 {$fullCoverPath} 2>&1";
		exec($cmd, $output, $returnCode);
		
		if ($returnCode === 0 && file_exists($fullCoverPath)) {
			return '/uploads/' . $coverPath;
		}
		
		return '';
	}
}
?>