<?php  
namespace app\admin\controller;

use think\facade\View;
use think\facade\Session;
use app\common\model\ParamCo;
use app\common\model\ParamLm;
use app\common\service\SetupSyService;
use app\common\service\ProService as IS;
use app\common\service\ProCategoryService as CS;
use app\common\service\PlinfoService;
use app\common\service\PlfileService;

class ProCo extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];

	protected $sy_id = 3;
	protected $conf = [];
	protected $service = [];
	protected $categoryService = [];

	/**
	 * [initialize description]
	 * @return [type] [description]
	 */
	protected function initialize(){
		parent::initialize();
		$this->conf = SetupSyService::getConfig($this->sy_id);
		$zt = SetupSyService::getCoZt($this->conf);
		$this->service = new IS();
		$this->categoryService = new CS();
		View::assign([
			'conf' => $this->conf,
			'zt'  => $zt,
		]);
	}

	/**
	 * [recycle 回收站]
	 * @return [type]                  [description]
	 */
	public function recycle(){
		if(request()->isPost()){
			
		}else{
			$searchItem = request()->param();
			$keyword = $searchItem['keyword']??'';
			$where = [];
			if(!empty($keyword)){
				$where[] = ["title", "like", "%" . $keyword . "%"];
			}
			$lists = $this->service->getListAll($where,true);
			$category = $this->categoryService->getCategoryList();
			View::assign([
				'lists' => $lists,
				'category' => $category,
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}

	/**
	 * [recycle_make 回收站操作]
	 * @return [type]          [description]
	 */
	public function recycle_make(){
		$param = request()->param();
		return $this->recycleMake($this->service,$param);
	}

	/**
	 * [default 信息首页]
	 * @return [type]                  [description]
	 */
	public function default(){
		if(request()->isPost()){
			$data = request()->param();
			$can = '';
			$lm = $data['lm']??'';
			if(!empty($lm)){
				$can .= '&lm='.$lm.'';
			}
			$zt_val = $data['zt_val']??'';
			if(!empty($zt_val)){
				$can .= '&zt_val='.$zt_val.'';
			}
			$keyword = $data['keyword']??'';
			if(!empty($keyword)){
				$can .= '&keyword='.$keyword;
			}
			$can_str = ltrim($can,'&');
			return json(['code'=>200,'where'=>$can_str,'msg'=>lang('tip')['loading']]);

		}else{
			if(empty($this->conf)){
				return '<h1 style="text-align:center;padding-top:30px;">'.lang('tip')['configSettingsFile'].'</h1>';
				die();
			}
			$searchItem = request()->param();
			$params['where'] = $this->setWhere($searchItem);
			$params['keyword'] = $searchItem['keyword']??'';
			$lists = $this->service->getListAll($params);
			$category = $this->categoryService->getCategoryList();

			// dump($lists);

			View::assign([
				'lists' => $lists,
				'category' => $category,
				'searchItem' => $searchItem
			]);
			return View::fetch();
		}
	}

	public function getParamJson($list_lm = ''){
		$paramJson = '';
		$where[] = ['lang','=',$this->lang];
		$list_lm = trim($list_lm,',');
		$list_arr = explode(',',$list_lm);
		foreach ($list_arr as $key => $value) {
			$proCate = $this->categoryService->getCategoryById($value);
			$paramJson = $proCate['param_json'];
			if($paramJson){
				break;
			}
		}
		return $paramJson;
	}

	public function getParamList($paramJson = ''){
		$paramLists = [];
		if(!$paramJson){
			return [];
		}
		$where[] = ['lang','=',$this->lang];
		$paramArr = json_decode($paramJson,true);
		foreach ($paramArr as $key => $value) {
			$value['children'] = ParamCo::where($where)->where('lm',$value['value'])->order('ding desc,px desc,id desc')->select();
			$paramLists[] = $value;
		}
		return $paramLists;
	}

	public function getParamData(){
		$data = request()->param();
		$lm = $data['lm'] ?? '0';
		$paramJson = '';
		$paramLists = [];
		if($lm !== '0'){
			$find = $this->categoryService->getCategoryById($lm);
			$paramJson = $find['param_json'];
			if($paramJson == ''){
				$paramJson = $this->getParamJson($find['list_lm']);
			}
			$paramLists = $this->getParamList($paramJson);
		}

		return json(['code'=>200,'list'=>$paramLists]);
	}

	/**
	 * [add 添加]
	 */
	public function add(){
		if(request()->isPost()){
			$data = request()->param();
			try{
				$info = $this->service->create($data);
				$id = $info->getLastInsID();
				if(!empty($this->conf['co']['info']) && $this->conf['co']['info'] == true){
					$plinfo = $this->plInfoCreate($id,$this->sy_id);
					if(!$plinfo){
						return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
					}
				}
				if(!empty($this->conf['co']['file']) && $this->conf['co']['file'] == true){
					$plFile = $this->plFnfoCreate($id,$this->sy_id);
					if(!$plFile){
						return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
					}
				}
				Base::master_log(lang('tip')['add'].$this->conf['sy']['name'].lang('tip')['information'].'：'.$data['title']);
				return json(['code'=>200,'msg'=>lang('tip')['add'].lang('tip')['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>lang('tip')['add'].lang('tip')['fail'].$e->getMessage()]);
			}
		}else{
			$category = $this->categoryService->getCategoryList();
			View::assign([
				'category' => $category,
			]);
			return View::fetch('edit');
		}
	}

	/**
	 * [edit 编辑]
	 * @return [type]                  [description]
	 */
	public function edit(){
		$data = request()->param();
		if(request()->isPost()){
			$id = $data['id'] ?? '';
			try{
				$paramArr = $data['param'] ?? [];
				unset($data['param']);
				$data['param_json'] = json_encode($paramArr);
				$update = $this->service->update($id,$data);
				Base::master_log(lang('tip')['edit'].$this->conf['sy']['name'].lang('tip')['information'].'：'.$data['title']);
				return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail'].$e->getMessage()]);
			}
		}else{
			$id = $data['id']??'';
			if(empty($id)){
				return json(['code'=>201,'msg'=>lang('tip')['id'].lang('tip')['cannotBeEmpty']]);   
			}
			$find = $this->service->getById($id);
			$category = $this->categoryService->getCategoryList();
			View::assign([
				'category' => $category,
				'find' => $find,
			]);
			return View::fetch('edit');
		}
	}

	/**
	 * [del 删除]
	 * @return [type]          [description]
	 */
	public function del(){
		$data = request()->param();
		$id = $data['id'];
		if(empty($id)){
			return json(['code'=>201,'msg'=>lang('tip')['id'].lang('tip')['cannotBeEmpty']]);
		}
		try{
			$bol = $this->service->delete($id);
			Base::master_log(lang('tip')['del'].$this->conf['sy']['name'].lang('tip')['information'].'：'.$id);
			return json(['code'=>200,'msg'=>lang('tip')['del'].lang('tip')['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>lang('tip')['del'].lang('tip')['fail'].$e->getMessage()]);
		}
	}

	/**
	 * [make 操作]
	 * @return [type]          [description]
	 */
	public function make(){
		$params = request()->param();
		return $this->statusMake($this->service,$params);
	}

}

?>