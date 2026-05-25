<?php  
// app/common/service/UploadService.php
namespace app\common\service;

use think\facade\Filesystem;
use think\File;
use think\exception\ValidateException;

class UploadService
{
	/**
	 * 允许的文件类型分组（可自定义）
	 */
	const TYPE_IMAGE   = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
	const TYPE_VIDEO   = ['mp4', 'mov', 'avi', 'wmv', 'flv', 'mkv', 'webm'];
	const TYPE_AUDIO   = ['mp3', 'wav', 'ogg', 'aac'];
	const TYPE_DOC     = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
	const TYPE_ARCHIVE = ['zip', 'rar', '7z', 'tar', 'gz'];

	/**
	 * 上传文件
	 * @param File $file 上传文件对象
	 * @param array $options 配置选项
	 *        - allowed_exts: array 允许的扩展名列表，如 ['jpg','png']
	 *        - allowed_mimes: array 允许的MIME类型列表（可选）
	 *        - max_size: int 最大文件大小（字节），默认 20MB
	 *        - save_path: string 存储子目录，如 'images'、'videos'
	 *        - disk: string 使用的磁盘，默认 'public'
	 *        - rename: bool|callable 是否重命名，默认 true（自动生成唯一名）
	 * @return array 返回上传结果，包含 path, url, name, size, ext, mime
	 * @throws ValidateException
	 */
	public static function upload(File $file, array $options = []): array
	{
		// 合并默认配置
		$default = [
			'allowed_exts' => [],
			'allowed_mimes' => [],
			'max_size'      => 20 * 1024 * 1024, // 20MB
			'save_path'     => 'uploads',
			'disk'          => 'public',
			'rename'        => true,
		];
		$options = array_merge($default, $options);

		// 1. 验证文件是否上传
		if (!$file) {
			throw new ValidateException('未选择文件');
		}

		// 2. 验证文件大小
		if ($file->getSize() > $options['max_size']) {
			$max = round($options['max_size'] / 1024 / 1024, 2);
			throw new ValidateException("文件大小不能超过 {$max} MB");
		}

		// 3. 验证扩展名
		$ext = strtolower($file->extension());
		if (!empty($options['allowed_exts']) && !in_array($ext, $options['allowed_exts'])) {
			$allowStr = implode(',', $options['allowed_exts']);
			throw new ValidateException("不支持的文件类型，仅支持: {$allowStr}");
		}

		// 4. 验证MIME类型（可选，更严格）
		if (!empty($options['allowed_mimes'])) {
			$mime = $file->getMime();
			if (!in_array($mime, $options['allowed_mimes'])) {
				$allowStr = implode(',', $options['allowed_mimes']);
				throw new ValidateException("不支持的文件MIME类型，仅支持: {$allowStr}");
			}
		}

		// 5. 生成保存路径（自动按日期分目录）
		$saveDir = trim($options['save_path'], '/') . '/' . date('Y/m/d');
		$saveName = null;

		if ($options['rename'] === true) {
			$customName = uniqid() . '.' . $ext;
			// 自动生成唯一文件名
			$saveName = Filesystem::disk($options['disk'])->putFileAs($saveDir, $file, $customName);
		} elseif (is_callable($options['rename'])) {
			// 自定义文件名生成规则
			$customName = call_user_func($options['rename'], $file);
			$saveName = $saveDir . '/' . $customName . '.' . $ext;
			Filesystem::disk($options['disk'])->put($saveName, file_get_contents($file->getRealPath()));
		} else {
			// 保留原文件名
			$originalName = $file->getOriginalName();
			$saveName = $saveDir . '/' . $originalName;
			Filesystem::disk($options['disk'])->put($saveName, file_get_contents($file->getRealPath()));
		}

		// 6. 获取访问URL
		$url = Filesystem::disk($options['disk'])->url($saveName);

		// 7. 返回文件信息
		return [
			'path' => $saveName,
			'url'  => $url,
			'name' => $file->getOriginalName(),
			'size' => $file->getSize(),
			'ext'  => $ext,
			'mime' => $file->getMime(),
		];
	}

	/**
	 * 便捷方法：上传图片
	 */
	public static function uploadImage(File $file, array $options = []): array
	{
		$options['allowed_exts'] = self::TYPE_IMAGE;
		$options['save_path'] = $options['save_path'] ?? 'images';
		return self::upload($file, $options);
	}

	/**
	 * 便捷方法：上传视频
	 */
	public static function uploadVideo(File $file, array $options = []): array
	{
		$options['allowed_exts'] = self::TYPE_VIDEO;
		$options['save_path'] = $options['save_path'] ?? 'videos';
		return self::upload($file, $options);
	}

	// 可继续添加 uploadAudio、uploadDoc 等
}
?>