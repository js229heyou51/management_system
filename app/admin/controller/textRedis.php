<?php  
namespace app\admin\controller;

use think\facade\Cache;
use app\common\model\ArticleCo;
use app\common\utils\RateLimiter;
/**
 * 
 */
class textRedis
{
	function romanToInt($s) {
		$arr = [
			'I' => 1,
			'V' => 5,
			'X' => 10,
			'L' => 50,
			'C' => 100,
			'D' => 500,
			'M' => 1000,
		];
		$brr = array_keys($arr);
		$len = strlen($s);
		$sum = 0;

		for ($i=0; $i < $len; $i++) {
			$prevStr = $s[$i];
			$prevKey = 0;
			$nextStr = $s[$i+1] ?? '';
			$nextKey = 0;
			if($nextStr == ''){
				$sum += $arr[$s[$i]];
				return $sum;
			}
			foreach($brr as $k => $v){
				if($prevStr == $v){
					$prevKey = $k;
				}
				if($nextStr == $v){
					$nextKey = $k;
				}
			}
			if($nextKey - $prevKey > 2){
				return '不存在的罗马数字';
			}

			if($nextKey > $prevKey){
				$sum += $arr[$nextStr] - $arr[$prevStr];
				$i++;
				continue;
			}else{
				$sum += $arr[$s[$i]]; 
			}
		}
		return $sum;
	}

	function intToRoman($num) {
		$arr = [
			'I' => 1,
			'V' => 5,
			'X' => 10,
			'L' => 50,
			'C' => 100,
			'D' => 500,
			'M' => 1000,
		];

		$len = strlen($num);
		$str = '';
		$prev = 0;
		$M = (int) floor($num / $arr['M']);
		if($M > 0){
			for ($j=0; $j < $M; $j++) { 
				$str .= 'M';
			}
		}

		return $str;
	}	
	function lengthOfLastWord($s){
		$s_arr = explode(' ',trim($s));
		return strlen(end($s_arr));
	}

	function longestCommonPrefix($strs){
		$res = '';
		foreach ($strs as $key => $value) {
			for ($i=0; $i < strlen($value); $i++) { 
				$temp = $value[$i];
			}
		}

		return $res;
	}

	function isPalindrome($s){
		$result = preg_replace('/[^a-zA-Z0-9]/','',$s);
		$result = strtolower($result);
		dump($result);
		$len = strlen($result);
		if($len % 2 == 0){
			$halt = $len / 2;
			$haltStr = substr($result, 0, $halt);
			$otherHaltStr = substr($result, $halt);
			$otherHaltStr = strrev($otherHaltStr);
		}else{
			$halt = (int) ceil($len / 2);
			$haltStr = substr($result, 0, $halt - 1 );
			$otherHaltStr = substr($result, $halt);
			$otherHaltStr = strrev($otherHaltStr);
		}
		dump($haltStr);
		dump($otherHaltStr);

		if($haltStr === $otherHaltStr){
			return $result.' 是回文串。';
		}else{
			return $result.' 不是回文串。';
		}
	}

