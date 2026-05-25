<?php  
namespace app\common\service;

use think\facade\Config;
use think\facade\Lang;
use think\facade\Session;
use think\exception\ValidateException;
use app\common\model\Pl_info as INFO;
use app\admin\validate\Plinfo as IV;


class PlinfoService{

	public static function getSesname(){
		return new INFO()->sesname;
	}

	public static function create($data){
		$pl_id = $data['pl_id'] ?? '';

		// 验证数据
		$validate = new IV;
		if (!$validate->check($data)) {
			throw new ValidateException($validate->getError());
		}
		$info = new INFO();
		if(empty($pl_id)){
			if(empty($pr_id)){
				$pr_id = date('His').rand(10,99);
				Session::set($info->sesname, $pr_id);
			}
		}else{
			$pr_id = $pl_id;
		}
		$data['pl_id'] = $pr_id;
		$data['pass'] = 1;
		$data['wtime'] = $data['wtime'] ?? date('Y-m-d H:i:s');
		$data['lang'] = Lang::getLangSet();
		$info->save($data);
		return $info;
	}

	/**
	 * 更新信息
	 */
	public static function update($id ,?array $data = [] ,$force = true){
		$info = INFO::find($id);
		if (!$info) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		if($force){
			// 验证数据
			$validate = new IV;
			if (!$validate->check($data)) {
				throw new ValidateException($validate->getError());
			}
		}
		$info->save($data);
		return $info;
	}

	/**
	 * 更新信息
	 */
	public static function updateByPlId($pl_id ,?array $data = []){
		$info = INFO::where('pl_id',$pl_id)->find();
		if (!$info) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		$info->save($data);
		return $info;
	}


	/**
	 * 获取列表
	 */
	public static function getList(?array $params = [], int $page = 1, int $limit = 18){
		$query = INFO::where('pass', '1');

		if(!empty($params['where'])){
			$query->where($params['where']);
		}

		if (!empty($params['keyword'])) {
			$query->whereLike('title|z_body', '%' . $params['keyword'] . '%');
		}
		// 排序
		$orderBy = $params['order'] ?? 'wtime desc';
		$query->order($orderBy);

		return $query->paginate([
			'list_rows' => $limit,
			'page' => $page
		]);
	}
	/**
	 * 获取全部
	 */
	public static function getAllList(?array $params = []){
		$query = INFO::where('pass', '1');

		if(!empty($params['where'])){
			$query->where($params['where']);
		}

		if (!empty($params['keyword'])) {
			$query->whereLike('title|z_body', '%' . $params['keyword'] . '%');
		}
		// 排序
		$orderBy = $params['order'] ?? 'wtime desc';
		$query->order($orderBy);

		return $query->select();
	}

	/**
	 * 根据id获取信息
	 */
	public static function getById($id){
		$find = INFO::where('pass', INFO::STATUS_ACTIVE)->find($id);
		if(!empty($find['img_sl'])){
			$find->imgList = getGalleryList($find['img_sl']);
		}
		return $find;
	}


	/**
	 * 删除信息
	 */
	public static function delete($id, $force = false){
		$info = INFO::find($id);

		if (!$info) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		if ($force) {
			// 物理删除
			$info->delete(true);
		} else {
			// 软删除
			$info->delete();
		}
		return true;
	}
}