<?php  
declare (strict_types = 1);

namespace app\index\middleware;

use think\facade\Session;
use app\index\controller\Login;


class UserCheck
{
	/**
	* 处理请求
	*/
	public function handle($request, \Closure $next)
	{
		if(Login::isLogin() == false){
			Session::set('loginUrl',$request->url());
			if($request->isAjax()){
				return json(['code'=>201,'msg'=>'请登录']);
			}else{
				return redirect($request->root().'/login');
			}
		}
		return $next($request);
	}
}

?>