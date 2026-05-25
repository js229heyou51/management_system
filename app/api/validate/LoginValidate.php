<?php
declare (strict_types=1);

namespace app\api\validate;

use think\Validate;

class LoginValidate extends Validate
{
    protected $rule = [
        'username' => 'require|length:4,30',
        'password' => 'require|length:6,30',
    ];
    
    protected $message = [
        'username.require' => '用户名/邮箱不能为空',
        'username.length'  => '用户名/邮箱长度为4~30个字符',
        'password.require' => '密码不能为空',
        'password.length'  => '密码长度为6~30个字符',
    ];
}