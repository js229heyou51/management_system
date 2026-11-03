<?php  
namespace app\admin\controller;

use think\facade\View;
use think\facade\Request;
use app\common\service\PersonService;
use app\common\service\SetupSyService;

class Person extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];

	protected $sy_id = 10;
	protected PersonService $service;
	protected $route;
	protected $conf = [];

	protected function initialize() {
		parent::initialize();
		$this->route = Request::controller();
		$this->service = new PersonService();
		$this->conf = SetupSyService::getConfig($this->sy_id);
		$zt = SetupSyService::getCoZt($this->conf);
		View::assign([
			'route' => $this->route,
			'conf' => $this->conf,
			'zt'  => $zt,
		]);
	}

	//
	public function recycle(){
		if(Request::isPost()){
			
		}else{
			$searchItem = Request::param();
			View::assign([
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}
	public function recycle_make(){
		$param = request()->param();
		return $this->recycleMake($this->service,$param);
	}

	// 系统设置
	public function setconfig(){
		if(request()->isPost()){
			$conf = request()->param();
			try{
				$update = SetupSyService::update($this->sy_id,$conf);
				Base::master_log($this->langHtml['tip']['edit'].$conf['sy']['name'].$this->langHtml['tip']['system'].$this->langHtml['tip']['configFile']);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			return View::fetch();
		}
	}

	// 信息首页
	public function default(){
		if(Request::isPost()){

		}else{
			if(empty($this->conf)){
				return '<h1 style="text-align:center;padding-top:30px;">'.$this->langHtml['tip']['configSettingsFile'].'</h1>';
				die();
			}
			$searchItem = Request::param();
			View::assign([
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}
	public function index(?array $params = []){
		$param = request()->param();
		$param['order'] = $params['order'] ?? 'wtime desc,id desc';
		$param['with'] = '';
		if(!empty($params)){
			$param = $params;
		}
		return $this->dataTable($this->service,$param);
	}

	// 添加信息
	public function add(){
		if(Request::isPost()){
			$data = request()->param();
			return $this->addCommon($this->service,$data,$this->conf,$this->sy_id);
		}else{
			return View::fetch('edit');
		}
	}

	// 信息修改
	public function edit(){
		$data = Request::param();
		if(Request::isPost()){
			return $this->editCommon($this->service,$data,$this->conf);
		}else{
			$id = $data['id'];
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);   
			}
			$find = $this->service->getById($id);
			View::assign([
				'find' => $find,
			]);
			return View::fetch();
		}
	}

	// 删除信息
	public function del(){
		$data = request()->param();
		return $this->delCommon($this->service,$data,$this->conf);
	}

	// 
	public function make(){
		$params = request()->param();
		return $this->statusMake($this->service,$params,$this->conf);
	}

}

?>