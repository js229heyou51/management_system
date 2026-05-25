<?php  
namespace app\admin\controller;

use think\facade\Cache;
use app\common\model\ArticleCo;
/**
 * 
 */
class textRedis
{
	
	function index()
	{
		$redis = Cache::store('redis')->handler();
		$article = ArticleCo::limit(500)->select();
		$redis->set('article', json_encode($article), 60);
		$start = microtime(true);
		$data = $redis->keys('*');
		// $redis->flushDb();
		// $data = json_decode($redis->get('article'),true);
		$redisTime = microtime(true) - $start;
		dump($redisTime);
		dump($data);
		// $redis = Cache::store('redis')->handler();
		// try {
		// 	$article = ArticleCo::limit(500)->select();
		// 	$redis->set('article', json_encode($article), 60);
		// 	// $value = $redis->get('article');
	    //     return 'Redis 测试成功: ' ;
	    // } catch (\Exception $e) {
	    //     return 'Redis 连接失败: ' . $e->getMessage();
	    // }
	}

	function dbIndex(){
		$start = microtime(true);
		$article = ArticleCo::limit(500)->select();
		$dbTime = microtime(true) - $start;
		dump($dbTime);
		dump($article);
	}
}

?>