<?php
declare (strict_types=1);

namespace app\common\model;

use think\Model;
use think\facade\Db;

class User extends Model
{
    protected $table = 'users';
    protected $pk = 'id';
    
    // 隐藏敏感字段
    protected $hidden = ['password', 'deleted_at'];
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    // 密码设置器（注册时使用，登录不需要）
    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }
    
    // 验证用户凭据
    public static function verifyCredentials($username, $password)
    {
        // 支持用户名或邮箱登录
        $user = self::where('username', $username)
            ->whereOr('email', $username)
            ->find();
        
        if (!$user) {
            return false;
        }
        
        // 验证密码
        if (password_verify($password, $user->password)) {
            return $user;
        }
        
        return false;
    }
}