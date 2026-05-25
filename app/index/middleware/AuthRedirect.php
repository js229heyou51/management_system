<?php
namespace app\index\middleware;

class AuthRedirect
{
	public function handle($request, \Closure $next)
	{
		// 排除登录页面本身
		if (!$request->isLogin()) {
			// 记录来源页面URL
			session('redirect_url', $request->url());
		}
		
		return $next($request);
	}
}