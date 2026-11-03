<?php  
namespace app\admin\controller;

use think\facade\View;
use app\common\service\SetupSyService;
use app\common\service\IconService as IS;
use app\common\service\IconCategoryService as CS;
use app\common\trait\ParseListParams;

class IconCo extends Base{
	use ParseListParams;

	protected $middleware = ['AdminCheck','AdminPermission'];

	protected $sy_id = 23;
	protected $conf = [];
	protected IS $service;
	protected CS $categoryService;

	/**
	 * [initialize description]
	 * @return View [description]
	 */
	protected function initialize(){
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
	 * @return View                  [description]
	 */
	public function recycle(){
		if(request()->isPost()){
			
		}else{
			$searchItem = request()->param();
			$category = $this->categoryService->getCategoryList();
			View::assign([
				'category' => $category,
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}

	/**
	 * [recycle_make 回收站操作]
	 * @return array          [description]
	 */
	public function recycle_make(){
		$param = request()->param();
		return $this->recycleMake($this->service,$param);
	}

	/**
	 * [default 信息首页]
	 * @return View                  [description]
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
			$category = $this->categoryService->getCategoryList([],['info']);
			View::assign([
				'category' => $category,
				'searchItem' => $searchItem
			]);
			return View::fetch();
		}
	}
	public function index(?array $params = []){
		$params = $this->parseListParams(['lm']);
		$params['with'] = 'profile';
		return $this->dataTable($this->service,$params);
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
	 * @return View                  [description]
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
			$category = $this->categoryService->getCategoryList();
			View::assign([
				'category' => $category,
				'find' => $find,
			]);
			return View::fetch('edit');
		}
	}

	/**
	 * [del 删除]
	 * @return array          [description]
	 */
	public function del(){
		$data = request()->param();
		return $this->delCommon($this->service,$data,$this->conf);
	}

	/**
	 * [make 操作]
	 * @return array [description]
	 */
	public function make(){
		$params = request()->param();
		return $this->statusMake($this->service,$params,$this->conf);
	}
}

?>