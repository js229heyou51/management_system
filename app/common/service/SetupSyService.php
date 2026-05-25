<?php  
declare (strict_types = 1);

namespace app\common\service;

use think\facade\Lang;
use app\common\model\SetupSy;

class SetupSyService{

	public static function getConfig($sy_id = 0): array{
		$setupSy = new SetupSy();
		return $setupSy->getConfig($sy_id);
	}

	public static function getBySyId($id){
		$setupSy = new SetupSy();
		return $setupSy->getBySyId($id);
	}

	public static function getByWhere(?array $where = []){
		$setupSy = new SetupSy();
		return $setupSy->getByWhere($where);
	}

	public static function update($sy_id,$conf){
		if(isset($conf['sy']['need_lm'])){
			$conf['sy']['need_lm'] = changety($conf['sy']['need_lm']);
		}
		if(!empty($conf['lm'])){
			foreach ($conf['lm'] as $key => $value) {
				$conf['lm'][$key] = changety($value);
			}	
		}
		if(!empty($conf['co'])){
			foreach ($conf['co'] as $key => $value) {
				$conf['co'][$key] = changety($value);
			}	
		}
		if(!empty($conf['plinfo'])){
			foreach ($conf['plinfo'] as $key => $value) {
				$conf['plinfo'][$key] = changety($value);
			}	
		}
		$data['title'] = $conf['sy']['name']??'';
		$data['sy_id'] = $sy_id;
		$data['pre'] = $conf['sy']['pre']??'';
		$data['lang'] = Lang::getLangSet();
		$data['config'] = serialize($conf);
		$find = SetupSyService::getBySyId($sy_id);
		if(!empty($find)){
			return SetupSy::where('sy_id',$sy_id)->where('lang',Lang::getLangSet())->save($data);
		}else{
			return SetupSy::insert($data);
		}
	}

	public static function getCoZt($conf = []): bool{
		if(!empty($conf)){
			if((isset($conf['co']['tuijian']) && $conf['co']['tuijian'] == true) ||
				(isset($conf['co']['hot']) && $conf['co']['hot'] == true) ||
				(isset($conf['co']['pass']) && $conf['co']['pass'] == true) ){
				return true;
			}else{
				return false;
			}
		}
		return false;
	}



	public static function getLmZt($conf = []): bool{
		if(!empty($conf)){
			if((isset($conf['lm']['tuijian_lm']) && $conf['lm']['tuijian_lm'] == true) ||
				(isset($conf['lm']['hot_lm']) && $conf['lm']['hot_lm'] == true) ||
				(isset($conf['lm']['pass_lm']) && $conf['lm']['pass_lm'] == true) ){
				return true;
			}else{
				return false;
			}
		}
		return false;
	}
}

?>