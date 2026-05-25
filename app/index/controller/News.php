<?php  
declare (strict_types = 1);

namespace app\index\controller;

use think\facade\View;
use think\facade\Request;
use app\common\model\NewsLm;
use app\common\model\NewsCo;

class News extends Base{

	public $nav = 'news';

	public function initialize(){
		parent::initialize();
		$newsCategory = NewsLm::where($this->where)->order($this->orderLm)->select();

		View::assign([
			'nav' => $this->nav,
			'newsCategory' => $newsCategory,
		]);
	}

	public function index(){
		$param = Request::param();
		$page = $param['page'] ?? '';
		$list = NewsCo::with('profile')->where($this->where)->order($this->orderCo)->paginate(15);

		$newsList = [
			'list' => $list,
			'page' => $list->render(),
		]; 

		View::assign([
			'newsList' => $newsList['list']['data'] ?? $newsList['list'],
			'page' => $newsList['page'],
		]);

		return View::fetch();
	}

	public function category(){

		$param = Request::param();
		$lm = $param['lm'] ?? '';
		$page = $param['page'] ?? '';

		$where = [];
		if($lm){
			$where[] = ['lm','=',$lm];
		}
		$list = NewsCo::with('profile')->where($this->where)->where($where)->order($this->orderCo)->paginate(15);

		$newsList = [
			'list' => $list,
			'page' => $list->render(),
		]; 

		View::assign([
			'lm' => $lm,
			'newsList' => $newsList['list']['data'] ?? $newsList['list'],
			'page' => $newsList['page'],
		]);

		return View::fetch('index');
	}


	public function show(){
		$param = Request::param();
		$id = $param['id'] ?? '';
		if(!$id){
			return json(['code'=>201,'msg'=>'参数错误！']);
		}
		$find = NewsCo::with('profile')->where('pass','=',1)->where('id','=',$id)->find();
		View::assign([
			'find' => $find,
		]);
		return View::fetch();
	}
}

?>