<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;

/**
 * Class app\common\model\ProCo
 *
 * @property array $img_sl 图片
 * @property array $pic_sl 图片2
 * @property array $vid_sl 视频
 * @property bool $ding 置顶
 * @property bool $hot 热门
 * @property bool $pass 屏蔽
 * @property bool $tuijian 推荐
 * @property float $price 价格
 * @property int $id id
 * @property int $lm 上一级
 * @property int $px 排序
 * @property int $read_num 浏览次数
 * @property string $apname 页面名称
 * @property string $delete_time 删除时间
 * @property string $f_body 简要介绍
 * @property string $fil_sl 文件
 * @property string $g_body 其他介绍
 * @property string $ip ip
 * @property string $keyword 关键词
 * @property string $lang 语言
 * @property string $link_url 跳转链接
 * @property string $list_lm 所有父级
 * @property string $package 包装
 * @property string $param_json 关键词
 * @property string $param_one 关键词
 * @property string $stock 库存
 * @property string $t_body 其他介绍
 * @property string $title 标题
 * @property string $wtime 创建时间
 * @property string $ym_des seo介绍
 * @property string $ym_key seo关键词
 * @property string $ym_tit seo标题
 * @property string $z_body 详细介绍
 * @property-read \app\common\model\Pl_info[] $price_lists
 * @property-read \app\common\model\ProLm $profile
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class ProCo extends Model{
	use SoftDelete;

	// 定义一对一关联  
	public function profile(){
		return $this->hasOne(ProLm::class, 'id_lm', 'lm');  
	}

	public function priceLists(){
		return $this->hasMany(Pl_info::class,'pl_id','id')->where('sy_id',3);
	}

	protected $type = [
		'img_sl' => 'json',
		'pic_sl' => 'json',
		'vid_sl' => 'json',
	];
}
?>

