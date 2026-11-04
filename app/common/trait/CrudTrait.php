<?php  
declare (strict_types = 1);
namespace app\common\trait;

use think\Model;
use think\Paginator;
use think\facade\Db;
use think\facade\Log;
use think\facade\Lang;
use think\exception\ValidateException;
use app\admin\validate\Info as InfoValidate;

trait CrudTrait{
	use CrudCategoryTrait;
	/**
	 * @var Model
	 */
	protected $model;

	/**
	 * 构建基础查询（count / getList 共用）
	 */
	private function buildQuery(array $params = [])
	{
		// dump($params);
		$query = $this->model->where('lang', Lang::getLangSet());
		// 软删除
		if (!empty($params['isTrashed'])) {
			$query->onlyTrashed();
		}
		// 字段
		if (!empty($params['field'])) {
			$allowFields = ['id', 'lm', 'title', 'z_body', 'wtime', 'px', 'pass']; // 按实际需要改
			$fields = array_intersect(
				array_map('trim', explode(',', (string)$params['field'])),
				$allowFields
			);
			if ($fields) {
				$query->field($fields);
			}
		}
		// 精确条件
		if (!empty($params['where']) && is_array($params['where'])) {
			$query->where($params['where']);
		}

		// 关键词
		if (!empty($params['keyword'])) {
			$query->whereLike('title|z_body', '%' . $params['keyword'] . '%');
		}

		// 时间条件：起止时间优先，其次 timeType
		$startTime = $params['startTime'] ?? null;
		$endTime   = $params['endTime'] ?? null;
		$timeType  = $params['timeType'] ?? null;

		if (!empty($startTime) && !empty($endTime)) {
			$query->whereTime('wtime', 'between', [$startTime, $endTime]);
		} elseif (!empty($startTime)) {
			$query->whereTime('wtime', '>=', $startTime);
		} elseif (!empty($endTime)) {
			$query->whereTime('wtime', '<=', $endTime);
		} elseif (!empty($timeType)) {
			$query->whereTime('wtime', $timeType);
		}

		return $query;
	}
	/**
	 * 排序白名单
	 */
	private function normalizeOrder(string $order): string
	{
		$allowFields = ['ding', 'px', 'wtime', 'id'];
		$parts = [];
		foreach (explode(',', $order) as $item) {
			$item = trim($item);
			if (!preg_match('/^([a-zA-Z0-9_]+)\s+(asc|desc)$/i', $item, $m)) {
				continue;
			}
			if (!in_array($m[1], $allowFields, true)) {
				continue;
			}
			$parts[] = $m[1] . ' ' . strtolower($m[2]);
		}
		return $parts ? implode(',', $parts) : 'px desc,wtime desc,id desc';
	}

	/**
	 * [getById 根据id获取信息]
	 * @param  int $id [ID]
	 * @param  array  $params [参数]
	 * @return Model     [description]
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
		try{
			if(!empty($find->img_sl)){
				$find->gallery_list = getGalleryList($find->img_sl);
			}
		}catch(\Exception $e){
			$find->gallery_list = [];
		}
		try{
			if(!empty($find->vid_sl)){
				$find->video_list = getVideoList($find->vid_sl);
			}
		}catch(\Exception $e){
			$find->video_list = [];
		}
		return $find;
	}

	/**
	 * [getByWhere 根据where条件获取一条信息]
	 * @param  array  $params [条件]
	 * @return Model        [description]
	 */
	public function getByWhere(?array $params = []): ?Model{

		$query = $this->buildQuery($params);

		// 排序
		$orderBy = 'wtime desc,id desc';
		$query->order($orderBy);
		$query->limit(1);
		$find = $query->find();
		try{
			if(!empty($find->img_sl)){
				$find->gallery_list = getGalleryList($find->img_sl);
			}
		}catch(\Exception $e){
			$find->gallery_list = [];
		}
		try{
			if(!empty($find->vid_sl)){
				$find->video_list = getVideoList($find->vid_sl);
			}
		}catch(\Exception $e){
			$find->video_list = [];
		}
		return $find;
	}

