<?php  
namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use app\common\model\Master as M;
use app\common\model\MasterAction as MA;
use app\common\model\MasterMenu as MM;
use app\common\model\MasterLog as ML;
use app\admin\validate\Master as V;
/**
 * 
 */
class Master extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];

	protected $route;

	protected function initialize() {
		parent::initialize();
		$this->route = Request::controller();
		View::assign([
			'route' => $this->route,
		]);
	}

	public function recycle(){
		if(Request::isPost()){
			

		}else{

			$searchItem = Request::param();
			$keyword = $searchItem['keyword']??'';
			$where[] = ['lang','=',$this->lang];
			// $where = [];
			if(!empty($keyword)){
				$where[] = ["title", "like", "%" . $keyword . "%"];
			}
			$limit = !empty((input('request.limit'))) ? (input('request.limit')) : '10';
			$curr = !empty((input('request.curr'))) ? (input('request.curr')) : '1';

			$count = M::onlyTrashed()->where($where)->count();
			$lists = M::onlyTrashed()->where($where)->order('id desc')->page($curr,$limit)->select();
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
							M::onlyTrashed()->find($value)->restore();
						}
					}
				}catch (\Exception $e){
					return ['code'=>201,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['fail'].$e->getMessage()];
				}
				return ['code'=>200,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['success']];
			}
			if($act == 'remove'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				try{
					foreach ($id as $key => $value){
						if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
							M::destroy($value,true);
						}
					}
					
				}catch (\Exception $e){
					return ['code'=>201,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['fail'].$e->getMessage()];
				}
				return ['code'=>200,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['success']];
			}
		}
	}

	public function setconfig(){

		return View::fetch();
	}

	public function default(){
		if(Request::isPost()){
			$data = Request::param();
			$can = '';
			if(!empty($data['zt_val'])){
				$can .= '&zt_val='.$data['zt_val'];
			}
			if(!empty($data['keyword'])){
				$can .= '&keyword='.$data['keyword'];
			}
			$can_str = ltrim($can,'&');
			return json(['code'=>200,'where'=>$can_str,'msg'=>$this->langHtml['tip']['loading']]);
		}else{
			$searchItem = Request::param();
			$where[] = ['lang','=',$this->lang];
			if(!empty($searchItem['zt_val'])){
				if($searchItem['zt_val'] == 'pass1'){
					$where[] = ['pass','=',1];
				}else if($searchItem['zt_val'] == 'pass2'){
					$where[] = ['pass','=',0];
				}
			}
			if(!empty($searchItem['keyword'])){
				$where[] = [
					['username','like','%'.$searchItem['keyword'].'%'],
					['rename','like','%'.$searchItem['keyword'].'%'],
				];
			}
			if(!empty($searchItem['startDate'])){
				$where[] = ['wtime', '>', $searchItem['startDate']]; 
			}
			if(!empty($searchItem['endDate'])){
				$where[] = ['wtime', '<', $searchItem['endDate']]; 
			}
			$limit = !empty((input('request.limit'))) ? (input('request.limit')) : '10';
			$curr = !empty((input('request.curr'))) ? (input('request.curr')) : '1';

			$count = M::where($where)->count();
			$lists = M::where($where)->order('id desc')->select()->toArray();
			$pageItem['count'] = $count;
			$pageItem['curr'] = $curr;
			$pageItem['limit'] = $limit;
			View::assign([
				'lists' => $lists,
				'pageItem' => $pageItem,
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}

	// 添加管理员
	public function add(){
		if(Request::isPost()){
			$data = Request::param();

			$validate = new V;
			if(!$validate->scene('Master')->check($data)) 
			return ['code'=>201,'msg'=>$validate->getError()];
			$find = M::where('username',$data['username'])->find();
			if(!empty($find)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['usernameBeenUsed']]);
			}
			$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

			$menu = MM::field('id')->where(['fid'=>0,'pass'=>1])->where('lang',$this->lang)->order('px asc,id asc')->select()->toArray();
			$menuArr = [];
			foreach ($menu as $key => $value) {
				$menuArr[] = $value['id']; 
			}
			$menuQuery = implode(',',$menuArr);
			$action = MA::field('title_val')->where([['pass','=',1],['fid','in',$menuQuery]])->where('lang',$this->lang)->order('px asc,id asc')->select()->toArray();
			$actionArr = [];
			foreach ($action as $key => $value) {
				$actionArr[] = $value['title_val']; 
			}
			if(!empty($data['menu_list'])){
				if (!array_diff($menuArr,$data['menu_list'])){
					$data['menu_list']='all';
				}else{
					$data['menu_list']=implode(',',$data['menu_list']);
				}
			}else{
				$data['menu_list']='all';
			}

			if(!empty($data['action_list'])){
				if (!array_diff($actionArr,$data['action_list'])){
					$data['action_list']='all';
				}else{
					$data['action_list']=implode(',',$data['action_list']);
				}
			}else{
				$data['action_list']='all';
			}

			$data['pass'] = 1;
			$data['wtime'] = $data['wtime'] ?? date('Y-m-d H:i:s');
			$data['wip'] = Request::ip();
			$data['ltime'] = $data['wtime'] ?? date('Y-m-d H:i:s');
			$data['lip'] = Request::ip();
			$data['lang'] = $this->lang;

			try{
				$insert = M::insert($data);
				Base::master_log($this->langHtml['tip']['add'].$this->langHtml['tip']['admin'].$this->langHtml['tip']['information'].'：'.$data['username']);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}

		}else{
			$lists = MM::where(['pass'=>1,'fid'=>0])->where('lang',$this->lang)->order('px asc,id asc')->select()->toArray();
			$action = MA::where(['pass'=>1])->where('lang',$this->lang)->order('px asc,id asc')->select()->toArray();
			$actions = [];
			foreach ($action as $key => $value) {
				$actions[$value['fid']][] = $value;
			}

			foreach ($lists as $key => $value) {
				if(!empty($actions[$value['id']])){
					$lists[$key]['children'] = $actions[$value['id']];
				}
			}
			View::assign([
				'lists' => $lists,
			]);

			return View::fetch('edit');
		}
	}

	// 修改管理员
	public function edit(){
		if(Request::isPost()){
			$data = Request::param();
			$id = $data['id'];
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);	
			}

			if(!empty($data['password'])){
				if(strlen($data['password']) <= 4 || strlen($data['password']) >= 20 || !checkpassword($data['password'])){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['passwordFormat']]);
				}
				$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
			}

			$menu = MM::field('id')->where('lang',$this->lang)->where(['fid'=>0,'pass'=>1])->order('px asc,id asc')->select()->toArray();
			$menuArr = [];
			foreach ($menu as $key => $value) {
				$menuArr[] = $value['id']; 
			}
			$menuQuery = implode(',',$menuArr);
			$action = MA::field('title_val')->where('lang',$this->lang)->where([['pass','=',1],['fid','in',$menuQuery]])->order('px asc,id asc')->select()->toArray();
			$actionArr = [];
			foreach ($action as $key => $value) {
				$actionArr[] = $value['title_val']; 
			}
			if(!empty($data['menu_list'])){
				if (!array_diff($menuArr,$data['menu_list'])){
					$data['menu_list']='all';
				}else{
					$data['menu_list']=implode(',',$data['menu_list']);
				}
			}else{
				$data['menu_list']='all';
			}

			if(!empty($data['action_list'])){
				if (!array_diff($actionArr,$data['action_list'])){
					$data['action_list']='all';
				}else{
					$data['action_list']=implode(',',$data['action_list']);
				}
			}else{
				$data['action_list']='all';
			}
			// SetupGl_setconfig,SetupGl_edit,KeyCo_add,KeyCo_del,KeyCo_edit,KeyCo_default,PlDao_pl_imru_tool,PlDao_pl_daru_tool,PlDao_pl_dacu_tool,Master_add,Master_del,Master_edit,Master_default,Master_log_default
			$find = M::where('id',$id)->find();
			if(empty($data['password'])){
				$data['password'] = $find['password'];
			}
			if($this->admin['username'] == $find['username']){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['changePwdTip']]);
			}

			try{
				$update = M::where('id',$id)->save($data);
				Base::master_log($this->langHtml['tip']['edit'].$this->langHtml['tip']['admin'].$this->langHtml['tip']['information'].'：'.$find['username']);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}

		}else{
			$data = Request::param();
			$id = $data['id'];
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);	
			}

			$find = M::where('id',$id)->find()->toArray();

			$lists = MM::where(['pass'=>1,'fid'=>0])->where('lang',$this->lang)->order('px asc,id asc')->select()->toArray();
			$action = MA::where(['pass'=>1])->where('lang',$this->lang)->order('px asc,id asc')->select()->toArray();
			$actions = [];
			foreach ($action as $key => $value) {
				$actions[$value['fid']][] = $value;
			}

			foreach ($lists as $key => $value) {
				if(!empty($actions[$value['id']])){
					$lists[$key]['children'] = $actions[$value['id']];
				}
			}
			if(!empty($find['menu_list'])){
				if($find['menu_list'] != 'all'){
					$find['menu_list'] = explode(',',$find['menu_list']);
				}
			}
			if(!empty($find['action_list'])){
				if($find['action_list'] != 'all'){
					$find['action_list'] = explode(',',$find['action_list']);
				}
			}
			View::assign([
				'lists' => $lists,
				'find' => $find,
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
		$data['delete_time'] = date('Y-m-d H:i:s',time());
		$find = M::where('id',$id)->find();
		try{
			$update = M::where('id',$id)->save($data);
			Base::master_log($this->langHtml['tip']['del'].$this->langHtml['tip']['admin'].$this->langHtml['tip']['information'].'：'.$find['username']);
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail'].$e->getMessage()]);
		}
	}

	public function make(){
		$param = Request::param();
		if(Request::isPost()){
			$act = $param['act'];
			$id = $param['id'];
			$px = $param['px']??'';
			$checkbox = $param['checkbox']??'';

			if($act == 'del'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				$delete = 0;
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$data['pass'] = 1;
						$del = M::where('id',$value)->delete();
						if(!empty($del)){
							$delete ++;
						}
					}
				}
				if(empty($delete)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
			}

			// 单个
			if($act == 'pass'){
				if($id == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
				}
				$find = M::where('id',$id)->find();

				if($find['pass'] == 1){
					$data['pass'] = 0;
				}else{
					$data['pass'] = 1;
				}
				try{
					$update = M::where('id',$id)->save($data);
					return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
				}
			}
		}
	}





	public function default_log(){
		if(Request::isPost()){
			$data = Request::param();
			$can = '';
			if(!empty($data['zt_val'])){
				$can .= '&zt_val='.$data['zt_val'];
			}
			if(!empty($data['keyword'])){
				$can .= '&keyword='.$data['keyword'];
			}
			$can_str = ltrim($can,'&');
			return json(['code'=>200,'where'=>$can_str,'msg'=>$this->langHtml['tip']['loading']]);
		}else{



			$searchItem = Request::param();
			$where[] = ['lang','=',$this->lang];
			if(!empty($searchItem['keyword'])){
				$where[] = ['username','like','%'.$searchItem['keyword'].'%'];
			}
			if(!empty($searchItem['startDate'])){
				$where[] = ['create_at', '>', $searchItem['startDate']]; 
			}
			if(!empty($searchItem['endDate'])){
				$where[] = ['create_at', '<', $searchItem['endDate']]; 
			}
			$limit = !empty((input('request.limit'))) ? (input('request.limit')) : '10';
			$curr = !empty((input('request.curr'))) ? (input('request.curr')) : '1';

			$count = ML::where($where)->count();
			$lists = ML::where($where)->order('id desc')->select();
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
}


?>