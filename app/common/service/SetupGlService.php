<?php  
declare (strict_types = 1);

namespace app\common\service;

use app\common\model\SetupGl;
use app\common\trait\CrudTrait;

class SetupGlService{


	public static function getById($id){
		$setupGl = new SetupGl();
		$find = $setupGl->getById($id);
		return $find;
	}

	public static function createSetupGl($data){
		return SetupGl::save($data);
	}

	public static function updateSetupGl($id,$data){
		return SetupGl::where('id',$id)->save($data);
	}
}

?>