<?php  
declare (strict_types = 1);

namespace app\admin\controller;

use think\facade\View;
use app\common\service\SetupSyService;
use app\common\service\ArticleService as IS;
use app\common\service\ArticleCategoryService as CS;

class ArticleLm extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];

	protected $sy_id = 8;
	protected $conf = [];
	protected $service = [];
	protected $categoryService = [];
	
	/**
	 * [initialize description]
	 * @return [type] [description]
	 */
	protected function initialize() {
		parent::initialize();
		$this->conf = SetupSyService::getConfig($this->sy_id);
		$zt = SetupSyService::getLmZt($this->conf);
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
			$lists = $this->categoryService->getCategoryList(['keyword' => $keyword], [], true);
			View::assign([
				'lists' => $lists,
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}
	
	/**
	 * [recycle_make 回收站操作]
	 * @return [type]                  [description]
	 */
	public function recycle_make(){
		$param = request()->param();
		return $this->recycleMake($this->categoryService,$param);
	}
	
	/**
	 * [setconfig 系统配置]
	 * @return [type]                  [description]
	 */
	public function setconfig(){
		if(request()->isPost()){
			$conf = request()->param();
			try{
				$update = SetupSyService::update($this->sy_id,$conf);
				Base::master_log($this->langHtml['tip']['edit'].$conf['sy']['name'].$this->langHtml['tip']['system'].$this->langHtml['tip']['configFile']);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			return View::fetch();
		}
	}

	/**
	 * [default 首页]
	 * @return [type]                  [description]
	 */
	public function default(){
		if(request()->isPost()){
			$lists = $this->categoryService->getCategoryTree(0);
			$count = count($lists);
			return json(['code'=>0,'data'=>$lists,'count' => 200]);
		}else{
			if(empty($this->conf)){
				return '<h1 style="text-align:center;padding-top:30px;">'.$this->langHtml['tip']['configSettingsFile'].'</h1>';
				die();
			}
			return View::fetch();
		}
	}

	/**
	 * [add 添加]
	 */
	public function add(){
		if(request()->isPost()){
			$data = request()->param();
			try{
				$cate = $this->categoryService->createCategory($data);
				$id_lm = $cate->getLastInsID();
				if($data['fid'] == 0 ){
					$update['list_lm'] = ','.$id_lm.',';
					$update['level_lm'] = 0;
				}else{
					$list_lm = $this->categoryService->getCategoryById($data['fid']);
					$update['list_lm'] = $list_lm['list_lm'].$id_lm.',';
					$update['level_lm'] = $list_lm['level_lm'] + 1;
				}
				$this->categoryService->updateCategory($id_lm,$update,false);
				Base::master_log($this->langHtml['tip']['add'].$this->conf['sy']['name'].$this->langHtml['tip']['category'].'：'.$data['title_lm']);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$e->getMessage()]);
			}
		}else{
			$category = $this->categoryService->getCategoryList();
			return view('edit',['category' => $category,]);
		}
	}

	/**
	 * [edit 编辑]
	 * @return [type]                  [description]
	 */
	public function edit(){
		$data = request()->param();
		if(request()->isPost()){
			$id_lm = $data['id_lm'];
			if($id_lm == $data['fid']){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['cannotSet']]);
			}
			if($data['fid'] == 0 ){
				$data['list_lm'] = ','.$id_lm.',';
				$data['level_lm'] = 0;
			}else{
				$list_lm = $this->categoryService->getCategoryById($data['fid']);
				$data['list_lm'] = $list_lm['list_lm'].$id_lm.',';
				$data['level_lm'] = $list_lm['level_lm'] + 1;
			}
			try{
				$dataCo['list_lm'] = $data['list_lm'];
				$update = $this->service->updateByWhere(['lm' => $id_lm],$dataCo);
				$update = $this->categoryService->updateCategory($id_lm,$data);
				Base::master_log($this->langHtml['tip']['edit'].$this->conf['sy']['name'].$this->langHtml['tip']['category'].'：'.$data['title_lm']);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			$id_lm = $data['id_lm'];
			if(empty($id_lm)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}
			$find = $this->categoryService->getCategoryById($id_lm);
			$category = $this->categoryService->getCategoryList();
			View::assign([
				'find' => $find,
				'category' => $category,
			]);
			return View::fetch();
		}
	}

	/**
	 * [del 删除]
	 * @return [type]                  [description]
	 */
	public function del(){
		$data = request()->param();
		$id_lm = $data['id_lm'];
		if(empty($id_lm)){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
		}
		$find = $this->categoryService->getCategoryById($id_lm);
		try{
			$this->categoryService->deleteCategory($id_lm);
			Base::master_log($this->langHtml['tip']['del'].$this->conf['sy']['name'].$this->langHtml['tip']['category'].'：'.$find['title_lm']);
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail'].$e->getMessage()]);
		}
	}

	/**
	 * [make 操作]
	 * @return [type]                  [description]
	 */
	public function make(){
		$param = request()->param();
		return $this->statusLmMake($this->categoryService,$param);
	}
}

?>