<?php 
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route; 

// 图片管理API
Route::group('gallery', function () {
    Route::post('upload', 'Gallery/upload');      // 上传
    Route::get('list', 'Gallery/list');          // 列表
    Route::get('detail/:id', 'Gallery/detail');  // 详情
    Route::delete('delete/:id', 'Gallery/delete'); // 删除
})->middleware(['AdminCheck']); // 添加认证中间件

// 图片管理API
Route::group('galleryCategory', function () {
    Route::post('create', 'GalleryCategory/create');      // 上传
    Route::delete('delete/:id', 'GalleryCategory/delete');      // 上传
})->middleware(['AdminCheck']); // 添加认证中间件

// 图片访问路由
Route::get('storage/gallery/:path', 'Gallery/image');

// 图片管理API
Route::group('video', function () {
    Route::post('upload', 'Video/upload');      // 上传
    Route::get('list', 'Video/list');          // 列表
    Route::get('detail/:id', 'Video/detail');  // 详情
    Route::delete('delete/:id', 'Video/delete'); // 删除
})->middleware(['AdminCheck']); // 添加认证中间件

// 图片管理API
Route::group('videoCategory', function () {
    Route::post('create', 'VideoCategory/create');      // 上传
    Route::delete('delete/:id', 'VideoCategory/delete');      // 上传
})->middleware(['AdminCheck']); // 添加认证中间件

// 图片访问路由
Route::get('storage/video/:path', 'Video/image');

?>