<?php 
// app/model/Gallery.php
namespace app\common\model;

use think\Model;

/**
 * Class app\common\model\Gallery
 *
 * @property int $category_id 分类ID
 * @property int $delete_time
 * @property int $height 图片高度
 * @property int $id
 * @property int $size 文件大小(字节)
 * @property int $status 状态:1正常,0删除
 * @property int $user_id 上传用户ID
 * @property int $width 图片宽度
 * @property string $create_time
 * @property string $description 描述
 * @property string $extension 文件扩展名
 * @property string $filename 原始文件名
 * @property string $md5 文件MD5
 * @property string $meta_data 元数据(JSON)
 * @property string $mime_type 文件类型
 * @property string $path 存储路径
 * @property string $title 图片标题
 * @property string $update_time
 * @property string $upload_type 上传方式
 * @property-read \app\common\model\GalleryCategory $category
 * @property-read mixed $full_url
 * @property-read mixed $thumbnail_url
 */
class Gallery extends Model
{
	// 设置表名
	protected $table = 'gallery';
	
	// 自动写入时间戳
	protected $autoWriteTimestamp = true;
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';
	
	// 设置JSON字段
	protected $json = ['meta_data'];
	
	// 图片状态常量
	const STATUS_ACTIVE = 1;   // 正常
	const STATUS_DELETED = 0;  // 已删除
	
	/**
	 * 获取器：获取完整图片URL
	 */
	public function getFullUrlAttr($value, $data)
	{
		if (empty($data['path'])) {
			return '';
		}
		
		// 如果是完整的URL直接返回
		if (strpos($data['path'], 'http') === 0) {
			return $data['path'];
		}
		
		// 拼接本地路径
		$domain = config('app.static_domain') ?: request()->domain();
		return $domain . $data['path'];
	}
	
	/**
	 * 获取器：获取缩略图URL
	 */
	public function getThumbnailUrlAttr($value, $data)
	{
		if (empty($data['path'])) {
			return '';
		}
		
		$path = $data['path'];
		// 如果是外部URL，可能需要特殊处理
		if (strpos($path, 'http') === 0) {
			return $path; // 或者调用第三方缩略图服务
		}
		
		$filename = pathinfo($path, PATHINFO_FILENAME);
		$extension = pathinfo($path, PATHINFO_EXTENSION);
		$thumbPath = 'thumbnails/' . $filename . '_thumb.' . $extension;
		
		$domain = config('app.static_domain') ?: request()->domain();
		return $domain . '/storage/gallery/' . $thumbPath;
	}
	
	/**
	 * 关联上传用户
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	
	/**
	 * 关联分类
	 */
	public function category()
	{
		return $this->belongsTo(GalleryCategory::class, 'category_id');
	}
}
?>