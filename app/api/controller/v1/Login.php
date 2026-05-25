<?php
declare (strict_types = 1);

namespace app\api\controller\v1;

use think\Request;

class Login extends \app\BaseController{

	public function index(){
		return json([
			'code' => 200,
			'msg' => 'ceshi'
		]);
	}
}