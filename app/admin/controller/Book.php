<?php  
namespace app\admin\controller;

use think\facade\View;
use app\common\service\BookService;
use app\common\service\SetupSyService;

class Book extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];

	protected $sy_id = 11;
	protected $conf = [];
	protected BookService $service;

	protected function initialize() {
		parent::initialize();
		$this->conf = SetupSyService::getConfig($this->sy_id);
		$this->service = new BookService();
		View::assign([
			'conf' => $this->conf,
		]);
	}

	//
	public function recycle(){
		if(request()->isPost()){
			
		}else{
			$searchItem = request()->param();
			$keyword = $searchItem['keyword']??'';
			$where = [];
			if(!empty($keyword)){
				$where[] = ["title", "like", "%" . $keyword . "%"];
			}
			$lists = $this->service->getListAll(['where' => $where ,'order' => 'wtime desc'],true,[]);
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


	public function default(){
		if(request()->isPost()){
			$data = request()->param();
			$can = '';
			if(!empty($data['chakan'])){
				$can .= '&chakan='.$data['chakan'];
			}
			if(!empty($data['keyword'])){
				$can .= '&keyword='.$data['keyword'];
			}
			$can_str = ltrim($can,'&');
			return json(['code'=>200,'where'=>$can_str,'msg'=>$this->langHtml['tip']['loading']]);
		}else{
			$searchItem = request()->param();

			$where[] = ['id_re','=',0];
			if(!empty($searchItem['chakan'])){
				if($searchItem['chakan'] == 'chakan1'){
					$where[] = ['chakan','=',1];
				}else if($searchItem['chakan'] == 'chakan2'){
					$where[] = ['chakan','=',0];
				}else if($searchItem['chakan'] == 'huifu1'){
					$where[] = ['huifu','=',1];
				}else if($searchItem['chakan'] == 'huifu2'){
					$where[] = ['huifu','=',0];
				}else if($searchItem['chakan'] == 'pass1'){
					$where[] = ['pass','=',1];
				}else if($searchItem['chakan'] == 'pass2'){
					$where[] = ['pass','=',0];
				}
			}
			if(!empty($searchItem['keyword'])){
				$where[] = ['title','like','%'.$searchItem['keyword'].'%'];
			}

			$lists = $this->service->getListAll(['where' => $where ,'order' => 'wtime desc'],false,[]);

			View::assign([
				'lists' => $lists,
				'searchItem' => $searchItem,
			]);

			return View::fetch();
		}
	}

	public function show(){
		if(request()->isPost()){
			$data = request()->param();
			$id = $data['id']??'';
			if($id == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}

			$find = $this->service->getById($id);
			$h_body = $this->service->getByWhere(['id_re'=>$id]);
			$data['z_body'] = $data['h_body'];
			unset($data['id']);
			unset($data['h_body']);

			if(empty($h_body)){
				$data['wtime'] = date('Y-m-d H:i:s');
				$data['id_re'] = $id;
				$data['lang'] = Lang::getLangSet();
				$data['ip'] = request()->ip();
				$data['chakan'] = 0;
				$data['huifu'] = 1;
				$data['pass'] = 1;
				try{
					$insert = $this->service->create($data,false);
					$update = ['huifu' => 1];
					$up = $this->service->update($id,$update);
					Base::master_log($this->langHtml['tip']['reply'].$this->conf['sy']['name'].$this->langHtml['tip']['information'].'：'.$find['title'].'');
					return json(['code'=>200,'msg'=>$this->langHtml['tip']['reply'].$this->langHtml['tip']['success']]);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['reply'].$this->langHtml['tip']['fail'].$e->getMessage()]);
				}
			}else{
				try{
					$update = $this->service->updateByWhere(['id_re' => $id],$update);
					Base::master_log($this->langHtml['tip']['edit'].$this->conf['sy']['name'].$this->langHtml['tip']['information'].'：'.$find['title'].'');
					return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
				}
			}

		}else{
			$param = request()->param();
			$id = $param['id'];
			if($id == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$find = $this->service->getById($id);

			if($find->chakan == 0){
				$data['chakan'] = 1;
				$update = $this->service->update($id,$data,false);
			}
			$reply = [];
			$reply_find = $this->service->getByWhere(['id_re' => $id]);

			if(!empty($reply_find)){
				$reply['htime'] = $reply_find['wtime'];
				$reply['h_body'] = $reply_find['z_body'];
			}
			
			View::assign([
				'find' => $find,
				'reply' => $reply,
			]);

			return View::fetch('show');
		}
	}

	public function hide(){
		$param = request()->param();
		$id = $param['id'];
		if(empty($id)){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
		}
		$find = $this->service->getById($id);
		if(empty($find)){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['noData']]);
		}
		$data = [];
		if($find['pass'] == 1){
			$data['pass'] = 0;
			$titleName = $this->langHtml['tip']['hide'];
		}else{
			$data['pass'] = 1;
			$titleName = $this->langHtml['tip']['show'];
		}
		try{
			$update = $this->service->update($id,$data,false);
			Base::master_log($titleName.$this->conf['sy']['name'].$this->langHtml['tip']['information'].'：'.$find['title']);
			return json(['code'=>200,'msg'=>$titleName.$this->langHtml['tip']['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>$titleName.$this->langHtml['tip']['fail'].$e->getMessage()]);
		}
	}


	// 删除信息
	public function del(){
		$param = request()->param();
		$id = $param['id'];
		if(empty($id)){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
		}
		$find = $this->service->getById($id);
		try{
			$update = $this->service->delete($id);
			Base::master_log($this->langHtml['tip']['del'].$this->conf['sy']['name'].$this->langHtml['tip']['information'].'：'.$find['title']);
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail'].$e->getMessage()]);
		}
	}

	public function make(){
		if(request()->isPost()){
			$param = request()->param();
			$act = $param['act'];
			if($act == 'del'){
				$id = $param['id'];
				$checkbox = $param['checkbox'];
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				$delete = 0;
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$del = $this->service->delete($value);
						if($del){
							$delete ++;
						}
					}
				}
				if(empty($delete)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
			}
		}
	}
}

?>