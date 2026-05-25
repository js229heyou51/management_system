<?php  
namespace app\admin\controller;

use think\facade\Request;
use think\facade\View;
use think\facade\Session;
use app\common\model\Pl_file;
use app\common\service\PlfileService as PS;

class Plfile extends Base{

	protected $route;

	public $config = [
		'table' => 'pl_file',
		'sy_id' => '',
		'pl_id' => '',
		'sesname' => 'demo_file_id',
		'mlang' => true,
		'z_body' => false,
		'seo' => true,
		'link_url' => false,
		'img_sl' => true,
	];

	protected function initialize(){
		parent::initialize();
		$this->route = Request::controller();
		$data = Request::param();
		$this->config['pl_id'] = !empty($this->config['pl_id'])?$this->config['pl_id']:($data['pl_id']??'');
		$this->config['sy_id'] = !empty($this->config['sy_id'])?$this->config['sy_id']:($data['sy_id']??'');
		View::assign([
			'route' => $this->route,
		]);
	}

	// 相关信息首页
	public function pl_file_default(){
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
			'lists' => $lists,
			'pl_id' => $pl_id,
			'sy_id' => $sy_id,
		]);
		return View::fetch();
	}

	// 相关信息添加

	public function pl_file_add(){
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
			return View::fetch('pl_file_edit');
		}
	}

	// 相关信息编辑
	public function pl_file_edit(){
		$data = Request::param();
		$info = new PS();
		if(Request::isPost()){
			$id = $data['id'];
			try{
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
	public function pl_file_del(){
		$data = Request::param();
		$id = $data['id'];
		if(empty($id)){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
		}
		$data['delete_time'] = date('Y-m-d H:i:s',time());

		try{
			$update = Pl_info::where('id',$id)->save($data);
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail'].$e->getMessage()]);
		}
	}

	// 相关信息排序
	public function pl_file_make(){
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
					$up = Pl_info::where('id',$id)->update($data);
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
			$find = Pl_info::where('id',$id)->find();
			if($find['pass'] == 1){
				$data['pass'] = 0;
			}else{
				$data['pass'] = 1;
			}
			try{
				$update = Pl_info::where('id',$id)->save($data);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}

		if($act == 'sort'){
			if($id == ''){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$find = Pl_info::where('id',$id)->find();

			$data['px'] = $param['px'] ?? '100';
			$update = Pl_info::where('id',$id)->update($data);

			if(empty($update)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
			}
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
		}
	}
}
?>