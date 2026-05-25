<?php

namespace app\admin\common;

use think\exception\HttpResponseException;
use think\facade\Config;
use think\facade\Request;
use think\facade\Route;
use think\Response;

/**
 * Trait Base
 * @package 
 */
trait Util
{
	/**
	 * 操作错误跳转的快捷方法
	 * @param string      $msg
	 * @param string|null $url
	 * @param string      $data
	 * @param int         $wait
	 * @param array       $header
	 * @return object
	 */
	public function error (string $msg = '', ?string $url = null, int $type = 0, $data = '', int $wait = 3, array $header = []): object
	{
		if (is_null($url)) {
			$url = Request::isAjax() ? '' : 'javascript:history.back(-1);';
		} else if ($url) {
			$url = (strpos($url, '://') || str_starts_with($url, '/')) ? $url : Route::buildUrl($url);
		}
		$result = ['code' => 0, 'msg' => $msg, 'data' => $data, 'url' => $url, 'wait' => $wait, 'type' => $type ];
		$type   = $this->getResponseType();
		
		if ('html' == strtolower($type)) {
			$response = Response::create(Config::get('app.error_tmpl'), 'view')->assign($result)->header($header);
		} else {
			$response = Response::create($result, $type)->header($header);
		}
		throw new HttpResponseException($response);
	}

	/**
	 * 操作成功跳转的快捷方法
	 * @param string      $msg
	 * @param string|null $url
	 * @param string      $data
	 * @param int         $wait
	 * @param array       $header
	 * @return object
	 */
	public function success ($msg = '', ?string $url = null, $data = '', int $wait = 3, array $header = []): object
	{
		if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
			$url = $_SERVER["HTTP_REFERER"];
		} else if ($url) {
			$url = (strpos($url, '://') || str_starts_with($url, '/')) ? $url : Route::buildUrl($url);
		}
		$result = ['code' => 1, 'msg' => $msg, 'data' => $data, 'url' => $url, 'wait' => $wait,];
		$type = $this->getResponseType();
		if ('html' == strtolower($type)) {
			$response = Response::create(Config::get('app.success_tmpl'), 'view')->assign($result)->header($header);
		} else {
			$response = Response::create($result, $type)->header($header);
		}
		throw new HttpResponseException($response);
	}

	/**
	 * 渲染404页面
	 * @param bool $json
	 * @return object
	 */
	public function error_404 (bool $json = false): object
	{
		$result = Config::get('app.error_404_tmpl');
		if ($json || Request::isJson() || Request::isAjax()) {
			$result = $this->json(99999);
		}
		$response = Response::create($result, 'view')->code(404)->header([]);
		throw new HttpResponseException($response);
	}

	/**
	 * 格式化返回Json数据
	 * @param string|null $msg
	 * @param int         $code
	 * @param array       $data
	 * @param array       $extend
	 * @param int         $httpCode
	 * @return object
	 */
	public function json (?string $msg = null,int $code = 200,int $type = 0,array $data = [],$extend = [],int $httpCode = 200): object
	{
		$result = [
			'msg' => $msg,
			'code'  => $code,
			'type'  => $type,
			'time' => time()
		];
		if (!empty($data)) {
			$result['data'] = $data;
		}
		if (!empty($extend)) {
			foreach ($extend as $k => $v) {
				$result[$k] = $v;
			}
		}
		$response = Response::create($result, 'json', $httpCode);
		throw new HttpResponseException($response);
	}

	/**
	 * 获取当前的Response 输出类型
	 * @return string
	 */
	protected function getResponseType (): string
	{
		return Request::isJson() || Request::isAjax() ? 'json' : 'html';
	}
}