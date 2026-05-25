<?php
declare (strict_types=1);

namespace app\api\controller;

use app\BaseController;
use app\common\model\User;
use app\api\validate\LoginValidate;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Config;
use think\Request;

class LoginController extends BaseController
{
	public function index(){
		dump(11);die;
		return json(['code'=>201,'msg' => lang('tip')['notPermission']]);
	}
	// 登录接口
	public function login(Request $request)
	{
		// 1. 限流（防暴力破解）—— 同一IP 每分钟最多5次
		$ip = $request->ip();
		dump($ip);die;
		$limiterKey = 'login_attempts_' . $ip;
		$attempts = Cache::get($limiterKey, 0);
		if ($attempts >= 5) {
			return json(['code' => 429, 'msg' => '请求过于频繁，请稍后再试'])->code(429);
		}
		
		// 2. 接收参数
		$data = $request->only(['username', 'password']);
		
		// 3. 参数验证
		try {
			validate(LoginValidate::class)->check($data);
		} catch (ValidateException $e) {
			// 验证失败，记录一次失败尝试（可选）
			Cache::inc($limiterKey);
			Cache::expire($limiterKey, 60);
			return json(['code' => 400, 'msg' => $e->getError()])->code(400);
		}
		
		// 4. 验证用户凭据
		$user = User::verifyCredentials($data['username'], $data['password']);
		if (!$user) {
			// 登录失败，增加失败计数
			Cache::inc($limiterKey);
			Cache::expire($limiterKey, 60);
			return json(['code' => 401, 'msg' => '用户名或密码错误'])->code(401);
		}
		
		// 5. 登录成功，清除限流缓存
		Cache::delete($limiterKey);
		
		// 6. 生成 JWT Token
		$jwtConfig = Config::get('jwt');
		$payload = [
			'iss' => 'your-app-name',          // 签发者
			'aud' => 'your-app-client',        // 接收者
			'iat' => time(),                   // 签发时间
			'exp' => time() + $jwtConfig['expire'], // 过期时间
			'uid' => $user->id,                // 用户ID
			'username' => $user->username,     // 非敏感信息
		];
		$token = JWT::encode($payload, $jwtConfig['secret'], $jwtConfig['algo']);
		
		// 7. 可选：将 token 存入缓存（黑名单管理）
		// 这里不强制，但为了实现单点登录或踢人功能可以存储
		
		// 8. 返回 Token 及用户基本信息（隐藏密码字段）
		$userData = $user->hidden(['password'])->toArray();
		return json([
			'code' => 200,
			'msg'  => '登录成功',
			'data' => [
				'token' => $token,
				'expire_in' => $jwtConfig['expire'],
				'user'  => $userData,
			]
		]);
	}
	
	// 退出登录（将 token 加入黑名单）
	public function logout(Request $request)
	{
		$token = $request->header('Authorization');
		$token = str_replace('Bearer ', '', $token);
		
		// 解析 token 获取过期时间
		try {
			$jwtConfig = Config::get('jwt');
			$decoded = JWT::decode($token, new Key($jwtConfig['secret'], $jwtConfig['algo']));
			$exp = $decoded->exp;
			$now = time();
			$ttl = $exp - $now;  // 剩余有效时间
			if ($ttl > 0) {
				// 将 token 加入黑名单缓存，过期时间与 token 剩余时间一致
				Cache::set('blacklist_' . md5($token), true, $ttl);
			}
		} catch (\Exception $e) {
			// token 无效，无需处理
		}
		
		return json(['code' => 200, 'msg' => '退出成功']);
	}
}