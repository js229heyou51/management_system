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

Route::post('api/login', 'LoginController/login');

use app\index\controller\Bom;
use app\index\controller\Product;
use app\index\controller\About;
use app\index\controller\Brand;
use app\index\controller\Cart;
use app\index\controller\Contact;
use app\index\controller\Login;
use app\index\controller\News;
use app\index\controller\Person;
use app\index\controller\Special;

Route::group('product',function(){
	Route::get('category/:lm', [Product::class, 'category']);
	Route::get('list/:lm', [Product::class, 'list']);
	Route::get('show/:id', [Product::class, 'show']);
});
Route::group('about',function(){
	Route::get('/', 'about/index');
	Route::get('/:id', 'about/index');
});
Route::group('contact',function(){
	Route::get('/', 'contact/index');
	Route::get('/:id', 'contact/index');
});
Route::group('brand',function(){
	Route::get('/', 'brand/index');
	Route::get('/:keyword', 'brand/index');
});
Route::get('login', 'login/index');
Route::get('register', 'login/register');

Route::group('article',function(){
	Route::get('/', 'article/index');
	Route::get('category/:lm', 'article/category');
	Route::get('show/:id', 'article/show');
});

Route::group('news',function(){
	Route::get('/', 'news/index');
	Route::get('category/:lm', 'news/category');
	Route::get('show/:id', 'news/show');
});

Route::group('cart',function(){
	Route::get('payOrder/:orderId', 'cart/payOrder');
});

// 
Route::get('bom', [Bom::class, 'index']);
Route::get('about', [About::class, 'index']);
Route::get('brand', [Brand::class, 'index']);
Route::get('product', [Product::class, 'index']);