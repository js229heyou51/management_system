<?php  
namespace app\admin\controller;

use think\facade\Request;
use think\facade\View;
use think\facade\Session;
use app\common\model\Pl_info;
use app\common\service\PlinfoService as PS;

class Plinfo extends Base{

	protected $route;

	public $config = [
		'table' => 'pl_info',
		'sy_id' => '',
		'pl_id' => '',
		'sesname' => 'demo_info_id',
		'price' => false,
		'link_url' => false,
		'img_sl' => false,
		'z_body' => true,
	];

	protected function initialize(){
		parent::initialize();
		$this->route = Request::controller();
		$data = Request::param();
		$this->config['pl_id'] = !empty($this->config['pl_id'])?$this->config['pl_id']:($data['pl_id']??'');
		$this->config['sy_id'] = !empty($this->config['sy_id'])?$this->config['sy_id']:($data['sy_id']??'');
		$this->config['price'] = $data['price'] ?? $this->config['price'] ?? false;
		$this->config['link_url'] = $data['link_url'] ?? $this->config['link_url'] ?? false;
		$this->config['img_sl'] = $data['img_sl'] ?? $this->config['img_sl'] ?? false;
		$this->config['z_body'] = $data['z_body'] ?? $this->config['z_body'] ?? true;
		View::assign([
			'route' => $this->route,
		]);
	}

	// 相关信息首页
	public function pl_info_default(){
		$data = Request::param();
		$pl_id = $data['pl_id']??'';
		$sy_id = $data['sy_id']??'';
		$info = new PS();
		$pr_id = Session::get($info->getSesname());
		$where[] = ['lang','=',$this->lang];
		$lists = [];
		if($pl_id == ''){
			if(!empty($pr_id)){
				$where[] = ['pl_id','=',$pr_id];
				$lists = $info->getAllList(['where'=>$where]);
			}
		}else{
			$where[] = ['pl_id','=',$pl_id];
			$lists = $info->getAllList(['where'=>$where]);
		}
		View::assign([
			'conf' => $this->config,
			'info' => $lists,
			'pl_id' => $pl_id,
			'sy_id' => $sy_id,
		]);
		return View::fetch();
	}

	// 相关信息添加

	public function pl_info_add(){
		$data = Request::param();
		if(Request::isPost()){
			$plinfo = new PS();
			try{
				$insert = $plinfo->create($data);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			$pl_id = $data['pl_id'] ?? '';
			View::assign([
				'conf' => $this->config,
				'pl_id' => $pl_id,
			]);
			return View::fetch('pl_info_edit');
		}
	}

	// 相关信息编辑
	public function pl_info_edit(){
		$data = Request::param();
		if(Request::isPost()){
			$id = $data['id'];
			try{
				$info = new PS();
				$update = $info->update($id,$data);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			$id = $data['id'];
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$info = new PS();
			$find = $info->getById($id);

			View::assign([
				'conf' => $this->config,
				'find' => $find,
				'pl_id' => $find->pl_id,
			]);
			return View::fetch();
		}
	}
	// 相关信息删除
	public function pl_info_del(){
		$data = Request::param();
		$id = $data['id'];
		if(empty($id)){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
		}

		try{
			$update = PS::delete($id);
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail'].$e->getMessage()]);
		}
	}

	// 相关信息排序
	public function pl_info_make(){
		$param = Request::param();
		$act = $param['act'];
		$id = $param['id'];
		if($act == 'px'){
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$px = $param['px'];
			foreach ($id as $key => $value){
				$id = $key;
				$data['px'] = $px[$key];
				try{
					$up = PS::update($id,$data);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
				}
			}
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
		}
		if($act == 'pass'){
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$find = PS::getById($id);
			if($find['pass'] == 1){
				$data['pass'] = 0;
			}else{
				$data['pass'] = 1;
			}
			try{
				$update = PS::update($id,$data);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}

		if($act == 'sort'){
			if($id == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$find = PS::getById($id);

			$data['px'] = $param['px'] ?? '100';
			$update = PS::update($id,$data,false);

			if(empty($update)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
			}
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
		}
	}
}
?>