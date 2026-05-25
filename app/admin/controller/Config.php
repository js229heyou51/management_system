<?php  
namespace app\admin\controller;

use think\facade\View;
use think\facade\Db;
use think\facade\Request;
use think\facade\Lang;
use app\common\model\Config as CF;
use app\common\model\MasterMenu as MM;
use app\common\model\MasterAction as MA;
use app\common\model\IconCo;

class Config extends Base{

	protected $middleware = ['AdminCheck'];
	
	protected $route = 'Config';
	
	protected function initialize() {
		parent::initialize();
		View::assign([
			'route' => $this->route,
		]);
	}


	// 网站配置
	public function index(){
		if(Request::isPost()){
			$param = Request::param();
			$list['sy_seo'] = changety($param['sy_seo']??'');
			$list['log'] = changety($param['log']??'');
			$list['key'] = changety($param['key']??'');
			$lang = $param['lang'];
			if(!empty($lang)){
				foreach ($lang as $k => $v) {
					$arr['name'] = $param['name'][$k];
					$arr['lang'] = $param['lang'][$k];
					$mlang[] = $arr;
				}
			}else{
				$arr['name'] = '';
				$arr['lang'] = $this->lang;
				$mlang[] = $arr;
			}
			$list['mlang'] = $mlang;
			$data['type'] = 'config';
			$data['lists'] = serialize($list);
			try{
				CF::where(['type'=>$data['type']])->save($data);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch(\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			$find = CF::where(['type'=>'config'])->find();

			if(!empty($find['lists'])){
				$find['config'] = unserialize($find['lists']);
			}

			View::assign([
				'find' => $find,
			]);
			return View::fetch();
		}
	}

	// navigation 导航管理
	public function navigation(){
		if(Request::isPost()){

			$param = Request::param();
			$ac = $param['ac']??'';
			$id = $param['id']??'';
			$px = $param['px']??'';

			if($ac == 'list'){
				$where[] = ['lang','=',$this->lang];
				$lists = MM::where($where)->where('fid',0)->order('fid asc,px asc,id asc')->select();
				$treeArr = [];
				foreach ($lists as $k => $v) {
					$v['children'] = MM::where($where)->where('fid',$v['id'])->order('fid asc,px asc,id asc')->select();
					$treeArr[] = $v;
				}
				return json(['code'=>0,'data'=>$treeArr,'count' => 200]);	
			}

			if($ac == 'pass'){
				$find = MM::where('id',$id)->find();
				if($find['pass'] == 1){
					$pass = 0;
				}else{
					$pass = 1;
				}
				$update = MM::where('id',$id)->update(['pass'=>$pass]);
			}

			if($ac == 'cond'){
				$update = MM::where('ty',2)->update(['pass'=>1]);
			}
			if($ac == 'conh'){
				$update = MM::where('ty',2)->update(['pass'=>0]);
			}
			if($ac == 'seod'){
				$update = MM::where('ty',3)->update(['pass'=>1]);
			}
			if($ac == 'seoh'){
				$update = MM::where('ty',3)->update(['pass'=>0]);
			}

			if($ac == 'px'){
				$update = MM::where('id',$id)->update(['px'=>$px]);
			}

			if(empty($update)){
				if($update <= 0){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['noData'].$this->langHtml['tip']['edit']]);
				}
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
			}
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
		}else{

			$where[] = ['lang','=',$this->lang];
			$menus = MM::order('px asc,id asc')->where($where)->select()->toArray();

			$menuTree = [];

			foreach($menus as $k => $v){
				$menuTree[$v['fid']][] = $v;
			}
			View::assign([
				'menus' => $menus,
			]);

			return View::fetch();
		}
	}

	// 复制导航
	public function navCopy(){
		$param = Request::param();
		if(Request::isPost()){
			$act = $param['act']??'';
			if(empty($act)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['parameterError']]);
			}