	/**
	 * [create 添加信息]
	 * @param  array        $data  [需要保存的数据]
	 * @param  bool|boolean $force [是否需要验证数据]
	 * @return Model              [description]
	 */
	public function create(?array $data = [],?bool $force = true): Model{
		$lm = $data['lm'] ?? null;
		if($force){
			// 验证数据
			$validate = new InfoValidate();

			if ($lm === 0 || $lm === '0' || $lm === '' || $lm === null) {
				$validate->remove('lm', 'require');
			}
			if (!$validate->check($data)) {
				throw new ValidateException($validate->getError());
			}
		}
		// 系统字段候选（无论表里有没有，先列出来）
	    $systemFields = [
	        'wtime'   => date('Y-m-d H:i:s'),
	        'ip'      => request()->ip() ?: '0.0.0.0',
	        'lang'    => Lang::getLangSet() ?: 'zh-cn',
	        'ding'    => 0,
	        'hot'     => 0,
	        'tuijian' => 0,
	        'pass'    => 1,
	    ];

	    // 只保留表里真实存在的字段
	    $tableFields = $this->model->getTableFields();
	    foreach ($systemFields as $field => $value) {
	        if (in_array($field, $tableFields, true)) {
	            $data[$field] = $value;
	        }
	    }

		if(!empty($lm)){
			$list_lm = $this->getCategoryById($lm);
			if (empty($category)) {
				throw new ValidateException('所选分类不存在');
			}
			$data['list_lm'] = $list_lm['list_lm'] ?? '';
			// 可不要
			if(empty($data['ym_key'])){
				$data['ym_key'] = $list_lm['ym_key'] ?? '';
			}
		}
		$result = $this->model->save($data);
		if ($result === false) {
			throw new \RuntimeException('信息创建失败');
		}
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
						$list_lm = $this->getCategoryById($data['lm']);
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
	 * @param  int  $id    [ID]
	 * @param  array   $data  [需要更新的数据]
	 * @param  boolean $force [是否需要验证数据]
	 * @return Model         [description]
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
	 * @return  array            
	 */
	public function batchUpdate(array $data, string $pk = 'id', array $allowFields = []){
		if (empty($data)) {
			return 0;
		}
	
		// 1. 校验主键字段名合法
		if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $pk)) {
			throw new \InvalidArgumentException("非法的主键字段名：{$pk}");
		}
	
		// 2. 校验每条记录都有主键，并检测重复主键
		$ids = [];
		foreach ($data as $index => $item) {
			if (!array_key_exists($pk, $item) || $item[$pk] === '' || $item[$pk] === null) {
				throw new \InvalidArgumentException('第 ' . ($index + 1) . " 条数据缺少主键字段 '{$pk}'");
			}
			$id = $item[$pk];
			if (isset($ids[$id])) {
				throw new \InvalidArgumentException("主键值 '{$id}' 重复，无法批量更新");
			}
			$ids[$id] = true;
		}
		$ids = array_keys($ids);
	
		// 3. 收集所有待更新字段（去主键）
		$fields = [];
		foreach ($data as $item) {
			$fields = array_merge($fields, array_keys($item));
		}
		$fields = array_unique($fields);
		$fields = array_diff($fields, [$pk]);
	
