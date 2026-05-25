<?php  
declare (strict_types = 1);

namespace app\index\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use app\common\model\ProLm;
use app\common\model\ProCo;
use app\common\model\ParamCo;
use app\common\model\Pl_info;
use app\common\model\SetupSy;

class Special extends Base{

	public $nav = 'special';

	public function initialize(){
		parent::initialize();
		$category = Prolm::where($this->where)->where('fid',0)->order($this->orderLm)->select();
		View::assign([
			'category' => $category,
			'nav' => $this->nav,
		]);
	}


	public function index(){
		
		$data = Request::param();
		$lm = $data['lm']??'';
		if(!$lm){
			$find = Prolm::where($this->where)->where('fid',0)->order($this->orderLm)->limit(1)->find();
		}else{
			$find = Prolm::where($this->where)->where('id_lm',$lm)->find();
		}
		$find['children'] = Prolm::where($this->where)->where('fid',$find['fid'])->order($this->orderLm)->select();

		foreach ($find['children'] as $key => $value) {
			$value['count'] = ProCo::where($this->where)->whereLike('list_lm','%,'.$value['id_lm'].',%')->count();
		}
		$find['count'] = ProCo::where($this->where)->whereLike('list_lm','%,'.$find['id_lm'].',%')->count();

		$proCate = Prolm::where($this->where)->where('id_lm',$find['fid'])->find();
		$paramJson = $find['param_json']??$proCate['param_json'];
		$paramArr = [];
		$paramLists = [];
		if($paramJson){
			$paramArr = json_decode($paramJson,true);
			foreach ($paramArr as $key => $value) {
				$value['children'] = ParamCo::where($this->where)->where('lm',$value['value'])->order($this->orderCo)->select();
				$paramLists[] = $value;
			}
		}

		$productList = [];

		$productLists = ProCo::where($this->where)->order($this->orderCo)->where('hot',1)->paginate(10);

		foreach ($productLists as $key => $value) {

			$paramArr = [];

			$param_json = $value['param_json']? json_decode($value['param_json'],true) :[];
			
			$paramArr = ParamCo::with('profile')->where([['id','in',implode(',', $param_json)]])->order($this->orderCo)->select();
			$paramList = [];
			foreach ($paramArr as $k => $v) {
				$v['param_name'] = $v['title']??'';
				$v['param_id'] = $v['id']??'';
				$v['value'] = $v['profile']['id_lm']??'';
				$v['title'] = $v['profile']['title_lm']??'';
				$paramList[] = $v;
			}


			$priceList = Pl_info::where($this->where)->where([['sy_id','=',3],['pl_id','=',$value['id']]])->field('title as num,price')->order('px desc,id asc')->select();

			$value['paramList'] = $paramList;
			$value['priceList'] = $priceList;
			$value['priceJSON'] = json_encode($priceList);
			$productList[] = $value;
		}


		$page = $productLists->render();

		$industry = [];

		View::assign([
			'find' => $find,
			'proCate' => $proCate,
			'paramLists' => $paramLists,
			'productList' => $productList,
			'page' => $page,
			'industry' => $industry,
		]);
		return View::fetch();
	}
}

?>
