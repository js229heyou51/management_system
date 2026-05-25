<?php
declare (strict_types = 1);

namespace app\api\controller;

use think\Request;
use think\facade\Lang;
use think\facade\Cookie;
use think\facade\Session;
use app\admin\validate\Login as V;
use app\common\service\MasterService;

class Login extends \app\BaseController{

	public function index(){
		$param = request()->param();
		$validate = new V;
		$validate->remove('code', 'require');
		if(!$validate->scene('login')->check($param)) 
		return ['msg'=>$validate->getError(),'code'=>201];

		$masterService = new MasterService();
		$user = $masterService->getByWhere(['username'=>$param['username']]);
		if(!$user){
			return json(['code'=>201,'msg' => lang('tip')['noData']]);
		}
		if($user->pass == 0){
			return json(['code'=>201,'msg' => lang('tip')['notPermission']]);
		}
		if($user->error_num < 3 || ($user->error_time > 0 && (time() - $user->error_time) > 900)){
			if(!password_verify($param['password'], $user->password)){
				if($user->error_num == 0 || ($user->error_time>0&&(time()-$user->error_time)>900) ){
					$masterService->updateByWhere([['id','=',$user->id]],['error_num' => 1,'error_time'=>time()]);
				}else{
					$masterService->updateByWhere([['id','=',$user->id]],['error_num' => $user->error_num + 1]);
				}
				return json(['code'=>201,'msg' => lang('tip')['passwordError']]);
			}else{
					$rememberToken = bin2hex(random_bytes(40));

					$update = [
						'remember_token' => $rememberToken,
						'login_num' => $user->login_num + 1,
						'lip' => $user['eip'],
						'eip' => request()->ip(),
						'ltime' => $user->etime,
						'etime' => date('Y-m-d H:i:s'),
						'error_num' => 0,
						'error_time' => 0,
					];
					$masterService->updateByWhere([['id','=',$user->id]],$update);

					$remember = $data['checked']??'';
					if($remember == 'on'){
						Cookie::set('remember_token',$rememberToken,3600 * 24 * 15);
						Cookie::set('username',$user->username);
						Cookie::set('password',base64_encode($user->password));
						Cookie::set('remember',$remember);
					}else{
						Cookie::delete('remember_token');
						Cookie::delete('username');
						Cookie::delete('password');
						Cookie::delete('remember');
					}
					$admin = [
						'id' => $user->id,
						'username' => $user->username,
						'rename' => $user->rename,
						'menu_list' => $user->menu_list,
						'action_list' => $user->action_list,
					];
					Session::set('remember_token',$rememberToken);
					return json(['code'=>200,'msg' => lang('tip')['loginSuccess'],'userInfo' => $admin]);
				}
		}else{
			return json(['code'=>201,'msg' => lang('tip')['errorTip']]);
		}

		return json([
			'code' => 200,
			'msg' => '登录成功',
		]);
	}
}