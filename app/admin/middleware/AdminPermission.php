<?php  
declare (strict_types = 1);

namespace app\admin\middleware;

use think\facade\Session;
use think\facade\Cookie;
use \app\common\model\Master as MM;

class AdminPermission
{
	use \app\admin\common\Util;
	/**
	* 处理请求
	*/
	public function handle($request, \Closure $next)
	{
		$ck=false;
		$action = $request->controller().'_'.$request->action();
		$type = 1;
		if($request->action() == 'default' || $request->action() == 'setconfig' || $request->action() == 'default_log' || $request->action() == 'crud'){
			$type = 0;
		}
		$rememberToken = Cookie::get('remember_token')??Session::get('remember_token');
		$admin = MM::where('remember_token',$rememberToken)->find();
		$action_list = $admin['action_list'];
		if(!empty($action_list)){
			if($action_list=='all'){
				$ck=true;
			}elseif(strpos(','.$action_list.',',','.$action.',')!==false){
				$ck=true;
			}elseif($request->action()=='make'){
				$ck=true;
			}elseif(strpos(','.$action_list.',',','.$request->controller().'_*'.',')!==false){
				$ck=true;
			}elseif($request->action()=='setconfig'){
				$ck=true;
			}elseif($request->controller()=='SetupSy'){
				$ck=true;
			}elseif($request->action()=='crud'){
				$ck=true;
			}elseif(strpos(''.$request->action().'','pl_')!==false){
				$ck=true;
			}
		}
		if($ck == false){
			return $request->isAjax()? $this->json('权限不足',999,$type):$this->error('权限不足','',$type);
		}
		return $next($request);
	}
}

?>