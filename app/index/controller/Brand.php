<?php  
declare (strict_types = 1);

namespace app\index\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use app\common\model\BrandCo;
use app\common\service\BrandService;

class Brand extends Base{

	public $nav = 'brand';

	public function initialize(){
		parent::initialize();
		View::assign([
			'nav' => $this->nav,
		]);
	}
	public function index(){

		$data = Request::param();
		$keyword = $data['keyword']??'';
		$where[] = ['lm','=',1];
		if($keyword){
			$where[] = ['keyword','like','%'.$keyword.'%'];
		}

		$res['letters'] = range('A', 'Z');;
		$brandService = new BrandService();
		$list = $brandService->getList(['where'=>$where],1,15);
		$res['list'] = $list;
		$page = $list->render();
		View::assign([
			'data' => $res,
			'keyword' => $keyword,
			'page' => $page,
		]);

		return View::fetch();
	}
}