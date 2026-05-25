<?php  
declare (strict_types = 1);
namespace app\common\trait;

use think\Model;
use think\Paginator;
use think\facade\Db;
use think\facade\Lang;
use think\facade\Validate;
use think\exception\ValidateException;
use app\admin\validate\Info as InfoValidate;
use app\common\model\Gallery;
use app\common\model\Video;

trait CrudTrait{

	// use CrudCategoryTrait;
	/**
	 * @var Model
	 */
	protected $model;

	/**
	 * [getById 根据id获取信息]
	 * @param  [type] $id [ID]
	 * @param  array  $params [参数]
	 * @return [type]     [description]
	 */
	public function getById($id,?array $params = []): ?Model{
		$query = $this->model->where('lang', Lang::getLangSet());
		if(!empty($params['field'])){
			$query->field($params['field']);
		}
		if(!empty($params['with'])){
			$query->with($params['with']);
		}
		$find = $query->find($id);
		if(!empty($find->img_sl)){
			$find->gallery_list = getGalleryList($find->img_sl);
		}

		if(!empty($find->vid_sl)){
			$find->video_list = getVideoList($find->vid_sl);
		}
		return $find;
	}

	/**
	 * [getByWhere 根据where条件获取一条信息]
	 * @param  array  $where [条件]
	 * @return [type]        [description]
	 */
	public function getByWhere(?array $where = [],?array $params = []): ?Model{
		$query = $this->model->where('lang', Lang::getLangSet());
		if(!empty($where)){
			$query->where($where);
		}
		if(!empty($params['with'])){
			$query->with($params['with']);
		}
		// 排序
		$orderBy = 'wtime desc,id desc';
		$query->order($orderBy);
		$query->limit(1);
		$find = $query->find();
		if(!empty($find->img_sl)){
			$find->gallery_list = getGalleryList($find->img_sl);
		}
		if(!empty($find->vid_sl)){
			$find->video_list = getVideoList($find->vid_sl);
		}
		return $find;
	}

	/**
	 * [create 添加信息]
	 * @param  array        $data  [需要保存的数据]
	 * @param  bool|boolean $force [是否需要验证数据]
	 * @return [type]              [description]
	 */
	public function create(?array $data = [],?bool $force = true): Model{
		if($force){
			// 验证数据
			$validate = new InfoValidate();

			if ($data['lm'] === 0) {
				$validate->remove('lm', 'require');
			}
			if (!$validate->check($data)) {
				throw new ValidateException($validate->getError());
			}
		}

		$data['wtime'] = $data['wtime'] ?? date('Y-m-d H:i:s');
		$data['ip'] = request()->ip();
		$data['lang'] = Lang::getLangSet();
		$data['ding'] = 0;
		$data['hot'] = 0;
		$data['tuijian'] = 0;
		$data['pass'] = 1;
		if(!empty($data['lm'])){
			$list_lm = $this->categoryService->getCategoryById($data['lm']);
			$data['list_lm'] = $list_lm['list_lm'];
			// 可不要
			if(empty($data['ym_key'])){
				$data['ym_key'] = $list_lm['ym_key'];
			}
		}
		$this->model->save($data);
		return $this->model;
	}

