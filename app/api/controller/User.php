<?php
declare (strict_types = 1);

namespace app\api\controller;

use think\Request;
use think\facade\Lang;
use think\facade\Cookie;
use think\facade\Session;
use app\common\service\MasterService;

class User extends \app\BaseController{
	protected $masterService;

	protected function initialize(){
		$this->masterService = new MasterService();
	}

	public function index(){
		$param = request()->param();

		if(empty($param['id'])){
			return json(['code' => 201,'msg' => lang('tip')['parameterError']]);
		}

		$user = $this->masterService->getById($param['id'],['field' => 'id,username,menu_list,rename,action_list']);

		return json(['code'=>200,'data' => $user]);
	}
}