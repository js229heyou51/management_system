<?php
return [
	// 商户配置
	'merchant' => [
		'merchant_id'     => env('NETPAY_MERCHANT_ID', ''),
		'terminal_id'     => env('NETPAY_TERMINAL_ID', ''),
		'access_code'     => env('NETPAY_ACCESS_CODE', ''),
		'secret_key'      => env('NETPAY_SECRET_KEY', ''),
	],
	
	// 接口地址
	'api_url' => [
		'test'    => 'https://test.netpay.com/api/payment',
		'prod'    => 'https://api.netpay.com/api/payment',
		'query'   => 'https://api.netpay.com/api/query',
		'refund'  => 'https://api.netpay.com/api/refund',
	],
	
	// 其他配置
	'notify_url'   => env('NETPAY_NOTIFY_URL', '/payment/notify'),
	'return_url'   => env('NETPAY_RETURN_URL', '/payment/return'),
	'currency'     => 'CNY',
	'version'      => '1.0',
	'charset'      => 'UTF-8',
	'sign_type'    => 'MD5', // 或 RSA
	'environment'  => env('APP_ENV', 'production') === 'production' ? 'prod' : 'test',
];