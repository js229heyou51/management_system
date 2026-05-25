<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * Class app\common\model\VisitLog
 *
 * @property int $id
 * @property string $ip IP地址
 * @property string $referer 来源页面
 * @property string $session_id Session ID
 * @property string $url 访问URL
 * @property string $user_agent 用户代理
 * @property string $visit_time 访问时间
 */
class VisitLog extends Model{
	protected $autoWriteTimestamp = false;

	protected $type = [
		'visit_time' => 'datetime'
	];

	// 记录访问
	public static function record($data = []){
		$default = [
			'ip' => request()->ip(),
			'url' => request()->url(),
			'user_agent' => request()->header('user-agent', ''),
			'referer' => request()->header('referer', ''),
			'session_id' => session_id(),
			'visit_time' => date('Y-m-d H:i:s')
		];
		$data = array_merge($default, $data);
		return self::create($data);
	}

	// 获取总访问次数
	public static function getTotalVisits(){
		return self::count();
	}

	// 获取今日访问次数
	public static function getTodayVisits(){
		$todayStart = strtotime(date('Y-m-d'));
		return self::where('visit_time', '>=', $todayStart)->count();
	}

	// 获取独立IP访问数
	public static function getUniqueIpCount()
	{
		return self::group('ip')->count();
	}
}

?>