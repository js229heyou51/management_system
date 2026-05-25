<?php  
declare (strict_types = 1);

namespace app\admin\controller;

use think\facade\View;
use think\facade\Request;
use think\facade\Db;
use think\facade\Cookie;
use think\facade\Session;
use app\common\model\Master as M;
use app\admin\validate\Login as V;

class Login extends Base{
	public function index(){
		if($this->isLogin()){
			return redirect(Request::root().'/index');
		}
		$username = Cookie::get('username');
		$password = base64_decode(!empty(Cookie::get('password')) ? Cookie::get('password') : '' );
		$remember = Cookie::get('remember');
		if(isset($username) && isset($password)){
			View::assign([
				'username' => $username,
				'password' => $password,
				'remember' => $remember
			]);
		}
		return View::fetch();
	}

	/*
	登录
	*/
	public function login(){
		if(Request::isPost()){
			$data = Request::param();
			$validate = new V;
			if(!$validate->scene('login')->check($data)) 
			return ['msg'=>$validate->getError(),'code'=>201];
			if(!captcha_check($data['code'])){
				return json(array('code'=>201,'msg' => $this->langHtml['tip']['codeIncorrect']));
			}
			$username = $data['username'];
			$password = $data['password'];
			$where = [
				'username'=> $username,
				// 'lang'=> $this->lang,
			];

			$user = M::where($where)->find();
			if(!$user){
				Base::master_log($username.$this->langHtml['tip']['noData'],$username);
				return json(array('code'=>201,'msg' => $this->langHtml['tip']['noData']));
			}

			if($user->pass == 0){
				Base::master_log($username.$this->langHtml['tip']['notPermission'],$username);
				return json(array('code'=>201,'msg' => $this->langHtml['tip']['notPermission']));
			}
			if($user->error_num < 3 || ($user->error_time > 0 && (time() - $user->error_time) > 900)){
				if(!password_verify($password, $user->password)){
					if($user->error_num == 0 || ($user->error_time>0&&(time()-$user->error_time)>900) ){
						M::where('id',$user->id)->update(['error_num' => 1,'error_time'=>time()]);
					}else{
						M::where('id',$user->id)->update(['error_num' => $user->error_num + 1]);
					}
					Base::master_log($username.$this->langHtml['tip']['passwordError'],$username);
					return json(array('code'=>201,'msg' => $this->langHtml['tip']['passwordError']));
				}else{
					$rememberToken = bin2hex(random_bytes(40));

					$update = [
						'remember_token' => $rememberToken,
						'login_num' => $user->login_num + 1,
						'lip' => $user['eip'],
						'eip' => Request::ip(),
						'ltime' => $user->etime,
						'etime' => date('Y-m-d H:i:s'),
						'error_num'=>0,
						'error_time'=>0,
					];
					M::where('id',$user->id)->update($update);

					$remember = $data['remember']??'';
					if($remember == 'on'){
						Cookie::set('remember_token',$rememberToken,3600 * 24 * 15);
						Cookie::set('username',$username);
						Cookie::set('password',base64_encode($password));
						Cookie::set('remember',$remember);
					}else{
						Cookie::delete('remember_token');
						Cookie::delete('username');
						Cookie::delete('password');
						Cookie::delete('remember');
					}
					$admin = [
						'uid' => $user->id,
						'username' => $user->username,
						'menu_list' => $user->menu_list,
						'action_list' => $user->action_list,
					];
					Session::set('remember_token',$rememberToken);
					Base::master_log($username.$this->langHtml['tip']['loginSuccess'],$username);
					return json(array('code'=>200,'msg' => $this->langHtml['tip']['loginSuccess']));
				}
			}else{
				return json(array('code'=>201,'msg' => $this->langHtml['tip']['errorTip']));
			}
		}
	}

	public static function isLogin(){
		$rememberToken = Cookie::get('remember_token')??Session::get('remember_token');
		$user = M::where('remember_token',$rememberToken)->find();
		if($user){
			return true;
		}
		return false;
	}
	public static function getUserInfo(){
		$rememberToken = Cookie::get('remember_token')??Session::get('remember_token');
		$user = M::where('remember_token',$rememberToken)->find();
		if($user){
			return $user;
		}
	}

	public function logout(){
		Base::master_log($this->langHtml['tip']['logOut']);
		Session::delete('admin');
		Session::delete('remember_token');
		Cookie::delete('remember_token');
		return json(array('code' => 200,'msg' => $this->langHtml['tip']['logOut']));
	}
}



?>