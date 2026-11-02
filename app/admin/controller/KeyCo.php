<?php  
namespace app\admin\controller;

use think\facade\View;
use think\facade\Request;
use think\facade\Db;
use app\common\service\KeyService as IS;

class KeyCo extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];

	protected $conf = [];
	protected $service = [];

	protected function initialize() {
		parent::initialize();
		$this->service = new IS();
	}

	public function recycle(){
		if(Request::isPost()){
			
		}else{
			$searchItem = request()->param();
			View::assign([
				'lists' => $lists,
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}
	public function recycle_make(){
		$param = request()->param();
		return $this->recycleMake($this->service,$param);
	}

	public function default(){
		if(request()->isPost()){
		}else{
			// if(empty($this->conf)){
			// 	return '<h1 style="text-align:center;padding-top:30px;">'.$this->langHtml['tip']['configSettingsFile'].'</h1>';
			// 	die();
			// }
			$searchItem = request()->param();
			View::assign([
				'searchItem' => $searchItem
			]);
			return View::fetch();
		}
	}

	public function index(?array $params = []){
		$param = request()->param();
		$param['order'] = $params['order'] ?? 'px desc,wtime desc,id desc';
		$param['with'] = '';
		if(!empty($params)){
			$param = $params;
		}
		return $this->dataTable($this->service,$param);
	}
	
	public function add(){
		if(request()->isPost()){
			$data = request()->param();

			try{
				$info = $this->service->create($data);
				Base::master_log($this->langHtml['tip']['add'].$this->langHtml['tip']['keyword'].$data['title']);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}

		}else{
			return View::fetch('edit');
		}
	}
	public function edit(){
		$data = request()->param();
		if(request()->isPost()){
			$id = $data['id'] ?? '';
			try{
				$update = $this->service->update($id,$data,false);
				Base::master_log($this->langHtml['tip']['edit'].$this->langHtml['tip']['keyword'].$data['title']);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}

		}else{
			$id = $data['id']??'';
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
		$id = $data['id']??'';
		if(empty($id)){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
		}
		try{
			$bol = $this->service->delete($id);
			Base::master_log($this->langHtml['tip']['del'].$this->langHtml['tip']['keyword'].$find['title']);
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail'].$e->getMessage()]);
		}
	}

	public function make(){
		$params = request()->param();
		return $this->statusMake($this->service,$params);
	}
}

?>