<?php  
declare (strict_types = 1);

namespace app\common\trait;

use think\Model;
use think\Paginator;
use think\facade\Lang;
use think\facade\Config;
use think\facade\Validate;
use think\exception\ValidateException;
use app\admin\validate\Category as CategoryValidate;

trait CrudCategoryTrait{

	use CrudTrait;

	/**
	 * @var Model
	 */
	protected $categoryModel;

	/**
	 * 创建类别
	 */
	public function createCategory(?array $data = []){
		// 验证数据
		$validate = new CategoryValidate();
		if (!$validate->check($data)) {
			throw new ValidateException($validate->getError());
		}

		if(empty($data['add_xx'])){
			$data['add_xx'] = 'yes';
		}
		if(empty($data['add_xia'])){
			$data['add_xia'] = 'yes';
		}
		if(empty($data['con_att'])){
			$data['con_att'] = 1;
		}
		$data['pass'] = 1;
		$data['hot'] = 0;
		$data['tuijian'] = 0;
		$data['wtime'] = $data['wtime'] ?? date('Y-m-d H:i:s');
		$data['ip'] = request()->ip();
		$data['lang'] = Lang::getLangSet();
		if($data['fid'] != 0){
			$list_lm = $this->getCategoryById($data['fid']);
			if(empty($list_lm)){
				throw new ValidateException($this->langHtml['tip']['NotExist']);
			}
		}
		$this->categoryModel->save($data);
		return $this->categoryModel;
	}

	/**
	 * 更新类别
	 */
	public function updateCategory($id,?array $data = [],$force = true){
		$cate = $this->categoryModel->find($id);
		if (!$cate) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		if($force){
			// 验证数据
			$validate = new CategoryValidate();
			if (!$validate->check($data)) {
				throw new ValidateException($validate->getError());
			}
		}
		$cate->save($data);
		return $cate;
	}

	/**
	 * 更新类别
	 */
	public function updateCategoryByWhere(?array $where = [],?array $data = [],$force = true){
		if (!$where) {
			throw new \Exception(Lang::get('tip')['parameterError']);
		}
		$cate = $this->categoryModel->where($where);
		if (!$cate) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		$cate->save($data);
		return $cate;
	}

	/**
	 * 删除信息
	 */
	public function deleteCategory($id, $force = false){
		$cate = $this->categoryModel->find($id);
		if (!$cate) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		if ($force) {
			// 物理删除
			$cate->delete(true);
		} else {
			// 软删除
			$cate->delete();
		}
		return true;
	}
	/**
	 * 根据id获取类别
	 */
	public function getCategoryById($id){
		$find = $this->categoryModel->find($id);
		if($find['img_sl_lm']){
			$find->imgList = getGalleryList($find['img_sl_lm']);
		}
		return $find;
	}

	public function getCategoryFind(?array $params = []){
		$query = $this->categoryModel->where('lang', Lang::getLangSet());
		if(!empty($params['where'])){
			$query->where($params['where']);
		}
		if (!empty($params['keyword'])) {
			$query->whereLike('title_lm|z_body_lm', '%' . $params['keyword'] . '%');
		}

		if(!empty($params['with'])){
			$query->with($params['with']);
		}
		$find = $query->limit(1)->find();
		
		if($find['img_sl_lm']){
			$find->gallery_list = getGalleryList($find['img_sl_lm']);
		}
		return $find;
	}

	public function getCategoryTree($parentId = 0, $withCount = false){
		$categories = $this->categoryModel->getTree($parentId);
		if ($withCount) {
			self::attachCategoryCount($categories);
		}
		return $categories;
	}

	/**
	 * 获取全部列表
	 */
	public function getCategoryList(?array $params = [],?array $withCount = [], $isTrashed = false){
		$query = $this->categoryModel->where('lang', Lang::getLangSet());
		if($isTrashed){
			$query->onlyTrashed();
		}
		if(!empty($params['where'])){
			$query->where($params['where']);
		}
		if (!empty($params['keyword'])) {
			$query->whereLike('title_lm|z_body_lm', '%' . $params['keyword'] . '%');
		}

		if(!empty($params['with'])){
			$query->with([$params['with'] => function($query){
				$query->where('lang', Lang::getLangSet());
			}]);
		}
		if(!empty($withCount)){
			$query->withCount($withCount);
		}
		// 排序
		$orderBy = $params['order'] ?? 'px desc';
		$query->order($orderBy);

		if(!empty($params['limit'])){
			$query->limit($params['limit']);
		}

		$result = $query->select();

		$lists = $this->loadGalleries($result);
		
		return $lists;
	}

	/**
	 * 为分类树附加数量统计
	 */
	private function attachCategoryCount(&$categories)
	{
		foreach ($categories as &$category) {
			// 获取分类及其所有子分类的ID
			$categoryIds = $this->categoryModel->getAllChildrenIds($category->id);
			
			// 统计图片数量
			$count = $this->service->whereIn('lm', $categoryIds)->count();
			$category->count = $count;
			// 递归处理子分类
			if (!empty($category->children)) {
				self::attachCount($category->children);
			}
		}
	}

	/**
	 * 恢复数据
	 */
	public function restore($id){
		$model = $this->categoryModel->onlyTrashed()->find($id);
		if (!$model) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		return $model->restore();;
	}

	/**
	 * 彻底删除
	 */
	public function destroy($id){
		$model = $this->categoryModel->onlyTrashed()->find($id);
		if (!$model) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		return $model->destroy($id,true);;
	}
}