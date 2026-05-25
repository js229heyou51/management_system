<?php  
declare (strict_types = 1);

namespace app\admin\middleware;

use app\admin\controller\Login as C;

class AdminCheck
{
	/**
	* 处理请求
	*/
	public function handle($request, \Closure $next)
	{
		if(C::isLogin() == false){
			return redirect($request->root().'/login/index');
		}
		$userInfo = C::getUserInfo();
		$request->withMiddleware([
            'user_id' => $userInfo['id'],
            'user_info' => $userInfo
        ]);
		return $next($request);
	}
}

?>