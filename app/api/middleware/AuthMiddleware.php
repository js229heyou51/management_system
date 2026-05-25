<?php
declare (strict_types=1);

namespace app\api\middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Request;

class AuthMiddleware
{
    public function handle($request, \Closure $next)
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return json(['code' => 401, 'msg' => '未提供Token或格式错误'])->code(401);
        }
        
        $token = $matches[1];
        
        // 检查黑名单
        if (Cache::get('blacklist_' . md5($token))) {
            return json(['code' => 401, 'msg' => 'Token已失效，请重新登录'])->code(401);
        }
        
        try {
            $jwtConfig = Config::get('jwt');
            $decoded = JWT::decode($token, new Key($jwtConfig['secret'], $jwtConfig['algo']));
            // 将解析后的用户信息挂载到请求中，供后续控制器使用
            $request->uid = $decoded->uid;
            $request->userData = $decoded;
        } catch (\Firebase\JWT\ExpiredException $e) {
            return json(['code' => 401, 'msg' => 'Token已过期'])->code(401);
        } catch (\Exception $e) {
            return json(['code' => 401, 'msg' => '无效Token'])->code(401);
        }
        
        return $next($request);
    }
}