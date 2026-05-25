<?php  
namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Filesystem;
use think\facade\Session;
use app\common\model\Pl_info;
use app\common\model\Pl_file;
use app\common\model\Pl_image;
use app\common\model\SetupSy as MS;
use app\common\model\Person as MC;

class Person extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];

	protected $sy_id = 10;
	protected $route;
	protected $tableLmName;
	protected $tableCoName;
	protected $conf = [];

	protected function initialize() {
		parent::initialize();
		$this->route = Request::controller();
		$lists = MS::where('sy_id',$this->sy_id)->where('lang',$this->lang)->find();
		if(!empty($lists['config'])){
			$this->conf = unserialize($lists['config']);
		}
		$zt = false;
		if(!empty($this->conf)){
			$this->tableCoName = $this->conf['sy']['table_co'] ?? '';
			if((isset($this->conf['co']['tuijian']) && $this->conf['co']['tuijian'] == true) ||
				(isset($this->conf['co']['hot']) && $this->conf['co']['hot'] == true) ||
				(isset($this->conf['co']['pass']) && $this->conf['co']['pass'] == true) ){
				$zt = true;
			}else{
				$zt = false;
			}
		}
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
			$keyword = $searchItem['keyword']??'';
			$where[] = ['lang','=',$this->lang];
			if(!empty($keyword)){
				$where[] = ["username", "like", "%" . $keyword . "%"];
			}

			$count = MC::onlyTrashed()->where($where)->count();
			$lists = MC::onlyTrashed()->where($where)->order('id desc')->select();
			View::assign([
				'lists' => $lists,
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}
	public function recycle_make(){
		$param = Request::param();
		if(Request::isPost()){
			$act = $param['act'];
			$id = $param['id']??'';
			$checkbox = $param['checkbox'] ?? '';
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['noData']]);
			}
			if($act == 'recovery'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				try{
					foreach ($id as $key => $value){
						if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
							MC::onlyTrashed()->find($value)->restore();
						}
					}
					return ['code'=>200,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['success']];
				}catch (\Exception $e){
					return ['code'=>201,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['fail'].$e->getMessage()];
				}
			}
			if($act == 'remove'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				try{
					foreach ($id as $key => $value){
						if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
							MC::destroy($value,true);
						}
					}
					return ['code'=>200,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['success']];
				}catch (\Exception $e){
					return ['code'=>201,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['fail'].$e->getMessage()];
				}
			}
		}
	}

	// 系统设置
	public function setconfig(){

		if(Request::isPost()){
			$conf = Request::param();

			if(!empty($conf['co'])){
				foreach ($conf['co'] as $key => $value) {
					$conf['co'][$key] = changety($value);
				}	
			}
			$data['title'] = $conf['sy']['name'];
			$data['sy_id'] = $this->sy_id;
			$data['lang'] = $this->lang;
			$data['config'] = serialize($conf);
			$find = MS::where('sy_id',$this->sy_id)->where('lang',$this->lang)->find();

			if(!empty($find)){
				try{
					$update = MS::where('sy_id',$this->sy_id)->where('lang',$this->lang)->save($data);
					Base::master_log($this->langHtml['tip']['edit'].$conf['sy']['name'].$this->langHtml['tip']['system'].$this->langHtml['tip']['configFile']);
					return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
				}
			}else{
				try{
					$insert = MS::insert($data);
					Base::master_log($this->langHtml['tip']['edit'].$conf['sy']['name'].$this->langHtml['tip']['system'].$this->langHtml['tip']['configFile']);
					return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
				}
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
			$lm = $searchItem['lm']??'';
			$zt_val = $searchItem['zt_val']??'';
			$keyword = $searchItem['keyword']??'';

			$queryCo = new MC;
			$where[] = ['lang','=',$this->lang];
			$whereOr = [];
			if(!empty($lm)){
				$where[] = ["list_lm", "like", "%," . $lm . ",%"];
			}
			if($zt_val){
				$where[] = ["pass",'=', $zt_val];
			}
			if(!empty($keyword)){
				$whereOr[] = ["username", "like", "%" . $keyword . "%"];
				$whereOr[] = ["phone", "like", "%" . $keyword . "%"];
			}
			if(!empty($searchItem['startDate'])){
				$where[] = ['wtime', '>', $searchItem['startDate']]; 
			}
			if(!empty($searchItem['endDate'])){
				$where[] = ['wtime', '<', $searchItem['endDate']]; 
			}
			$limit = !empty((input('request.limit'))) ? (input('request.limit')) : '10';
			$curr = !empty((input('request.curr'))) ? (input('request.curr')) : '1';

			$count = MC::where($where)->whereOr($whereOr)->count();
			$lists = MC::where($where)->whereOr($whereOr)->order('wtime desc,id desc')->select()->toArray();
			$pageItem = [
				'count' => $count,
				'curr' => $curr,
				'limit' => $limit,
			];
			View::assign([
				'lists' => $lists,
				'pageItem' => $pageItem,
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}

	// 添加信息
	public function add(){
		if(Request::isPost()){
			$data = Request::param();
			$lm = $data['lm'] ?? '0';
			if($this->conf['sy']['need_lm'] == false || $lm == 0 ){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['choose'].$this->langHtml['tip']['category']]);
			}
			if(empty($data['title'])){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['title'].$this->langHtml['tip']['cannotBeEmpty']]);
			}

			if(empty($data['px'])){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['sort'].$this->langHtml['tip']['cannotBeEmpty']]);
			}

			$data['ding'] = 0;
			$data['pass'] = 1;
			$data['hot'] = 0;
			$data['tuijian'] = 0;
			$data['ip'] = Request::ip();
			$data['lang'] = $this->lang;
			if(!empty($data['lm'])){
				$list_lm = ML::where('id_lm',$data['lm'])->find();
				$data['list_lm'] = $list_lm['list_lm'];
			}
			try{
				$insert = MC::insert($data);
				$id = MC::getLastInsID();   
				if(!empty($this->conf['co']['image']) && $this->conf['co']['image'] == true){
					$confImage = new Plimage();
					$pr_id = Session::get($confImage->config['sesname']);
					if(!empty($pr_id)){
						$update['pl_id'] = $id;
						$update['sy_id'] = $this->conf['sy']['id'];
						$up = Db::name($confImage->config['table'])->where('pl_id',$pr_id)->update($update);
						if(empty($up)){
							return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
						}
						Session::delete($confImage->config['sesname']);
					}
				}
				if(!empty($this->conf['co']['info']) && $this->conf['co']['info'] == true){
					$confInfo = new Plinfo();
					$pr_id = Session::get($confInfo->config['sesname']);
					if(!empty($pr_id)){
						$update['pl_id'] = $id;
						$up = Db::name($confInfo->config['table'])->where('pl_id',$pr_id)->update($update);
						if(empty($up)){
							return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
						}
						Session::delete($confInfo->config['sesname']);
					}
				}
				if(!empty($this->conf['co']['file']) && $this->conf['co']['file'] == true){
					$confFile = new Plfile();
					$pr_id = Session::get($confFile->config['sesname']);
					if(!empty($pr_id)){
						$update['pl_id'] = $id;
						$up = Db::name($confFile->config['table'])->where('pl_id',$pr_id)->update($update);
						if(empty($up)){
							return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
						}
						Session::delete($confFile->config['sesname']);
					}
				}
				Base::master_log($this->langHtml['tip']['add'].$this->conf['sy']['name'].$this->langHtml['tip']['information'].'：'.$data['title']);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			return View::fetch('edit');
		}
	}

	// 信息修改
	public function edit(){
		$data = Request::param();
		if(Request::isPost()){
			$id = $data['id'];
			if(!empty($data['password'])){
				if(strlen($data['password']) <= 4 || strlen($data['password']) >= 20 || !checkpassword($data['password'])){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['passwordFormat']]);
				}
				$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
			}else{
				if(empty($id)){
					$data['password'] = password_hash('123456', PASSWORD_DEFAULT);
				}
			}

			try{
				if($id){
					$update = MC::where('id',$id)->update($data);
					Base::master_log($this->langHtml['tip']['edit'].$this->conf['sy']['name'].$this->langHtml['tip']['information'].'：'.$data['username']);
					return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
				}else{
					$data['pass'] = 1;
					$data['wtime'] = time();
					$data['wip'] = Request::ip();
					$data['ltime'] = time();
					$data['lip'] = Request::ip();
					$data['lang'] = $this->lang;
					$insert = MC::insert($data);
					$id = MC::getLastInsID();
					Base::master_log($this->langHtml['tip']['add'].$this->conf['sy']['name'].$this->langHtml['tip']['information'].'：'.$data['username']);
					return json(['code'=>200,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['success']]);
				}
				
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			$id = $data['id'];
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);   
			}
			$find = MC::where('lang',$this->lang)->where('id',$id)->find();$imgList = [];
			if(!empty($find['img_sl'])){
				$imgIds = json_decode($find['img_sl']);
				if (!empty($imgIds) && is_array($imgIds)) {
					$imgList = \app\common\model\Gallery::where('status',1)->where('id','in',$imgIds)->orderField('id', $imgIds)->select();
				}
			}
			$galleryList['imgList'] = $imgList;
			View::assign([
				'find' => $find,
				'galleryList' => $galleryList,
			]);
			return View::fetch();
		}
	}

	// 删除信息
	public function del(){
		$data = Request::param();
		$id = $data['id'];
		if(empty($id)){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
		}
		$find = MC::where('id',$id)->find();
		$data['delete_time'] = date('Y-m-d H:i:s',time());
		try{
			$update = MC::where('id',$id)->save($data);
			Base::master_log($this->langHtml['tip']['del'].$this->conf['sy']['name'].$this->langHtml['tip']['information'].'：'.$find['title']);
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail'].$e->getMessage()]);
		}
	}

	// 
	public function make(){
		$param = Request::param();
		if(Request::isPost()){
			$act = $param['act'];
			$id = $param['id'];
			$px = $param['px']??'';
			$checkbox = $param['checkbox']??'';

			// 批量
			if($act == 'px'){
				$update = 0;
				foreach ($id as $key => $value){
					$data['px'] = $px[$value];
					$up = MC::where('id',$value)->update($data);
					if(!empty($up)){
						$update ++;
					}
				}
				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}

			if($act == 'ding1'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				$update = 0;
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$data['ding'] = 1;
						$up = MC::where('id',$value)->update($data);
						if(!empty($up)){
							$update ++;
						}
					}
				}
				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}

			if($act == 'ding2'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				$update = 0;
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$data['ding'] = 0;
						$up = MC::where('id',$value)->update($data);
						if(!empty($up)){
							$update ++;
						}
					}
				}
				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}

			if($act == 'tj1'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				$update = 0;
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$data['tuijian'] = 1;
						$up = MC::where('id',$value)->update($data);
						if(!empty($up)){
							$update ++;
						}
					}
				}
				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}

			if($act == 'tj2'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				$update = 0;
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$data['tuijian'] = 0;
						$up = MC::where('id',$value)->update($data);
						if(!empty($up)){
							$update ++;
						}
					}
				}
				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}

			if($act == 'hot1'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				$update = 0;
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$data['hot'] = 1;
						$up = MC::where('id',$value)->update($data);
						if(!empty($up)){
							$update ++;
						}
					}
				}
				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}

			if($act == 'hot2'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				$update = 0;
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$data['hot'] = 0;
						$up = MC::where('id',$value)->update($data);
						if(!empty($up)){
							$update ++;
						}
					}
				}
				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}

			if($act == 'pass1'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				$update = 0;
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$data['pass'] = 0;
						$up = MC::where('id',$value)->update($data);
						if(!empty($up)){
							$update ++;
						}
					}
				}
				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}

			if($act == 'pass2'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				$update = 0;
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$data['pass'] = 1;
						$up = MC::where('id',$value)->update($data);
						if(!empty($up)){
							$update ++;
						}
					}
				}
				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}
			if($act == 'del'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				$delete = 0;
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$data['pass'] = 1;
						$data['delete_time'] = date('Y-m-d H:i:s',time());
						$del = MC::where('id',$value)->save($data);
						if(!empty($del)){
							$delete ++;
						}
					}
				}
				if(empty($delete)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['noData'].$this->langHtml['tip']['del']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
			}

			// 单个
			if($act == 'ding'){
				if($id == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
				}
				$find = MC::where('id',$id)->find();

				if($find['ding'] == 1){
					$data['ding'] = 0;
				}else{
					$data['ding'] = 1;
				}
				$update = MC::where('id',$id)->update($data);

				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}
			if($act == 'tuijian'){
				if($id == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
				}
				$find = MC::where('id',$id)->find();

				if($find['tuijian'] == 1){
					$data['tuijian'] = 0;
				}else{
					$data['tuijian'] = 1;
				}
				$update = MC::where('id',$id)->update($data);

				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}
			if($act == 'hot'){
				if($id == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
				}
				$find = MC::where('id',$id)->find();

				if($find['hot'] == 1){
					$data['hot'] = 0;
				}else{
					$data['hot'] = 1;
				}
				$update = MC::where('id',$id)->update($data);

				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}
			if($act == 'pass'){
				if($id == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
				}
				$find = MC::where('id',$id)->find();

				if($find['pass'] == 1){
					$data['pass'] = 0;
				}else{
					$data['pass'] = 1;
				}
				$update = MC::where('id',$id)->update($data);

				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}
			if($act == 'sort'){
				if($id == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
				}
				$find = MC::where('id',$id)->find();

				$data['px'] = $param['px'] ?? '100';
				$update = MC::where('id',$id)->update($data);

				if(empty($update)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}
		}
	}

}

?>