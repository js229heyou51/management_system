<?php  
declare (strict_types = 1);

namespace app\index\controller;

use think\facade\View;
use app\common\service\HomeService;
use app\common\service\HomeCategoryService;

class Contact extends Base{

	public $nav = 'contact';
	protected $homeService;
	protected $homeCategoryService;

	public function initialize(){
		parent::initialize();
		$this->homeService = new HomeService();
		$this->homeCategoryService = new HomeCategoryService();
		$with = 'info';
		$where = ['fid'=>7];
		$list = $this->homeCategoryService->getCategoryList(['with'=>$with,'where'=>$where]);
		View::assign([
			'navList' => $list,
			'nav' => $this->nav,
		]);
	}
	public function index(){

		$data = request()->param();
		$id = $data['id']??'';
		if($id){
			$find = $this->homeService->getById($id);
		}else{
			$find = $this->homeService->getByWhere(['lm'=>8]);
		}

		View::assign([
			'find' => $find,
		]);

		return View::fetch();
	}
}