<?php  
namespace app\admin\controller;

use think\facade\View;
use think\facade\Request;
use think\facade\Db;
use app\common\model\SetupSy as MS;

class SetupSy extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];
	
	protected $route;


	protected function initialize() {
		parent::initialize();
		$this->route = Request::controller();
		View::assign([
			'route' => $this->route,
		]);
	}
	public function edit(){
		$res = request();
		if(Request::isPost()){
			$sy_id = Request::param('sy_id');
			$data = Request::param();
			try {
				$update = MS::where('sy_id',$sy_id)->save($data);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			$sy_id = Request::param('sy_id');
			if(empty($sy_id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}

			$find = MS::where('sy_id',$sy_id)->find();

			View::assign([
				'find' => $find,
			]);
			return View::fetch();
		}
	}

	public function m_edit(){
		if(Request::isPost()){
			$sy_id = Request::param('sy_id');
			$data = Request::param();
			if(empty($sy_id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			try {
				$update = MS::where('sy_id',$sy_id)->save($data);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			$sy_id = Request::param('sy_id');
			if(empty($sy_id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$find = MS::where('sy_id',$sy_id)->find();

			View::assign([
				'find' => $find,
			]);
			return View::fetch();
		}
	}
}


?>