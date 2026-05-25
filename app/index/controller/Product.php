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
use app\common\service\ProService as IS;
use app\common\service\ProCategoryService as CS;
use app\common\service\ParamService;

class Product extends Base{

	public $nav = 'product';
	protected $service = [];
	protected $categoryService = [];
	protected $paramService;

	public function initialize(){
		parent::initialize();
		$this->service = new IS();
		$this->categoryService = new CS();
		$this->paramService = new ParamService();
		$params['where'] = [['fid','=','0']];
		$category = $this->categoryService->getCategoryList($params);
		View::assign([
			'category' => $category,
			'nav' => $this->nav,
		]);
	}

	public function index(){
		return View::fetch();
	}

	public function category(){
		$data = request()->param();
		$lm = $data['lm']??'';
		$params['with'] = ['children'=> function($query){
			$query->withCount(['profile' => function($query,&$alias){
				$query->where($this->where);
				$alias = 'count';
			}])->where($this->where)->order($this->orderLm);
		}];
		if(!$lm){
			$find = $this->categoryService->getCategoryFind($params);
		}else{
			$params['where'] = [['id_lm' ,'=', $lm]];
			$find = $this->categoryService->getCategoryFind($params);
		}

		$where = [['list_lm' ,'like', '%,'.$find['id_lm'].',%']];

		$find['count'] = $this->service->count($where);

		View::assign([
			'find' => $find,
		]);
		return View::fetch();
	}
	public function list(){
		$data = request()->param();
		$lm = $data['lm']??'';
		if(!$lm){
			$params['where'] = [['fid' ,'=', 0]];
			$find = $this->categoryService->getCategoryFind($params);
		}else{
			$params['withCount'] = ['profile' => function($query,&$alias){
				$query->where($this->where);
				$alias = 'count';
			}];
			$params['where'] = [['id_lm' ,'=', $lm]];
			$find = $this->categoryService->getCategoryFind($params);
		}
		$where['where'] = [['fid','=',$find['fid']]];
		$find['children'] = $this->categoryService->getCategoryList($where);
		foreach ($find['children'] as $key => $value) {
			$where = [['list_lm' ,'like', '%,'.$value['id_lm'].',%']];
			$value['count'] = $this->service->count($where);
		}
		$categoryParams['where'] = [['id_lm' ,'=', $find['fid']]];
		$proCate = $this->categoryService->getCategoryFind($categoryParams);

		$paramJson = $find['param_json']??$proCate['param_json'];
		$paramArr = [];
		$paramLists = [];
		if($paramJson){
			$paramArr = json_decode($paramJson,true);
			foreach ($paramArr as $key => $value) {
				$value['children'] = $this->paramService->getListAll(['where'=>['lm'=>$value['value']]]);
				$paramLists[] = $value;
			}
		}

		$productList = [];
		$param['with'] = [
			'priceLists' => function($query){
				$query->field('pl_id,title as num,price')->order('px desc,id asc');
		}];
		$param['where'] = [['lm','=',$find['id_lm']]];

		$page = (int) ($data['page'] ?? 1);

		$productLists = $this->service->getList($param,$page,10);

		foreach ($productLists as $key => $value) {
			$param_json = $value['param_json']? json_decode($value['param_json'],true) :[];
			$paramList = [];
			foreach ($param_json as $k => $v) {
				$children = $this->paramService->getByWhere(['id'=>$v],['with'=>['profile']]);
				$paramList[] = $children;
			}

			$value['paramList'] = $paramList;
			$value['priceList'] = $value['priceLists'];
			$value['priceJSON'] = json_encode($value['priceLists']);
			$productList[] = $value;
		}

		$pageRender = $productLists->render();
		$count = $productLists->total();
		$industry = [];

		View::assign([
			'find' => $find,
			'proCate' => $proCate,
			'paramLists' => $paramLists,
			'productList' => $productList,
			'page' => $pageRender,
			'count' => $count,
			'industry' => $industry,
		]);
		return View::fetch();

	}