		// 4. 字段名校验 + 白名单过滤
		$fields = array_filter($fields, function ($field) use ($allowFields) {
			// 字段名必须合法，防止 SQL 注入
			if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $field)) {
				return false;
			}
			return empty($allowFields) || in_array($field, $allowFields, true);
		});
	
		if (empty($fields)) {
			throw new \InvalidArgumentException('没有允许更新的字段');
		}
	
		// 5. 构建 CASE WHEN SQL
		$tableName = $this->model->getTable();
		$cases     = [];
		$bind      = [];
	
		foreach ($fields as $field) {
			$case = "`{$field}` = CASE `{$pk}` ";
			foreach ($data as $item) {
				// 关键：用 array_key_exists，null 值也能更新
				if (!array_key_exists($field, $item)) {
					continue;
				}
				$case   .= "WHEN ? THEN ? ";
				$bind[]  = $item[$pk];    // WHEN 的主键值
				$bind[]  = $item[$field]; // THEN 的字段值
			}
			$case   .= "ELSE `{$field}` END";
			$cases[] = $case;
		}
	
		$sql = "UPDATE `{$tableName}` SET " . implode(', ', $cases);
	
		// 6. WHERE 主键 IN
		$placeholders = implode(',', array_fill(0, count($ids), '?'));
		$sql .= " WHERE `{$pk}` IN ({$placeholders})";
		$bind = array_merge($bind, $ids);
	
		// 7. 执行（用 Db::execute，不是连接实例）
		try {
			return (int) Db::execute($sql, $bind);
		} catch (\Throwable $e) {
			Log::error('批量更新失败', [
				'table' => $tableName,
				'sql'   => $sql,
				'bind'  => $bind,
				'error' => $e->getMessage(),
			]);
			// 不把原始异常信息直接抛给调用方，保留异常链
			throw new \RuntimeException('批量更新失败：' . $e->getMessage(), 0, $e);
		}
	}

	/**
	 * [updateByWhere 根据条件更新信息]
	 * @param  array  $where [description]
	 * @param  array  $data  [description]
	 * @return model        [description]
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
	 * @param  int  $id    [description]
	 * @param  boolean $force [description]
	 * @return bool         [description]
	 */
	public function delete(?int $id, $force = false): bool{
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
	 * @param  array       $params [description] isTrashed 软删除； where 是查询条件； with 是关联表； withCount 是关联表数量； keyword 是模糊查询； startTime 开始时间，endTime结束时间；order排序。
	 * @param  int|integer $page   [description]
	 * @param  int|integer $limit  [description]
	 * @return Paginator              [description]
	 */
	public function getList(?array $params = [], int $page = 1, int $limit = 18): Paginator{

		// dump($params);
		$query = $this->buildQuery($params);
		// 关联加载：限制允许的关联名，避免任意关联
		$allowWith = ['profile', 'priceList', 'article', 'record', 'usedInfo', 'info']; // 按你项目实际关联改
		if (!empty($params['with'])) {
			$with = $params['with'];
			// 兼容字符串写法：with('category,tags')
			if (is_string($with)) {
				$with = explode(',', $with);
			}

			$filtered = [];
			foreach ((array)$with as $key => $val) {
				if (is_int($key)) {
					// 索引数组：值是关联名，如 ['category', 'tags']
					if (is_string($val) && in_array($val, $allowWith, true)) {
						$filtered[] = $val;
					}
				} else {
					// 关联数组：键是关联名，值是闭包或字段数组
					// 如 ['category' => function(){...}]
					if (in_array($key, $allowWith, true)) {
						$filtered[$key] = $val;
					}
				}
			}

			if ($filtered) {
				$query->with($filtered);
			}
		}

		if (!empty($params['withCount'])) {
			$withCount = $params['withCount'];
			// 兼容字符串写法：with('category,tags')
			if (is_string($withCount)) {
				$withCount = explode(',', $withCount);
			}

			$filtered = [];
			foreach ((array)$withCount as $key => $val) {
				if (is_int($key)) {
					// 索引数组：值是关联名，如 ['category', 'tags']
					if (is_string($val) && in_array($val, $allowWith, true)) {
						$filtered[] = $val;
					}
				} else {
					// 关联数组：键是关联名，值是闭包或字段数组
					// 如 ['category' => function(){...}]
					if (in_array($key, $allowWith, true)) {
						$filtered[$key] = $val;
					}
				}
			}

			if ($filtered) {
				$query->withCount($filtered);
			}
		}
		// 排序：白名单校验，防止 SQL 注入
		$orderBy = $this->normalizeOrder($params['order'] ?? 'px desc,wtime desc,id desc');
		$query->order($orderBy);

		$page  = max(1, (int)($params['page'] ?? 1));
		$limit = (int)($params['limit'] ?? 18);
		$limit = $limit > 0 ? min($limit, 100) : 18; // 限制最大条数，防止被刷

		$lists = $query->paginate([
			'list_rows' => $limit,
			'page' => $page
		]);
		// 批量加载图片
		$lists->setCollection(
			loadGalleries($lists->getCollection())
		);
		// 批量加载视频
		$lists->setCollection(
			loadVideos($lists->getCollection())
		);

		return $lists;
	}

	/**
	 * [restore 恢复数据]
	 * @param  int $id [description]
	 * @return array     [description]
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
	 * @param  int $id [description]
	 * @return array     [description]
	 */
	public function destroy(?int $id){
		$model = $this->model->onlyTrashed()->find($id);
		if (!$model) {
			throw new \Exception(Lang::get('tip')['noData']);
		}
		return $model->destroy($id,true);;
	}

	

	/**
	 * [count 获取数量]
	 * @param  array  $params [条件] isTrashed 软删除； where 是查询条件；keyword是模糊查询； $timeType 传 'today' / 'week' / 'month' / 'year'；startTime 开始时间，endTime结束时间；order排序。
	 * @return float       [description]
	 */
	public function count(?array $params = []){
		return $this->buildQuery($params)->count();
	}

	
}
?>