<?php  
namespace app\admin\controller;

use think\facade\View;
use app\common\service\SetupSyService;
use app\common\service\ParamService as IS;
use app\common\service\ParamCategoryService as CS;

class ParamCo extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];

	protected $sy_id = 6;
	protected $conf = [];
	protected $service = [];
	protected $categoryService = [];

	/**
	 * [initialize description]
	 * @return [type] [description]
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
	 * @return [type]                  [description]
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
	 * @return [type]          [description]
	 */
	public function recycle_make(){
		$param = request()->param();
		return $this->recycleMake($this->service,$param);
	}

	/**
	 * [default 信息首页]
	 * @return [type]                  [description]
	 */
	public function default(){
		if(request()->isPost()){
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
		$param = request()->param();
		$param['order'] = $params['order'] ?? 'ding desc,px desc,wtime desc,id desc';
		$param['with'] = 'profile';
		if(!empty($params)){
			$param = $params;
		}
		return $this->dataTable($this->service,$param);
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
	 * @return [type]          [description]
	 */
	public function del(){
		$data = request()->param();
		return $this->delCommon($this->service,$data,$this->conf);
	}

	/**
	 * [make 操作]
	 * @return [type] [description]
	 */
	public function make(){
		$params = request()->param();
		return $this->statusMake($this->service,$params);
	}
}

?>