	function isSubsequence($s, $t) {
		$len = strlen($s);
		$arr = mb_str_split($t);
		$flag = false;
		$temp = 0;
		for ($i=0; $i < $len; $i++) { 
			foreach ($arr as $k => $v) {
				if($s[$i] === $v){

				}
			}
		}
		return true;
	}
	/**
	* 获取客户端真实 IP
	*
	*@parambool $trustProxy 是否信任反向代理写入的 XFF 头
	*             部署在 CDN/SLB 后 = true；直连 = false
	*/
	function getRealIp(bool $trustProxy = false):string
	{
	  $candidates = [];

	 if($trustProxy) {
	   // XFF 可能是 "client, proxy1, proxy2"，最左侧才是原始客户端
	   if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	     foreach(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])as$part) {
	        $part = trim($part);
	       if(filter_var($part, FILTER_VALIDATE_IP)) {
	          $candidates[] = $part;
	        }
	      }
	    }

	   if(!empty($_SERVER['HTTP_X_REAL_IP'])) {
	      $candidates[] = trim($_SERVER['HTTP_X_REAL_IP']);
	    }
	  }

	 // 永远放在最后兜底
	  $candidates[] = $_SERVER['REMOTE_ADDR'] ??'';

	 foreach($candidates as $ip) {
	   if(filter_var($ip, FILTER_VALIDATE_IP)) {
	     return$ip;
	    }
	  }

	 return'0.0.0.0';
	}
	/**
	 * 检查 IP 是否命中含通配符的规则
	 *
	 * @param string $ip       目标 IP
	 * @param array  $patterns 规则，如 ['192.168.1.*', '10.0.*.*']
	 */
	function checkIpPatterns(string $ip, array $patterns): bool
	{
	    // 只对合法 IP 做判断
	    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
	        return false;
	    }

	    foreach ($patterns as $pattern) {
	        // 1) preg_quote 转义正则元字符  2) 再把 \* 还原为 .*
	        // 顺序不能反：先 quote 再替换，才是安全的
	        $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/';

	        if (preg_match($regex, $ip)) {
	            return true;
	        }
	    }

	    return false;
	}

	

	
	function index()
	{
		$limiter = new RateLimiter(5, 900);
		$ip    = request()->ip(true);
		$email = trim($_POST['email'] ?? '');

		// 任意一个维度超限就拦下
		$limiter->assertNotLocked($ip,    'login');   // 维度一：来源 IP
		// $limiter->hit($ip, 'login');
		$limiter->assertNotLocked($email, 'login');   // 维度二：目标账号
		// echo $this->getRealIp();
		// 使用示例
		$blockedPatterns = ['192.168.1.*', '10.0.*.*', '127.0.*.*'];

		if ($this->checkIpPatterns($this->getRealIp(true), $blockedPatterns)) {
		    http_response_code(403);
		    exit('Your IP range is blocked.');
		}

		echo 'Access Granted!';
		// $s = '   fly me   to   the moon  ';
		// $res = $this->lengthOfLastWord($s);
		// dump($res);
		// 
		// 
		// $strs = ["flower","flow","flight"];
		// $res = $this->longestCommonPrefix($strs);

		// $s = "A man, a plan, a canal: Panama";
		// $s = 'race a car';
		// // $s = " ";
		// $s = '0P';
		// $res = $this->isPalindrome($s);
		// $s = 'abc';
		// $t = 'ahbgdc';
		// $s = "axc"; 
		// $t = "ahbgdc";
		// $s = "acb";
		// $t = "ahbgdc";
		// $s = "aza";
		// $t = "abzba";
		// $res = $this->isSubsequence($s, $t);
		// dump($res);
		// 创建一个新的 Fiber
		// $fiber = new \Fiber(function(): void {
		//     echo "Fiber started\n<br>";

		//     // 暂停当前 Fiber，并传递一个值
		//     $suspendedValue = \Fiber::suspend('Suspended value');

		//     echo "Fiber resumed, got value: {$suspendedValue}\n<br>";
		// });

		// // 启动 Fiber
		// echo "Before Fiber\n<br>";
		// $result = $fiber->start();   // 第一次启动，Fiber 运行到 suspend 处暂停
		// echo "Fiber suspended with value: {$result}\n<br>";

		// // 恢复 Fiber（并传入一个值给 suspend() 的返回值）
		// $fiber->resume('Hello from resume');
		// echo "After Fiber\n<br>";
		// $redis = Cache::store('redis')->handler();
		// $article = ArticleCo::limit(500)->select();
		// $redis->set('article', json_encode($article), 60);
		// $start = microtime(true);
		// $data = $redis->keys('*');
		// // $redis->flushDb();
		// // $data = json_decode($redis->get('article'),true);
		// $redisTime = microtime(true) - $start;
		// dump($redisTime);
		// dump($data);
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