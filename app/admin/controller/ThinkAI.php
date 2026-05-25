<?php  
declare (strict_types = 1);

namespace app\admin\controller;

use think\facade\View;
use think\ai\Client;
use Parsedown;

class ThinkAi extends Base{



	public function initialize(){
		parent::initialize();
	}

	public function index(){
		View::assign([
			'role' => $this->admin->username,
		]);
		return View::fetch();
	}

	public function aiApi(){

		$data = request()->param();

		$client = new Client(env('THINKAI_KEY'));

		$role = $this->admin->username ?? 'user';

		$content = $data['content'] ?? '';
		if(empty($content)){
			return json(['code' => 201,'msg'=> '参数错误']);
		}

		// $content = '以网易外贸通的“开发信总进垃圾箱？网易29年邮箱技术，让送达率高达95%”为题，生成一篇800字左右的文字';

		$result = $client->chat()->completions([
			'model' => env('THINKAI_MODEL'),
			'messages' => [
				['role' => 'user', 'content' => $content],
			],
			'stream'=>false,
		]);

		if($result['finish_reason'] == 'length'){
			return json(['code' => 201,'msg'=> '到达 tokens 长度上限']);
		}

		$Parsedown = new Parsedown();
		$Parsedown->setSafeMode(true); // 这一行是关键
		$safeHtml = $Parsedown->text($result['message']['content']); // $safeHtml 是安全的 HTML
		if($result['finish_reason'] == 'stop'){
			return json(['code' => 200, 'reply' => $safeHtml]);
		}


	}
}

?>