	public function listAjax(){
		$data = request()->param();
		$lm = $data['lm']??'';
		$idArr = $data['idArr']??[];
		$page = (int)($data['page'] ?? 1);

		$where = [];
		foreach ($idArr as $key => $value) {
			$where[] = ['param_json','like','%"'.$value.'"%'];
		}

		if(!$lm){
			$find = $this->categoryService->getCategoryFind(['where' => ['fid'=>0]]);
		}else{
			$where[] = ['list_lm','like','%,'.$lm.',%'];
			$find = $this->categoryService->getCategoryFind(['where' => ['id_lm'=>$lm]]);
		}

		$proCate = $this->categoryService->getCategoryFind(['where' => ['id_lm'=>$find->fid]]);
		$paramJson = $find['param_json']??$proCate['param_json'];
		$paramArr = [];
		$paramLists = [];
		if($paramJson){
			$paramArr = json_decode($paramJson,true);
			foreach ($paramArr as $key => $value) {
				$value['children'] = $this->paramService->getListAll(['where'=>['lm'=>$value['value']]]);
				$paramLists[] = $value;
			}
		}

		$with = [
			'priceLists' => function($query){
				$query->field('pl_id,title as num,price')->order('px desc,id asc');
		}];
		$productLists = $this->service->getList(['with'=>$with,'where'=>$where],$page,10);

		$productList = [];
		foreach ($productLists as $key => $value) {
			$param_json = $value['param_json']? json_decode($value['param_json'],true) :[];
			$paramList = [];
			foreach ($param_json as $k => $v) {
				$children = ParamCo::with(['profile'])->where($this->where)->where('id',$v)->order($this->orderCo)->find();
				$paramList[] = $children;
			}

			$value['paramList'] = $paramList;
			$value['priceList'] = $value['priceLists'];
			$value['priceJSON'] = json_encode($value['priceLists']);
			$productList[] = $value;
		}

		return json(['code'=>200,'data'=>$productList,'page'=>$productLists,'count'=>$productLists->total()]);
	}


	public function show(){
		$data = request()->param();

		$id = $data['id']??'';

		$find = $this->service->getById($id,['with'=>['profile','priceLists']]);
		$proCate = $this->categoryService->getCategoryList(['where'=>['fid'=>$find['profile']['fid']],'with'=>'profile']);
		$param_json = $find['param_json']? json_decode($find['param_json'],true) :[];
		$paramArr = [];
		$paramLists = [];
		foreach ($param_json as $k => $v) {
			$paramLists[] = $this->paramService->getById($v);
		}
		$priceLists = [];
		if(!empty($find->priceLists)){
			$priceLists = $find->priceLists;
		}

		$priceJSON = json_encode($priceLists);

		$product = ProCo::where($this->where)->order($this->orderCo)->where([['lm','=',$find['lm']],['id','!=',$id]])->limit(15)->select();

		View::assign([
			'find' => $find,
			'priceLists' => $priceLists,
			'priceJSON' => $priceJSON,
			'paramLists' => $paramLists,
			'product' => $product,
			'proCate' => $proCate,
		]);

		return View::fetch();
	}

	public function getParamList($list_lm = '',$param_json = ''){
		$paramJson = '';
		$list_lm = trim($list_lm,',');
		$list_arr = explode(',',$list_lm);
		foreach ($list_arr as $key => $value) {
			$proCate = ProLm::where($this->where)->where('id_lm',$value)->find();
			$paramJson = $proCate['param_json'];
			if($paramJson){
				break;
			}
		}
		$paramLists = [];
		if(!$paramJson){
			return $paramLists;
		}
		$paramArr = json_decode($paramJson,true);
		foreach ($paramArr as $k => $v) {
			$children = ParamCo::where($this->where)->where('lm',$v['value'])->where([['id','in',implode(',', $param_json)]])->order($this->orderCo)->find();
			$v['param_name'] = $children['title']??'';
			$v['param_id'] = $children['id']??'';
			$paramLists[] = $v;
		}
		return $paramLists;
	}

	public function search(){
		$params = Request::param();
		$keyword = $params['keyword']??'';
		$category = ProLm::where('fid',0)->where('pass',1)->order('px desc,id_lm asc')->select();
		$lists = ProCo::with(['priceLists' => function($query){
			$query->field('pl_id,title,price')->order('id asc');
		}])->where('title','like',"%".$keyword."%")->order('ding desc,px desc,id desc')->paginate(5);
		$page = $lists->render();
		$count = $lists->total();
		$productList = [];
		foreach ($lists as $k => $v) {
			$param_json = $v['param_json']? json_decode($v['param_json'],true) :[];
			$paramLists = $this->getParamList($v['list_lm'],$param_json);
			$v['paramList'] = $paramLists;
			$v['priceJSON'] = json_encode($v['priceLists']);
			$productList[] = $v;
		}
		$industry = [];
		View::assign([
			'category' => $category,
			'productList' => $productList,
			'page' => $page,
			'industry' => $industry,
			'keyword' => $keyword,
		]);
		return View::fetch();
	}
}

?>