	/**
	 * 批量插入数据（支持大数据量）
	 * @param array $dataList 需要批量保存的数据数组
	 * @param bool $force 是否需要验证数据
	 * @param int $batchSize 每批次插入的数量
	 * @return int 返回插入的总行数
	 * @throws ValidateException
	 * @throws \Exception
	 */
	public function batchCreate(array $dataList, ?bool $force = true, int $batchSize = 100): int
	{
		if (empty($dataList)) {
			throw new ValidateException('没有要插入的数据');
		}
		$totalCount = 0;
		// 开启事务
		Db::startTrans();
		try {
			// 分批处理
			$chunks = array_chunk($dataList, $batchSize);
			foreach ($chunks as $chunkIndex => $chunk) {
				$insertData = [];
				foreach ($chunk as $index => $data) {
					// 验证数据
					if ($force) {
						$validate = new InfoValidate();
						if (isset($data['lm']) && $data['lm'] === 0) {
							$validate->remove('lm', 'require');
						}
						if (!$validate->check($data)) {
							$globalIndex = $chunkIndex * $batchSize + $index + 1;
							throw new ValidateException("第 {$globalIndex} 条数据验证失败：" . $validate->getError());
						}
					}
					// 处理公共字段
					$data['wtime'] = $data['wtime'] ?? date('Y-m-d H:i:s');
					$data['ip'] = request()->ip();
					$data['lang'] = Lang::getLangSet();
					$data['ding'] = $data['ding'] ?? 0;
					$data['hot'] = $data['hot'] ?? 0;
					$data['tuijian'] = $data['tuijian'] ?? 0;
					$data['pass'] = $data['pass'] ?? 1;
					
					// 处理分类关联信息
					if (!empty($data['lm'])) {
						$list_lm = $this->categoryService->getCategoryById($data['lm']);
						$data['list_lm'] = $list_lm['list_lm'];
						
						if (empty($data['ym_key'])) {
							$data['ym_key'] = $list_lm['ym_key'];
						}
					}
					$insertData[] = $data;
				}
				
				// 执行批量插入
				if (!empty($insertData)) {
					$count = $this->model->insertAll($insertData);
					$totalCount += $count;
				}
			}
			// 提交事务
			Db::commit();
			return $totalCount;
		} catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
			throw $e;
		}
	}

	/**
	 * [update 根据id更新信息]
	 * @param  [type]  $id    [ID]
	 * @param  array   $data  [需要更新的数据]
	 * @param  boolean $force [是否需要验证数据]
	 * @return [type]         [description]
	 */
	public function update($id ,?array $data = [], $force = true): Model{
		$model = $this->model->find($id);
		if (!$model) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		if($force){
			// 验证数据
			$validate = new InfoValidate();
			if (!$validate->check($data)) {
				throw new ValidateException($validate->getError());
			}
		}
		$model->save($data);
		return $model;
	}

	/**
	 * [batchUpdate 根据id批量更新信息]
	 * @param  array  $data        [description]
	 * @param  string $pk          [description]
	 * @param  array  $allowFields [description]
	 * @return [type]              [description]
	 */
	public function batchUpdate(array $data, string $pk = 'id', array $allowFields = []){
		if (empty($data)) {
			return 0;
		}
		// 验证数据必须包含主键
		foreach ($data as $item) {
			if (!isset($item[$pk])) {
				throw new Exception("批量更新数据必须包含主键字段 '{$pk}'");
			}
		}
		// 提取所有主键值
		$ids = array_column($data, $pk);
		if (empty($ids)) {
			return 0;
		}
		// 获取所有待更新的字段（除去主键）
		$fields = [];
		foreach ($data as $item) {
			$fields = array_merge($fields, array_keys($item));
		}
		$fields = array_unique($fields);
		// 移除主键字段
		$fields = array_diff($fields, [$pk]);
		// 字段白名单过滤
		if (!empty($allowFields)) {
			$fields = array_intersect($fields, $allowFields);
			if (empty($fields)) {
				throw new Exception('没有允许更新的字段');
			}
		}
		// 获取表名和数据库连接
		$tableName = $this->model->getTable();
		$connection = $this->model->db()->getConnection();

		// 构建 SQL 语句，使用 ? 占位符
		$sql = "UPDATE `{$tableName}` SET ";
		$bind = []; // 按顺序存储绑定值

		foreach ($fields as $field) {
			$sql .= "`{$field}` = CASE `{$pk}` ";
			foreach ($data as $item) {
				if (!isset($item[$field])) {
					continue; // 当前记录没有该字段，跳过
				}
				$sql .= "WHEN ? THEN ? ";
				$bind[] = $item[$pk];   // 第一个 ? 绑定主键值
				$bind[] = $item[$field]; // 第二个 ? 绑定字段值
			}
			$sql .= "ELSE `{$field}` END, ";
		}

		// 去掉末尾的逗号和空格
		$sql = rtrim($sql, ', ');

		// 添加 WHERE 条件
		$placeholders = implode(',', array_fill(0, count($ids), '?'));
		$sql .= " WHERE `{$pk}` IN ({$placeholders})";
		// 将主键值追加到绑定数组末尾
		$bind = array_merge($bind, $ids);
		// 执行更新
		try {
			return $connection->execute($sql, $bind);
		} catch (DbException $e) {
			throw new DbException('批量更新失败：' . $e->getMessage());
		}
	}

	/**
	 * [updateByWhere 根据条件更新信息]
	 * @param  array  $where [description]
	 * @param  array  $data  [description]
	 * @return [type]        [description]
	 */
	public function updateByWhere(?array $where = [],?array $data = []){
		if (!$where) {
			throw new \Exception(Lang::get('tip')['parameterError']);
		}
		$model = $this->model->where($where);

		if (!$model) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		$model->save($data);
		return $model;
	}


	/**
	 * [delete 删除信息]
	 * @param  [type]  $id    [description]
	 * @param  boolean $force [description]
	 * @return [type]         [description]
	 */
	public function delete($id, $force = false): bool{
		$model = $this->model->find($id);

		if (!$model) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		if ($force) {
			// 物理删除
			$model->delete(true);
		} else {
			// 软删除
			$model->delete();
		}
		return true;
	}

	/**
	 * [getList 分页获取列表]
	 * @param  array       $params [description]
	 * @param  int|integer $page   [description]
	 * @param  int|integer $limit  [description]
	 * @return [type]              [description]
	 */
	public function getList(?array $params = [], int $page = 1, int $limit = 18): Paginator{
		$query = $this->model->where('lang', Lang::getLangSet());
		if(!empty($params['where'])){
			$query->where($params['where']);
		}
		if(!empty($params['with'])){
			$query->with($params['with']);
		}

		if (!empty($params['keyword'])) {
			$query->whereLike('title|z_body', '%' . $params['keyword'] . '%');
		}
		// 排序
		$orderBy = $params['order'] ?? 'px desc,wtime desc,id desc';
		$query->order($orderBy);

		$lists = $query->paginate([
			'list_rows' => $limit,
			'page' => $page
		]);
		// 批量加载图片
		$lists->setCollection(
			$this->loadGalleries($lists->getCollection())
		);
		// 批量加载视频
		$lists->setCollection(
			$this->loadVideos($lists->getCollection())
		);

		return $lists;
	}

	/**
	 * [getListAll 获取全部列表]
	 * @param  array        $params    [description]
	 * @param  bool|boolean $isTrashed [description]
	 * @param  array        $with      [description]
	 * @param  array        $withCount [description]
	 * @return [type]                  [description]
	 */
	public function getListAll(?array $params = [],?bool $isTrashed = false, array $with = ['profile'], array $withCount = []){
		$query = $this->model->where('lang', Lang::getLangSet());
		if($isTrashed){
			$query->onlyTrashed();
		}
		if($with){
			$query->with($with);
		}
		if($withCount){
			$query->withCount($withCount);
		}
		if(!empty($params['field'])){
			$query->field($params['field']);
		}
		if(!empty($params['where'])){
			$query->where($params['where']);
		}
		if (!empty($params['keyword'])) {
			$query->whereLike('title|z_body', '%' . $params['keyword'] . '%');
		}
		// 排序
		$orderBy = $params['order'] ?? 'px desc,wtime desc,id desc';
		$query->order($orderBy);
		if(!empty($params['limit'])){
			$query->limit($params['limit']);
		}
		// $count = $query->count();
		$result = $query->select();
		// 批量加载图片
		$lists = $this->loadGalleries($result);
		// 批量加载视频
		$lists = $this->loadVideos($lists);
		return $lists;
	}

	/**
	 * [restore 恢复数据]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function restore($id){
		$model = $this->model->onlyTrashed()->find($id);
		if (!$model) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		return $model->restore();;
	}

	/**
	 * [destroy 彻底删除]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function destroy($id){
		$model = $this->model->onlyTrashed()->find($id);
		if (!$model) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		return $model->destroy($id,true);;
	}

	/**
	 * [loadGalleries 获取图片]
	 * @param  [type] $objects [description]
	 * @return [type]          [description]
	 */
	public function loadGalleries($objects){

		if ($objects->isEmpty()) {
			return $objects;
		}
		// 1. 收集所有产品中的图片ID（去重）
		$allGalleryIds = [];
		foreach ($objects as $object) {
			$ids = ($object->img_sl ? : []) ?  : ($object->img_sl_lm? : []);
			$allGalleryIds = array_merge($allGalleryIds, $ids);
		}
		$allGalleryIds = array_unique($allGalleryIds);
		if (empty($allGalleryIds)) {
			// 没有图片，直接给每个产品设置空数组
			foreach ($objects as $object) {
				$object->gallery_list = [];
			}
			return $objects;
		}
		// 2. 批量查询所有图片，并按ID索引
		$galleries = Gallery::where('status', 1)  // 假设只查询正常状态的图片
			->whereIn('id', $allGalleryIds)
			->select()
			->column(null, 'id'); // 以ID为键的数组

		// 3. 为每个产品按原顺序组装图片列表
		foreach ($objects as $object) {
			$galleryIds = ($object->img_sl ?: []) ?  : ($object->img_sl_lm? : []);
			$objectGalleries = [];
			foreach ($galleryIds as $id) {
				if (isset($galleries[$id])) {
					$objectGalleries[] = $galleries[$id];
				}
			}
			// 动态添加属性 gallery_list
			$object->gallery_list = $objectGalleries;
		}
		
		return $objects;
	}

	/**
	 * [loadVideos 获取视频]
	 * @param  [type] $objects [description]
	 * @return [type]          [description]
	 */
	public function loadVideos($objects){

		if ($objects->isEmpty()) {
			return $objects;
		}
		// 1. 收集所有产品中的视频ID（去重）
		$allVideoIds = [];
		foreach ($objects as $object) {
			$ids = $object->vid_sl ? : [];
			$allVideoIds = array_merge($allVideoIds, $ids);
		}
		$allVideoIds = array_unique($allVideoIds);
		if (empty($allVideoIds)) {
			// 没有视频，直接给每个产品设置空数组
			foreach ($objects as $object) {
				$object->video_list = [];
			}
			return $objects;
		}
		// 2. 批量查询所有视频，并按ID索引
		$videos = Video::where('status', 1)  // 假设只查询正常状态的视频
			->whereIn('id', $allVideoIds)
			->select()
			->column(null, 'id'); // 以ID为键的数组

		// 3. 为每个产品按原顺序组装视频列表
		foreach ($objects as $object) {
			$videoIds = $object->vid_sl ?: [];
			$objectVideos = [];
			foreach ($videoIds as $id) {
				if (isset($videos[$id])) {
					$objectVideos[] = $videos[$id];
				}
			}
			// 动态添加属性 video_list
			$object->video_list = $objectVideos;
		}
		
		return $objects;
	}

	/**
	 * [count 获取数量]
	 * @param  array  $where [查询条件]
	 * @return [type]        [description]
	 */
	public function count(?array $where = []){
		$query = $this->model->where('lang', Lang::getLangSet());
		if (!empty($where)) {
			$query->where($where);
		}
		return $query->count();
	}
}
?>