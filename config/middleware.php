<?php
// 中间件配置
return [
    // 别名或分组
    'alias'    => [
        'AdminCheck' => app\admin\middleware\AdminCheck::class,
        'AdminPermission'  => app\admin\middleware\AdminPermission::class,
        'UserCheck'  => app\index\middleware\UserCheck::class,
    ],
    // 优先级设置，此数组中的中间件会按照数组中的顺序优先执行
    'priority' => [],
];
