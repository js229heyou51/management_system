<?php  
declare(strict_types=1);

namespace app\api\middleware;

class Cors
{
	public function handle($request, \Closure $next)
	{
		$response = $next($request);
		// 设置跨域头
		$response->header([

			'Access-Control-Allow-Origin'   => $request->header('origin', '*'), // 允许所有域名（生产环境建议指定具体域名）
			'Access-Control-Allow-Methods'  => 'GET,POST,PUT,DELETE,OPTIONS', // 允许的请求方法
			'Access-Control-Allow-Headers'  => 'Content-Type,Authorization,X-Requested-With', // 允许的请求头
			'Access-Control-Allow-Credentials' => 'true' // 是否允许发送 Cookie
		]);
		
		// 处理 OPTIONS 预检请求
		if ($request->method(true) === 'OPTIONS') {
			$response->code(204);
		}

		return $response;
	}
}

?>