			if($act == 1){
				$data['title'] = $param['ftitle']??'';
				if($data['title'] == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['name'].$this->langHtml['tip']['cannotBeEmpty']]);
				}
				$data['pass'] = $param['fpass']??'';
				$data['px'] = $param['fpx']??'';
				$data['fid'] = 0;
				$data['lang'] = $this->lang;

				if($data['px'] == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['sort'].$this->langHtml['tip']['cannotBeEmpty']]);
				}
				try {
					$insert = MM::insert($data);
					$fid = MM::getLastInsID();
					$id = $param['id']??'';
					$arr = [];
					$data = [];
					foreach ($id as $k => $v){
						$arr['fid'] = $fid;
						$arr['ty'] = $param['ty'.$v]??'';
						$arr['pass'] =  $param['pass'.$v]??'';
						$arr['title'] =  $param['title'][$k]??'';
						$arr['link_url'] = $param['link_url'][$k]??'';
						$arr['px'] = $param['px'][$k]??'';;
						$arr['lang'] = $this->lang;
						$data[] = $arr;
					}
					$insert = MM::insertAll($data);
					return json(['code'=>200,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['success']]);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['succ'].$e->getMessage()]);
				}
			}else if($act == 2){
				$data['fid'] = $param['fid']??'';
				$data['ty'] = $param['ty']??'';
				$data['title'] = $param['title']??'';
				if($data['title'] == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['name'].$this->langHtml['tip']['cannotBeEmpty']]);
				}
				$data['link_url'] = $param['link_url']??'';
				$data['pass'] = $param['pass']??'';
				$data['px'] = $param['px']??'';
				$data['lang'] = $this->lang;

				if($data['px'] == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['sort'].$this->langHtml['tip']['cannotBeEmpty']]);
				}
				try {
					$insert = MM::insert($data);
					return json(['code'=>200,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['success']]);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['fail'].$e->getMessage()]);
				}
			}

		}else{
			$id = (input('get.id'));
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$act = (input('get.act'));

			$nav = MM::where('fid',0)->order('px asc,id asc')->select()->toArray();

			$find = MM::where('id',$id)->find();

			$list = [];
			if($act == 1){
				$list = MM::where('fid',$id)->order('px asc')->select()->toArray();
			}

			View::assign([
				'find' => $find,
				'list' => $list,
				'act' => $act,
				'nav' => $nav,
			]);

			return View::fetch();
		}
	}

	// 添加导航
	public function navAdd(){
		if(Request::isPost()){
			$data = Request::param();
			if($data['title'] == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['name'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			if($data['px'] == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['sort'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$data['lang'] = $this->lang;

			try {
				$insert = MM::insert($data);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{

			$where[] = ['lang','=',$this->lang];

			$iconList = IconCo::where($where)->field('id,title,keyword')->where('lm',1)->order('ding desc,px desc,id desc')->select();

			$nav = MM::where('fid',0)->where($where)->order('px asc,id asc')->select()->toArray();

			View::assign([
				'nav' => $nav,
				'iconList' => $iconList,
			]);
			return View::fetch();
		}
	}

	// 编辑导航
	public function navEdit(){
		if(Request::isPost()){
			$data = Request::param();
			$id = $data['id'];
			$data['fid'] = (input('post.fid'));
			$data['ty'] = (input('post.ty'));
			$data['title'] = (input('post.title'));
			if($data['title'] == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['name'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$data['title2'] = (input('post.title2'));
			$data['link_url'] = (input('post.link_url'));
			$data['link_url2'] = (input('post.link_url2'));
			$data['pass'] = (input('post.pass'));
			$data['px'] = (input('post.px'));
			$data['lang'] = $this->lang;

			if($data['px'] == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['sort'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			try {
				$update = MM::where('id',$id)->save($data);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{

			$id = (input('get.id'));
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}

			$where[] = ['lang','=',$this->lang];
			$iconList = IconCo::where($where)->field('id,title,keyword')->where('lm',1)->order('ding desc,px desc,id desc')->select();
			$find = MM::where('id',$id)->find();
			$nav = MM::where('fid',0)->order('px asc,id asc')->select()->toArray();

			View::assign([
				'iconList' => $iconList,
				'find' => $find,
				'nav' => $nav,
			]);


			return View::fetch();
		}
	}
	// 删除导航
	public function navDel(){
		$data = Request::param();
		$id = $data['id']??'';
		if(empty($id)){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
		}
		try {
			$data['delete_time'] = date('Y-m-d H:i:s',time());
			$update = MM::where('id',$id)->save($data);
			$lists = MM::where('fid',$id)->select()->toArray();
			if(!empty($lists)){
				$update = MM::where('fid',$id)->save($data);
			}
			$limit = MA::where('fid',$id)->select()->toArray();
			if(!empty($limit)){
				$update = MA::where('fid',$id)->save($data);
			}
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail'].$e->getMessage()]);
		}
	}

	// 导航回收站
	public function navRecycle(){
		if(Request::isPost()){
			$param = Request::param();
			$ac = $param['ac']??'';
			$id = $param['id']??'';

			if($ac == 'list'){
				$limit = input('get.limit')??'1000';
				$where[] = ['lang','=',$this->lang];
				$lists = MM::where($where)->order('fid,px asc,id asc')->paginate($limit);
				return json(['code'=>200,'data'=>$lists->items(),'extend'=>['count' => $lists->total()]]);	
			}

		}else{

			$where[] = ['lang','=',$this->lang];
			$count = MM::onlyTrashed()->where($where)->count();
			$lists = MM::onlyTrashed()->where($where)->order('px desc,id desc')->select();
			View::assign([
				'lists' => $lists,
			]);

			return View::fetch();
		}
	}

	public function navRecycleMake(){
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
							MM::onlyTrashed()->find($value)->restore();
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
							MM::destroy($value,true);
						}
					}
					return ['code'=>200,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['success']];
				}catch (\Exception $e){
					return ['code'=>201,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['fail'].$e->getMessage()];
				}
			}
		}
	}

	// limit 权限项管理
	public function limit(){
		if(Request::isPost()){
			$param = Request::param();
			$ac = $param['ac']??'';
			$id = $param['id']??'';

			if($ac == 'list'){
				$where[] = ['lang','=',$this->lang];
				$lists = MM::where($where)->where('fid',0)->order('fid asc,px asc,id asc')->select();
				$treeArr = [];
				foreach ($lists as $k => $v) {
					$v['children'] = MA::where($where)->where('fid',$v['id'])->order('fid asc,px asc,id asc')->select();
					$treeArr[] = $v;
				}
				return json(['code'=>0,'data'=>$treeArr,'count' => 200]);	
			}
			if($ac == 'px'){
				$px = $param['px']??'';
				$update = MA::where('id',$id)->update(['px'=>$px]);
			}
			if($ac == 'pass'){
				$find = MA::where('id',$id)->find();
				if($find['pass'] == 1){
					$pass = 0;
				}else{
					$pass = 1;
				}
				$update = MA::where('id',$id)->update(['pass'=>$pass]);
			}

			if($ac == 'cond'){
				$update = MA::where('ty',2)->update(['pass'=>1]);
			}
			if($ac == 'conh'){
				$update = MA::where('ty',2)->update(['pass'=>0]);
			}
			if($ac == 'seod'){
				$update = MA::where('ty',3)->update(['pass'=>1]);
			}
			if($ac == 'seoh'){
				$update = MA::where('ty',3)->update(['pass'=>0]);
			}
			if(empty($update)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
			}
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
		}else{

			$menu = MM::where('fid',0)->where('lang',$this->lang)->order('px asc,id asc')->select()->toArray();
			$limit = MA::order('px asc,id asc')->where('lang',$this->lang)->select()->toArray();

			$limitTree = [];

			foreach($limit as $k => $v){
				$limitTree[$v['fid']][] = $v;
			}
			View::assign([
				'menu' => $menu,
				'limitTree' => $limitTree,
			]);

			return View::fetch();
		}
	}

	// 复制权限项
	public function limitCopy(){
		if(Request::isPost()){
			$param = Request::param();
			$act = $param['act']??'';
			if(empty($act)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['parameterError']]);
			}

			if($act == 1){
				$fid = $param['fid']??'';
				if($fid == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['group']]);
				}
				$id = $param['id']??[];
				$arr = [];
				$data = [];
				foreach ($id as $k => $v){
					$arr['fid'] = $fid;
					$arr['title'] = $param['title'][$k];
					$arr['title_val'] = $param['title_val'][$k];
					$arr['pass'] = $param['pass'.$v];
					$arr['px'] = $param['px'][$k];
					$data[] = $arr;
				}
				try {
					$insert = MA::insertAll($data);
					return json(['code'=>200,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['success']]);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['fail'].$e->getMessage()]);
				}

			}else if($act == 2){
				$data['fid'] = $param['fid']??'';
				if($data['fid'] == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['group']]);
				}
				$data['title'] = $param['title']??'';
				if($data['title'] == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['limitName'].$this->langHtml['tip']['cannotBeEmpty']]);
				}
				$data['title_val'] = $param['title_val']??'';
				if($data['title_val'] == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['limitValue'].$this->langHtml['tip']['cannotBeEmpty']]);
				}
				$data['pass'] = $param['pass']??'';
				$data['px'] = $param['px']??'';
				$data['lang'] = $this->lang;

				if($data['px'] == ''){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['sort'].$this->langHtml['tip']['cannotBeEmpty']]);
				}

				try {
					$insert = MA::insert($data);
					return json(['code'=>200,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['success']]);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['fail'].$e->getMessage()]);
				}
			}

		}else{
			$id = (input('get.id'));
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$act = (input('get.act'));

			$nav = MM::where('fid',0)->order('px asc,id asc')->select()->toArray();

			

			$list = [];
			if($act == 1){
				$find = MM::where('id',$id)->find();
				$list = MA::where('fid',$id)->order('px asc')->select()->toArray();
			}else{
				$find = MA::where('id',$id)->find();
			}

			View::assign([
				'find' => $find,
				'list' => $list,
				'act' => $act,
				'nav' => $nav,
			]);

			return View::fetch();
		}
	}

	// 添加权限项
	public function limitAdd(){
		if(Request::isPost()){
			$data = Request::param();
			if($data['title'] == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['limitName'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			if($data['title_val'] == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['limitValue'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$data['lang'] = $this->lang;
			if($data['px'] == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['sort'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			try {
				$insert = MA::insert($data);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{

			$nav = MM::where('fid',0)->where('lang',$this->lang)->order('px asc,id asc')->select()->toArray();

			View::assign([
				'nav' => $nav,
			]);
			return View::fetch();
		}
	}

	// 编辑权限项
	public function limitEdit(){
		if(Request::isPost()){
			$data = Request::param();
			if($data['title'] == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['limitName'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			if($data['title_val'] == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['limitValue'].$this->langHtml['tip']['cannotBeEmpty']]);
			}

			if($data['px'] == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['sort'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$data['lang'] = $this->lang;

			try {
				$update = MA::update($data);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{

			$id = (input('get.id'));
			if(empty($id)){
				return json(['code'=>1,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}

			$find = MA::where('id',$id)->find();
			$nav = MM::where('fid',0)->order('px asc,id asc')->select()->toArray();

			View::assign([
				'find' => $find,
				'nav' => $nav,
			]);


			return View::fetch();
		}
	}

	// 删除权限项
	public function limitDel(){
		$table = 'master_menu';
		$id = (input('post.id'));
		if(empty($id)){
			return json(['code'=>1,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
		}
		try {
			$data['delete_time'] = date('Y-m-d H:i:s',time());
			$update = MA::where('id',$id)->save($data);
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail'].$e->getMessage()]);
		}
	}


	// 回收站
	public function limitRecycle(){
		if(Request::isPost()){
			$param = Request::param();
			$ac = $param['ac']??'';
			$id = $param['id']??'';

			if($ac == 'list'){
				$limit = input('get.limit')??'1000';
				$where[] = ['lang','=',$this->lang];
				$lists = MA::where($where)->order('fid,px asc,id asc')->paginate($limit);
				return json(['code'=>200,'data'=>$lists->items(),'extend'=>['count' => $lists->total()]]);	
			}

		}else{

			$where[] = ['lang','=',$this->lang];
			$count = MA::onlyTrashed()->where($where)->count();
			$lists = MA::onlyTrashed()->where($where)->order('px desc,id desc')->select();
			View::assign([
				'lists' => $lists,
			]);

			return View::fetch();
		}
	}

	public function limitRecycleMake(){
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
							MA::onlyTrashed()->find($value)->restore();
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
							MA::destroy($value,true);
						}
					}
					return ['code'=>200,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['success']];
				}catch (\Exception $e){
					return ['code'=>201,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['fail'].$e->getMessage()];
				}
			}
		}
	}
}
?>