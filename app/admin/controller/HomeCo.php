<?php  
namespace app\admin\controller;

use think\facade\View;
use think\facade\Session;
use app\common\service\SetupSyService;
use app\common\service\HomeService as IS;
use app\common\service\HomeCategoryService as CS;

class HomeCo extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];

	protected $sy_id = 1;
	protected $conf = [];
	protected $service = [];
	protected $categoryService = [];

	protected function initialize() {
		parent::initialize();
		$this->conf = SetupSyService::getConfig($this->sy_id);
		$zt = SetupSyService::getCoZt($this->conf);
		$this->service = new IS();
		$this->categoryService = new CS();
		View::assign([
			'conf' => $this->conf,
			'zt'  => $zt,
		]);
	}

	/**
	 * [recycle 回收站]
	 * @return [type]                  [description]
	 */
	public function recycle(){
		if(request()->isPost()){
		
		}else{
			$searchItem = request()->param();
			$keyword = $searchItem['keyword']??'';
			$where = [];
			if(!empty($keyword)){
				$where[] = ["title", "like", "%" . $keyword . "%"];
			}
			$lists = $this->service->getListAll($where,true);
			$category = $this->categoryService->getCategoryList();
			View::assign([
				'lists' => $lists,
				'category' => $category,
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}

	/**
	 * [recycle_make 回收站操作]
	 * @return [type]          [description]
	 */
	public function recycle_make(){
		$param = request()->param();
		return $this->recycleMake($this->service,$param);
	}

	/**
	 * [default 信息首页]
	 * @param  IS     $service         [对应模型的信息service]
	 * @param  CS     $categoryService [对应模型的分类service]
	 * @return [type]                  [description]
	 */
	public function default(){
		if(request()->isPost()){
			$data = request()->param();
			return $this->defaultCommon($data);
		}else{
			if(empty($this->conf)){
				return '<h1 style="text-align:center;padding-top:30px;">'.$this->langHtml['tip']['configSettingsFile'].'</h1>';
				die();
			}
			$searchItem = request()->param();
			$params['where'] = $this->setWhere($searchItem);
			$params['keyword'] = $searchItem['keyword']??'';
			$lists = $this->service->getListAll($params);
			$category = $this->categoryService->getCategoryList([],['info']);
			View::assign([
				'lists' => $lists,
				'category' => $category,
				'searchItem' => $searchItem
			]);
			return View::fetch();
		}
	}

	/**
	 * [add 添加]
	 */
	public function add(){
		if(request()->isPost()){
			$data = request()->param();
			return $this->addCommon($this->service,$data,$this->conf,$this->sy_id);
		}else{
			$category = $this->categoryService->getCategoryList();
			View::assign([
				'category' => $category,
			]);
			return View::fetch('edit');
		}
	}

	/**
	 * [edit 编辑]
	 * @return [type]                  [description]
	 */
	public function edit(){
		$data = request()->param();
		if(request()->isPost()){
			return $this->editCommon($this->service,$data,$this->conf);
		}else{
			$id = $data['id']??'';
			if(empty($id)){
				return '<h1 style="text-align:center;padding-top:30px;">'.$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty'].'</h1>';
				die();
			}
			$find = $this->service->getById($id);
			$findLm = $this->categoryService->getCategoryById($find->lm);
			$category = $this->categoryService->getCategoryList();
			View::assign([
				'category' => $category,
				'find' => $find,
				'findLm' => $findLm,
			]);
			return View::fetch('edit');
		}
	}

	/**
	 * [del 删除]
	 * @return [type]          [description]
	 */
	public function del(){
		$data = request()->param();
		return $this->delCommon($this->service,$data,$this->conf);
	}

	/**
	 * [make 操作]
	 * @param  IS     $service [对应模型的信息service]
	 * @return [type]          [description]
	 */
	public function make(){
		$params = request()->param();
		$act = $params['act'];
		if($act == 'ajax'){
			$lm = $params['lm']??'';
			$findLm = $this->categoryService->getCategoryById($lm);
			return json(['code'=>200,'data'=>$findLm]);
		}
		return $this->statusMake($this->service,$params);
	}

}

?>