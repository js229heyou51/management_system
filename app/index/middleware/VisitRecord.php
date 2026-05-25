<?php  
declare (strict_types = 1);

namespace app\index\middleware;

use think\facade\Cache;
use app\common\model\VisitLog;

class VisitRecord
{
	/**
	* 处理请求
	*/
	public function handle($request, \Closure $next){
		$response = $next($request);
		
		// 记录访问
		$this->recordVisit($request);
		
		return $response;
	}


	protected function recordVisit($request){
		try {
			$ip = $request->ip();
			$url = $request->url();
			$userAgent = $request->header('user-agent');
			$referer = $request->header('referer', '');
			// 使用缓存防止短时间重复记录
			$cacheKey = 'visit_record_' . md5($ip);
			if (!Cache::has($cacheKey)) {
				// 记录到数据库
				VisitLog::create([
					'ip' => $ip,
					'url' => $url,
					'user_agent' => $userAgent,
					'referer' => $referer,
					'visit_time' => time()
				]);
				// 设置缓存，1小时内同一IP访问同一URL不重复记录
				Cache::set($cacheKey, 1, 3600);
				// 更新总访问量
				$this->updateTotalVisits();
			}
		}catch (\Exception $e) {
			// 记录错误日志，但不影响正常访问
			\think\facade\Log::error('访问记录失败: ' . $e->getMessage());
		}
	}

	protected function updateTotalVisits(){
		$totalKey = 'total_visits';
		$todayKey = 'visits_' . date('Ymd');
		// 更新总访问量
		Cache::inc($totalKey);
		Cache::inc($todayKey);
	}
}

?>