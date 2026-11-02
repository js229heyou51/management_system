<?php
declare(strict_types=1);
namespace app\common\model;

use think\Model;

class LoginAttempt extends Model
{
    protected $name = 'login_attempts';
    protected $autoWriteTimestamp = false;   // 时间戳自己管
    protected $updateTime = false;
}