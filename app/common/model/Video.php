<?php  
// app/common/model/Video.php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\Video
 *
 * @property array $meta_data 额外元数据（如编码信息）
 * @property array $tags 标签数组
 * @property int $category_id 分类ID
 * @property int $duration 时长（秒）
 * @property int $height 视频高度
 * @property int $id
 * @property int $is_recommend 是否推荐
 * @property int $like_count 点赞数
 * @property int $size 文件大小（字节）
 * @property int $sort_order 排序
 * @property int $status 状态 1-正常 0-删除
 * @property int $user_id 上传用户ID
 * @property int $view_count 播放次数
 * @property int $width 视频宽度
 * @property string $cover_image 封面图URL
 * @property string $create_time
 * @property string $delete_time 软删除时间
 * @property string $description 视频描述
 * @property string $format 视频格式（mp4, mov等）
 * @property string $title 视频标题
 * @property string $update_time
 * @property string $video_path 视频本地路径
 * @property string $video_url 视频URL
 * @property-read \app\common\model\VideoCategory $category
 * @property-read mixed $duration_format
 * @property-read mixed $size_format
 * @property-read mixed $url
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class Video extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	
	protected $table = 'video';
	protected $pk = 'id';
	
	// 自动时间戳
	protected $autoWriteTimestamp = true;
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';

    // 状态常量
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 0;
	
	// 类型转换
	protected $type = [
		'status'     => 'integer',
		'category_id'=> 'integer',
		'user_id'    => 'integer',
		'duration'   => 'integer',
		'size'       => 'integer',
		'width'      => 'integer',
		'height'     => 'integer',
		'view_count' => 'integer',
		'like_count' => 'integer',
		'sort_order' => 'integer',
		'is_recommend'=> 'integer',
		'tags'       => 'json',
		'meta_data'  => 'json',
	];
	
	// 追加字段
	protected $append = ['duration_format', 'size_format', 'url'];
	
	/**
	 * 关联分类
	 */
	public function category()
	{
		return $this->belongsTo(VideoCategory::class, 'category_id', 'id');
	}
	
	/**
	 * 关联上传用户
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
	
	/**
	 * 关联产品（多对多）
	 */
	public function products()
	{
		return $this->belongsToMany(Product::class, 'product_video', 'video_id', 'product_id')
					->withPivot(['type', 'sort_order']);
	}
	
	/**
	 * 获取格式化时长
	 */
	public function getDurationFormatAttr($value, $data)
	{
		$seconds = $data['duration'] ?? 0;
		if ($seconds < 60) {
			return $seconds . '秒';
		}
		$minutes = floor($seconds / 60);
		$secs = $seconds % 60;
		return $minutes . '分' . ($secs ? $secs . '秒' : '');
	}
	
	/**
	 * 获取格式化大小
	 */
	public function getSizeFormatAttr($value, $data)
	{
		$size = $data['size'] ?? 0;
		if ($size < 1024) {
			return $size . 'B';
		} elseif ($size < 1024 * 1024) {
			return round($size / 1024, 2) . 'KB';
		} elseif ($size < 1024 * 1024 * 1024) {
			return round($size / (1024 * 1024), 2) . 'MB';
		} else {
			return round($size / (1024 * 1024 * 1024), 2) . 'GB';
		}
	}
	
	/**
	 * 获取完整URL（如果有配置CDN）
	 */
	public function getUrlAttr($value, $data)
	{
		if (!empty($data['video_url'])) {
			if (strpos($data['video_url'], 'http') === 0) {
				return $data['video_url'];
			}
			return config('app.cdn_domain', '') . $data['video_url'];
		}
		return '';
	}
	
	/**
	 * 播放数递增
	 */
	public function incrementViewCount()
	{
		$this->view_count++;
		$this->save();
	}
}
?>