<?php
declare (strict_types = 1);

namespace app\api\controller;

use think\Request;
use think\facade\Lang;
use think\facade\Cookie;
use think\facade\Session;
use app\common\service\MasterMenuService;

class Menu extends \app\BaseController{
	protected $service;

	protected function initialize(){
		$this->service = new MasterMenuService();
	}

	public function index(){

		$param = request()->param();

		$where['order'] = 'fid asc,px asc,id asc';
		if(!empty($param)){
			$where['where'] =  [['id','in',implode(',', $param)]]; 
		}

		$menuList = $this->service->getListAll($where,false,[]);

		$menuId = [];
		$menuTree = [];
		$children = [];
		foreach ($menuList as $key => $value) {
			$menuId[] = ''.$value['id'];
			if($value['fid'] == 0){
				if(isset($menuTree[$value['id']])){
					$menuTree[$value['id']] = array_merge($value,$menuTree[$value['id']]);
				}else{
					$menuTree[$value['id']] = $value->toArray();
				}
			}else{
				$menuTree[$value['fid']]['children'][] = $value->toArray();
			}
		}
		return json([
			'code' => 200,
			'menuList' => $menuTree,
			'menuId' => $menuId
		]);
	}
}