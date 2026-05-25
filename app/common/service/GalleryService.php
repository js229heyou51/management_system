<?php  
namespace app\common\service;

use think\facade\Filesystem;
use think\facade\Config;
use app\common\model\Gallery;
use think\file\UploadedFile;

class GalleryService
{
	/**
	 * 上传图片
	 * @param UploadedFile $file 上传的文件对象
	 * @param array $data 附加数据
	 * @return Gallery|false
	 */
	public static function upload(UploadedFile $file, array $data = [])
	{
		try {
			// 验证文件类型
			$allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
			if (!in_array($file->getMime(), $allowedMimes)) {
				throw new \Exception('不支持的文件类型');
			}
			
			// 计算文件MD5（用于去重）
			$md5 = md5_file($file->getRealPath());
			
			// 检查是否已存在相同图片
			$exists = Gallery::where('md5', $md5)->where('status', Gallery::STATUS_ACTIVE)->find();
			if ($exists) {
				// 如果已存在，返回已有记录（避免重复存储）
				return $exists;
			}

			// 验证文件大小（默认3MB）
			$maxSize = config('image.max_size', 3 * 1024 * 1024);

			$result = UploadService::upload($file, [
				'allowed_exts' => [],
				'allowed_mimes' => $allowedMimes,
				'max_size'     => $maxSize,
				'save_path'    => 'gallery',
			]);
			
			$relativePath = $result['url'];
			
			// 获取图片信息
			$imageInfo = getimagesize($file->getRealPath());
			
			// 创建图库记录
			$gallery = new Gallery();
			$gallery->save([
				'title' => $data['title'] ?? $file->getOriginalName(),
				'filename' => $file->getOriginalName(),
				'path' => $relativePath,
				'size' => $file->getSize(),
				'mime_type' => $file->getMime(),
				'upload_type' => $data['upload_type'],
				'extension' => $file->getOriginalExtension(),
				'width' => $imageInfo[0] ?? 0,
				'height' => $imageInfo[1] ?? 0,
				'md5' => $md5,
				'category_id' => $data['category_id'] ?? 0,
				'user_id' => $data['user_id'] ?? 0,
				'description' => $data['description'] ?? '',
				'meta_data' => json_encode([
					'original_name' => $file->getOriginalName(),
					'upload_ip' => request()->ip(),
					'user_agent' => request()->header('user-agent')
				])
			]);
			// 生成缩略图（如果需要）
			// self::generateThumbnail($relativePath);
			
			return $gallery;
		} catch (\Exception $e) {
			// 记录错误日志
			\think\facade\Log::error('图片上传失败: ' . $e->getMessage());
			return false;
		}
	}
	
	/**
	 * 生成缩略图
	 */
	private static function generateThumbnail(string $imagePath)
	{
		$fullPath = public_path() . 'storage/gallery/' . $imagePath;
		
		if (!file_exists($fullPath)) {
			return false;
		}
		
		try {
			// 使用ThinkPHP的图像处理类
			$image = \think\Image::open($fullPath);
			
			// 生成缩略图
			$thumbPath = dirname($fullPath) . '/thumbnails';
			if (!is_dir($thumbPath)) {
				mkdir($thumbPath, 0755, true);
			}
			
			$thumbName = pathinfo($imagePath, PATHINFO_FILENAME) . '_thumb.' . pathinfo($imagePath, PATHINFO_EXTENSION);
			$thumbFullPath = $thumbPath . '/' . $thumbName;
			
			// 缩放到最大300x300
			$image->thumb(300, 300)->save($thumbFullPath);
			
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}
	
	/**
	 * 批量上传
	 */
	public static function batchUpload(array $files, array $data = [])
	{
		$results = [];
		foreach ($files as $file) {
			$result = self::upload($file, $data);
			$results[] = $result !== false ? $result : null;
		}
		return $results;
	}
	
	/**
	 * 获取图片列表
	 */
	public static function getList(array $params = [], int $page = 1, int $limit = 18)
	{

		// with([
		// 	'category' => function($query){
		// 		$query->with(['children'])->field('id,parent_id');
		// 	}])->
		$query = Gallery::where('status', Gallery::STATUS_ACTIVE);
		
		// 条件筛选
		if (!empty($params['category_id'])) {
			$categoryIds = \app\common\model\GalleryCategory::getAllChildrenIds($params['category_id']);
			if (!empty($categoryIds)) {
				$query->whereIn('category_id', $categoryIds);
			} else {
				$query->where('category_id', $params['category_id']);
			}
		}
		
		if (!empty($params['user_id'])) {
			$query->where('user_id', $params['user_id']);
		}
		
		if (!empty($params['keyword'])) {
			$query->whereLike('title|description', '%' . $params['keyword'] . '%');
		}
		
		// 排序
		$orderBy = $params['order'] ?? 'create_time desc';
		$query->order($orderBy);

		return $query->paginate([
			'list_rows' => $limit,
			'page' => $page
		]);
	}

	public static function update(array $params = []){

		try{
			Gallery::update($params);
			return true;
		}catch(\Exception $e){
			\think\facade\Log::error('更新失败: ' . $e->getMessage());
			return false;
		}

		
	}
}
?>