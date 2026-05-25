<?php  
namespace app\index\controller;

use think\facade\View;
use app\common\service\HomeService;
use app\common\service\ProService;
use app\common\service\ProCategoryService;
use app\common\service\BrandService;
use app\common\service\NewsService;
use think\ai\Client;

class Index extends Base{

	public $nav = 'index';
	public function initialize(){
		parent::initialize();
		View::assign([
			'nav'=>$this->nav
		]);
	}

	public function index(){
		$homeService = new HomeService();
		$proService = new ProService();
		$proCategoryService = new ProCategoryService();
		$brandService = new BrandService();
		$newsService = new NewsService();
		$homeWhere = ['lm' => 1];
		$data['banner'] = $homeService->getListAll(['where' => $homeWhere,'order' => $this->orderCo]);

		$hotProductWhere = ['hot' => 1];
		$data['hotProduct'] = $proService->getListAll(['where' => $hotProductWhere,'order' => $this->orderCo]);

		$brandWhere = ['lm' => 1];
		$data['brand'] = $brandService->getListAll(['where' => $brandWhere,'order' => $this->orderCo]);

		$productWhere = ['tuijian' => 1];
		$data['product'] = $proService->getListAll(['where' => $productWhere,'order' => $this->orderCo,'limit' => 10]);

		$productCategoryWhere = ['tuijian' => 1];
		$data['productCategory'] = $proCategoryService->getCategoryList(['where' => $productCategoryWhere,'order' => $this->orderLm,'limit' => 9]);

		$data['news'] = $newsService->getListAll(['order' => $this->orderCo, 'limit' => 10]);

		View::assign([
			'data' => $data
		]);

		return View::fetch();
	}

	//测试页面
	public function phone(){
		return View::fetch();
	}

	//测试页面
	public function range(){
		return View::fetch();
	}

	//测试页面
	public function range2(){
		return View::fetch();
	}

	//测试页面
	public function range3(){
		return View::fetch();
	}

	public function testai(){
		$client = new Client(env('THINKAI_KEY'));

		// 非流式输出
		$content = '以网易外贸通的“开发信总进垃圾箱？网易29年邮箱技术，让送达率高达95%”为题，生成一篇800字左右的文字';
		$content = 'DeepSeek的v3和r1哪个版本好';
		// $content = '你好';
		$result = $client->chat()->completions([
			'model' => env('THINKAI_MODEL'),
			'messages' => [
				['role' => 'user', 'content' => $content],
			],
			'stream'=>false,
			// 'stream'=>true,
		]);
		dump($result);
		// foreach($result as $chunk){
		// 	dump($chunk);
		// }
	}
}


?>