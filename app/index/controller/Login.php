<?php  
declare (strict_types = 1);

namespace app\index\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Session;
use think\facade\Cookie;
use think\facade\Lang;
use app\index\validate\Login as LV;
use app\index\validate\Register as RV;
use app\common\model\Person;

class Login extends Base{

	public function initialize(){
		parent::initialize();
	}

	public function index(){
		$url = Session::get('loginUrl')??'';
		View::assign([
			'url'=>$url
		]);
		return View::fetch();
	}

	public function login(){
		$data = Request::param();
		$username = $data['username'] ? trim($data['username']) : '';
		$password = $data['password'] ? trim($data['password']) : '';
		$url = !empty($data['url'])? $data['url'] : '/';

		$validate = new LV;
		if(!$validate->scene('login')->check($data)) {
			return json(['code'=>201,'msg'=>$validate->getError()]);
		}

		$find = Person::where($this->where)->where('username',$username)->find();
		if(!$find){
			return json(['code'=>201,'msg'=>'该账号不存在，请先注册']);
		}

		if(!password_verify($password, $find['password'])){
			return json(['code'=>201,'msg'=>'密码错误，请重新输入']);
		}
		$token = bin2hex(random_bytes(40));
		$updateData = [
			'token' => $token,
			'login_num' => $find->login_num + 1,
			'lip' => $find['eip'],
			'eip' => Request::ip(),
			'ltime' => $find->etime,
			'etime' => date('Y-m-d H:i:s'),
		];

		try{
			$update = Person::where($this->where)->where('username',$username)->update($updateData);
			Session::set('token',$token);
			Session::delete('loginUrl');
			return json(['code'=>200,'msg'=>'登录成功','url'=>$url]);
		}catch(\Exception $e){
			return json(['code'=>201,'msg'=>'登录失败'.$e->getMessage()]);
		}
	}

	public function logout(){
		Session::delete('token');
		return json(['code'=>200,'msg'=>'退出成功']);
	}

	public static function isLogin(){
		$token = Cookie::get('token')??Session::get('token');
		if($token == ''){
			return false;
		}
		$user = Person::where('token',$token)->find();
		if($user){
			return true;
		}
		return false;
	}

	public function register(){

		if(Request::isPost()){
			$data = Request::param();
			$smscode = $data['smscode'] ? trim($data['smscode']) : '';
			$username = $data['username'] ? trim($data['username']) : '';
			$password = $data['password'] ? trim($data['password']) : '';
			$conformpassword = $data['conformpassword'] ? trim($data['conformpassword']) : '';
			$validate = new RV;
			if(!$validate->scene('register')->check($data)) {
				return json(['code'=>201,'msg'=>$validate->getError()]);
			}
			$emailCode = Session::get('emailCode');
			if(!password_verify($smscode, $emailCode)){
				return json(['code'=>201,'msg'=>'验证码错误，请重新填写']);
			}

			$find = Person::where($this->where)->where('username',$username)->find();

			if(!empty($find)){
				return json(['code'=>201,'msg'=>'该账号已存在，请直接登录']);
			}

			$insterData = [
				'username' => $username,
				'password' => password_hash($password, PASSWORD_DEFAULT),
				'login_num' => 0,
				'pass' => 1,
				'lang' => Lang::getLangSet(),
				'wip' => Request::ip(),
				'wtime' => date('Y-m-d H:i:s',time())
			];

			try{
				$insert = Person::insert($insterData);
				Session::delete('emailCode');
				return json(['code'=>200,'msg'=>'注册成功']);
			}catch(\Exception $e){
				return json(['code'=>201,'msg'=>'注册失败'.$e->getMessage()]);
			}

		}


		return View::fetch();
	}

	public function sendSms(){
		$data = Request::param();

		$username = $data['username']??'';
		if(!$username){
			return json(['code'=>201,'msg'=>'邮箱不能为空']);
		}
		$emailConfig['username'] = 'js229@heyou51.com';
		$emailConfig['password'] = 'fuzhiqi950109@';
		$emailConfig['outbox']   = 'js229@heyou51.com';
		$emailConfig['inbox']    = $username;
		$emailCode = ''.rand(123456,999999);

		Session::set('emailCode',password_hash($emailCode, PASSWORD_DEFAULT));

		$title   = '【验证码】'; 
		$content = '【验证码】您的验证码是'.$emailCode.'，15分钟内有效。如非本人操作，请忽略此信息。';
		
		$email = new EmailConfig('25','smtp.qiye.163.com',$emailConfig,$title,$content);
		$resEmail = $email->sendEmail();
		if(!$resEmail){
			return json(['code'=>201,'msg'=>'发送失败']);
		}
		return json(['code'=>200,'msg'=>'提交成功']);

	}
}