<?php
use think\facade\Route;

// // 登录相关（无需认证）
Route::post('api/login', 'LoginController/login');
// Route::post('api/logout', 'LoginController/logout')->middleware('auth'); // 退出登录需要先认证

// // 需要认证的 API 组
// Route::group('api', function () {
//     Route::get('user/profile', 'UserController/profile');
//     // 其他受保护的接口...
// })->middleware('auth');

?>