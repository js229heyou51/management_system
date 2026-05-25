<?php  
declare (strict_types = 1);

namespace app\index\controller;

use think\facade\View;
use think\facade\Request;
use app\common\model\ArticleLm;
use app\common\model\ArticleCo;

class Article extends Base{

	public $nav = 'article';

	public function __construct(){
		parent::__construct();

		
		$articleCategory = ArticleLm::where($this->where)->order($this->orderLm)->select();

		View::assign([
			'nav' => $this->nav,
			'articleCategory' => $articleCategory,
		]);
	}

	public function index(){

		$articleList = ArticleCo::with('profile')->where($this->where)->order($this->orderCo)->paginate(15);
		View::assign([
			'articleList' => $articleList,
			'page' => $articleList->render(),
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
		$list = ArticleCo::with('profile')->where($this->where)->where($where)->order($this->orderCo)->paginate(15);

		$articleList = [
			'list' => $list,
			'page' => $list->render(),
		]; 


		View::assign([
			'lm' => $lm,
			'articleList' => $articleList['list']['data'] ?? $articleList['list'],
			'page' => $articleList['page'],
		]);

		return View::fetch('index');
	}

	public function show(){
		$param = Request::param();
		$id = $param['id'] ?? '';
		if(!$id){
			return json(['code'=>201,'msg'=>'参数错误！']);
		}
		$find = ArticleCo::with('profile')->where('pass','=',1)->where('id','=',$id)->find();
		View::assign([
			'find' => $find,
		]);
		return View::fetch();
	}
}

?>