<?php  
namespace app\admin\controller;

use think\facade\View;
use think\facade\Request;
use think\facade\Db;
use think\Controller;  
use think\facade\Filesystem;
use app\common\model\SetupGl as MG;
use app\common\model\SetupSy as MS;
use app\common\service\SetupSyService;
use app\common\service\SetupGlService;

class SetupGl extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];

	public $sy_id = 0;
	public $conf = '';
	public $service = [];

	public function initialize(){
		parent::initialize();
		$this->conf = SetupSyService::getConfig($this->sy_id);
		$this->service = new SetupGlService();
		View::assign([
			'conf' => $this->conf,
		]);
	}

	// 系统设置
	public function setconfig(){
		if(request()->isPost()){
			$conf = request()->param();
			foreach ($conf as $key => $value) {
				$conf[$key] = changety($value);
			}
			try{
				$update = SetupSyService::update($this->sy_id,$conf);
				Base::master_log($this->langHtml['tip']['edit'].$this->langHtml['tip']['website'].$this->langHtml['tip']['settings'].':'.$this->langHtml['tip']['configFile']);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			$list = SetupSyService::getConfig($this->sy_id);
			View::assign([
				'list' => $list,
			]);
			return View::fetch();
		}
	}

	// 编辑
	public function edit(){
		if(request()->isPost()){
			$data = request()->param();
			$data['lang'] = $this->lang;
			try {
				$find = $this->service->getById(1);

				if($find){
					$this->service->updateSetupGl($find->id,$data);
				}else{
					$this->service->createSetupGl($data);
				}
				
				Base::master_log($this->langHtml['tip']['edit'].$this->langHtml['tip']['website'].$this->langHtml['tip']['settings'].':'.$this->langHtml['tip']['website'].$this->langHtml['tip']['settings']);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			if(empty($this->conf)){
				return '<h1 style="text-align:center;padding-top:30px;">'.$this->langHtml['tip']['configSettingsFile'].'</h1>';
				die();
			}
			$setup = $this->service->getById(1);
			$setup['mapf_body'] = str_replace("\r\n",'<br />',$setup['f_body']??'');
			$galleryList = [];
			if(!empty($setup['logo'])){
				$galleryList['logoList'] = getGalleryList($setup->logo);
			}
			$iconList = [];
			if(!empty($setup['icon'])){
				$galleryList['iconList'] = getGalleryList($setup->icon);
			}
			View::assign([
				'setup' => $setup,
				'galleryList' => $galleryList,
			]);
			return View::fetch();
		}
	}

	public function crud(){
		if(request()->isPost()){
			$data = request()->param();
			$data['sy']['pre'] = strtolower($data['sy']['pre']);
			if(empty($data['sy']['id'])){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['systemID'].$this->langHtml['tip']['cannotBeEmpty']]);
			}

			if(empty($data['sy']['pre'])){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['systemPre'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$data['sy']['menu'] = changety($data['sy']['menu']);
			$data['sy']['sheet'] = changety($data['sy']['sheet']);
			$data['sy']['need_lm'] = changety($data['sy']['need_lm']);
			foreach ($data['lm'] as $key => $value) {
				$data['lm'][$key] = changety($value);
			}
			foreach ($data['co'] as $key => $value) {
				$data['co'][$key] = changety($value);
			}
			if($data['sy']['menu']){
				$res = Crud::goMenu($data);
				if(!empty($res['code']) && $res['code'] == 201){
					return json(['code'=>201,'msg'=>$res['msg']]);
				}
			}
			if($data['sy']['sheet']){
				$res = Crud::goAddDb($data,Base::$cong);
				if(!empty($res['code']) && $res['code'] == 201){
					return json(['code'=>201,'msg'=>$res['msg']]);
				}
			}
			Crud::goCrud($data);
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['success']]);
		}else{
			$lists = SetupSyService::getByWhere();
			View::assign([
				'lists' => $lists
			]);
			return View::fetch();
		}
	}
	
}
?>