<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Session;

/**
 * Class app\common\model\FeedRecord
 *
 * @property bool $ding 置顶
 * @property bool $hot 热门
 * @property bool $pass 屏蔽
 * @property bool $tuijian 推荐
 * @property int $id id
 * @property int $lm 上一级
 * @property int $px 排序
 * @property int $read_num 浏览次数
 * @property string $account 标题
 * @property string $apname 页面名称
 * @property string $article_id 关键词
 * @property string $article_str 关键词
 * @property string $delete_time 删除时间
 * @property string $f_body 简要介绍
 * @property string $feed_id 关键词
 * @property string $fil_sl 文件
 * @property string $g_body 其他介绍
 * @property string $img_sl 图片
 * @property string $ip ip
 * @property string $keyword 关键词
 * @property string $lang 语言
 * @property string $link_url 跳转链接
 * @property string $list_lm 所有父级
 * @property string $name 标题
 * @property string $num 关键词
 * @property string $pic_sl 图片2
 * @property string $t_body 其他介绍
 * @property string $title 标题
 * @property string $title_lm 标题
 * @property string $vid_sl 视频
 * @property string $web_id 关键词
 * @property string $web_str 关键词
 * @property string $web_url 关键词
 * @property string $wtime 创建时间
 * @property string $ym_des seo介绍
 * @property string $ym_key seo关键词
 * @property string $ym_tit seo标题
 * @property string $z_body 详细介绍
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class FeedRecord extends Model{
	use SoftDelete;

}
?>