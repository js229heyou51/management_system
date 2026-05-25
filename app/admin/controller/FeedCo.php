<?php  
namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use think\facade\Session;
use app\common\model\Pl_info;
use app\common\model\Pl_file;
use app\common\model\Pl_image;
use app\common\model\SetupSy as MS;
use app\common\model\FeedLm as ML;
use app\common\model\FeedCo as MC;
use app\common\model\ArticleLm;
use app\common\model\WebCo;
use app\common\model\ArticleCo;
use app\common\model\FeedRecord;
use GuzzleHttp\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;  
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use app\common\service\SetupSyService;
use app\common\service\FeedService as IS;
use app\common\service\FeedCategoryService as CS;
use app\common\service\ArticleCategoryService;

class FeedCo extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];

	protected $sy_id = 9;
	protected $conf = [];
	protected $service = [];
	protected $categoryService = [];

	protected function initialize() {
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

	//
	public function recycle(){
		if(Request::isPost()){
			
		}else{
			$searchItem = Request::param();
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
	public function recycle_make(){
		$param = Request::param();
		return $this->recycleMake($this->service,$param);
	}

	// 信息首页
	public function default(){
		if(Request::isPost()){
			$data = Request::param();
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
			return json(['code'=>200,'where'=>$can_str,'msg'=>$this->langHtml['tip']['loading']]);

		}else{
			if(empty($this->conf)){
				return '<h1 style="text-align:center;padding-top:30px;">'.$this->langHtml['tip']['configSettingsFile'].'</h1>';
				die();
			}
			$searchItem = request()->param();
			$params['where'] = $this->setWhere($searchItem);
			$params['keyword'] = $searchItem['keyword']??'';
			$withCount = [
				'record' => function($query) {
					$query->whereTime('wtime','today');
				}];
			$with = [
					'article' => function($query){
						$query->withCount([
							'info' => function($query, &$alias) {
								$query->where('lang','=',$this->lang);
								$alias = 'info_count';
							},
							'usedInfo' => function($query, &$alias) {
								$query->where('pass',1)->where('lang','=',$this->lang)->where('read_num','>', 0);
								$alias = 'used_count';
							},
						]);
					}
				];
			$lists = $this->service->getListAll($params,false,$with,$withCount);
			$category = $this->categoryService->getCategoryList([],['info']);

			View::assign([
				'lists' => $lists,
				'category' => $category,
				'searchItem' => $searchItem
			]);
			return View::fetch();
		}
	}

	// 添加信息
	public function add(){
		if(request()->isPost()){
			$data = request()->param();
			if(!$this->conf['sy']['need_lm']){
				$data['lm'] = 0;
			}
			try{
				$info = $this->service->create($data);
				$id = $info->getLastInsID();
				if(!empty($this->conf['co']['info']) && $this->conf['co']['info'] == true){
					$plinfo = $this->plInfoCreate($id,$this->sy_id);
					if(!$plinfo){
						return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
					}
				}
				if(!empty($this->conf['co']['file']) && $this->conf['co']['file'] == true){
					$plFile = $this->plFnfoCreate($id,$this->sy_id);
					if(!$plFile){
						return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail']]);
					}
				}
				Base::master_log($this->langHtml['tip']['add'].$this->conf['sy']['name'].$this->langHtml['tip']['information'].'：'.$data['title']);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['add'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			$category = $this->categoryService->getCategoryList();
			$articleCate = ArticleLm::withCount([
					'info' => function($query, &$alias) {
						$query->where('lang','=',$this->lang);
						$alias = 'info_count';
					}, // 统计全部数量，别名为 total_count
					'usedInfo' => function($query ,&$alias) {
						$query->where('lang',$this->lang)->where('pass',1)->where('read_num','>', 0);
						$alias = 'used_count';
					}
				])->where('lang',$this->lang)->field('id_lm,title_lm')->order('fid,px desc,id_lm')->select()->toArray();

			$webCate = WebCo::with([
				'profile'=>function($query){
					$query->field('title_lm,id_lm');
				}])->where('lang',$this->lang)->field('id,title,lm')->order('lm desc,px desc,id desc')->select();

			View::assign([
				'category' => $category,
				'articleCate' => $articleCate,
				'webCate' => $webCate,
			]);
			return View::fetch('edit');
		}
	}

	// 信息修改
	public function edit(){
		$data = request()->param();
		if(request()->isPost()){
			$id = $data['id'] ?? '';
			if(!$this->conf['sy']['need_lm']){
				$data['lm'] = 0;
			}
			try{
				$update = $this->service->update($id,$data);
				Base::master_log($this->langHtml['tip']['edit'].$this->conf['sy']['name'].$this->langHtml['tip']['information'].'：'.$data['title']);
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
			}
		}else{
			$id = $data['id'];
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);   
			}
			$find = $this->service->getById($id);
			$category = $this->categoryService->getCategoryList();

			$articleCate = ArticleLm::withCount([
					'info'=> function($query, &$alias) {
						$query->where('lang','=',$this->lang);
						$alias = 'info_count';
					}, // 统计全部数量，别名为 total_count
					'usedInfo' => function($query ,&$alias) {
						$query->where('lang',$this->lang)->where('pass',1)->where('read_num','>', 0);
						$alias = 'used_count';
					}
				])->where('lang',$this->lang)->field('id_lm,title_lm')->order('fid,px desc,id_lm')->select()->toArray();


			$webCate = WebCo::with([
				'profile'=>function($query){
					$query->field('title_lm,id_lm');
				}])->where('lang',$this->lang)->field('id,title,lm')->order('lm desc,px desc,id desc')->select();

			View::assign([
				'category' => $category,
				'articleCate' => $articleCate,
				'webCate' => $webCate,
				'find' => $find,
			]);
			return View::fetch('edit');
		}
	}

	// 删除信息
	public function del(){
		$data = request()->param();
		$id = $data['id'];
		if(empty($id)){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
		}
		try{
			$bol = $this->service->delete($id);
			Base::master_log($this->langHtml['tip']['del'].$this->conf['sy']['name'].$this->langHtml['tip']['information'].'：'.$id);
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail'].$e->getMessage()]);
		}
	}

	// 
	public function make(){
		$params = Request::param();
		return $this->statusMake($this->service,$params);
	}

	//
	public function createWeb(){
		$param = Request::param();
		$id = $param['id'];
		if(empty($id)){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['noData']]);
		}
		$find = MC::where('id',$id)->field('id,title,article_str,web_str,num')->where('pass','=',1)->find();
		if(empty($find)){
			return json(['code'=>400,'msg'=>'该任务已屏蔽']);
		}
		$num = $find['num'] ?? 1;
		$title = $find['title'] ?? '';
		if(empty($title)){
			return json(['code'=>400,'msg'=>$this->langHtml['tip']['noData']]);
		}
		$where[] = ['lang','=', $this->lang];
		if($find['web_str']){
			// 有多少个网站需要发布文章
			$web = WebCo::with([
				'profile' => function($query){
					$query->field('id_lm,title_lm');
				}
			])->where('id','in',$find['web_str'])->field('lm,tlm,title,tfile,table_co,table_lm,link_url,web_url,id,release,folder_path')->where('pass','=',1)->select()->toArray();
			$count = WebCo::where('id','in',$find['web_str'])->where('pass','=',1)->count();
			if($count <= 0){
				return json(['code'=>400,'msg'=>$this->langHtml['tip']['noData']]);
			}
			
			// 随机获取 $num 篇文章
			$article = ArticleCo::with('profile')->where('lm','=',$find['article_str'])->where('pass','=',1)->where($where)->where('read_num',0)->orderRaw('rand()')->limit($num)->select()->toArray();
			$artCount = ArticleCo::where('lm','=',$find['article_str'])->where('pass','=',1)->where('read_num',0)->where($where)->orderRaw('rand()')->limit($num)->count();
			if($artCount <= 0){
				return json([
					'code' => 400,
					'msg' => '当前没有可用文章',
				]);
			}

			$recordTotal = FeedRecord::where('feed_id',$id)->whereTime('wtime','today')->count();

			if($recordTotal >= $num){
				return json([
					'code' => 400,
					'msg' => '该任务已超出今日发布数量',
				]);
			}

			$artKey = $num - $recordTotal;
			$webReleaseCount = 0;
			foreach ($web as $k => $v) {
				$recordCount = FeedRecord::where('web_id',$v['id'])->whereTime('wtime','today')->count();
				if(empty($v['release']) || $v['release'] <= $recordCount){
					$webReleaseCount++;
					continue;
				}
				for ($i=0; $i < $v['release']; $i++) { 
					$artKey --;
					if(isset($article[$artKey])){
						
						try{
							$client = new Client([
								'verify' => false,
							]);

							$url = $v['link_url'] ?? '';
							if(empty($url)){
								return json([
									'code' => 400,
									'msg' => '链接不能为空',
								]);
							}

							$postData['lm'] = $v['tlm'] ?? '';
							$postData['title'] = $article[$artKey]['title'] ?? '';
							$postData['keyword'] = $article[$artKey]['keyword'] ?? '';
							$postData['f_body'] = $article[$artKey]['f_body'] ?? '';
							$postData['z_body'] = $article[$artKey]['z_body'] ?? '';
							$postData['ym_key'] = $article[$artKey]['ym_key'] ?? '';
							$postData['table_co'] = $v['table_co'] ?? '';
							$postData['table_lm'] = $v['table_lm'] ?? '';
							$postData['folder_path'] = $v['folder_path'] ?? '';
							if($postData['title'] == ''){
								return json([
									'code' => 400,
									'msg' => '标题不能为空',
								]);
							}
							$act = 'knowledge';
							$response = $client->request('POST', $url, [
								'form_params' => [
									'act' => $act,
									'postData' => $postData,
								]
							]);
							// 获取状态码
							$statusCode = $response->getStatusCode();
							
							// 获取返回的内容 (字符串)
							$content = $response->getBody()->getContents();

							$jsonResponse = json_decode($response->getBody(), true);

							if($jsonResponse['status'] == 200){
								$web_url = str_replace('{$id}', $jsonResponse['id']??'', $v['web_url']??'');

								//投喂记录
								$data = [
									'article_id' => $jsonResponse['id'] ?? 0,
									'feed_id' => $find['id'],
									'web_url' => $web_url ?? '',
									'web_id' => $v['id'] ?? '',
									'name'     => $article[$artKey]['title'],
									'title_lm' => $article[$artKey]['profile']['title_lm'],
									'title'    => $title,
									'ym_key'   => $article[$artKey]['ym_key'],
									'wtime'    => date('Y-m-d H:i:s'),
									'lang'     => $this->lang,
									'ip'       => Request::ip(),
									'pass'     => 1,
									'account'  => $v['profile']['title_lm'].$v['title'],
									'px'	   => 100,
								];
								$feed = FeedRecord::insert($data);
								$updateData['read_num'] = $article[$artKey]['read_num'] + 1;
								$update = ArticleCo::where('id',$article[$artKey]['id'])->update($updateData);
								Base::master_log('投喂文章：'.$article[$artKey]['title'].'到网站:'.$title);
								// sleep(2);
							}else{
								$web_url = '';
								//投喂记录
								$data = [
									'article_id' => 0,
									'feed_id' => $find['id'],
									'web_url' => $web_url ?? '',
									'web_id' => $v['id'] ?? '',
									'name'     => $article[$artKey]['title'],
									'title_lm' => $article[$artKey]['profile']['title_lm'],
									'title'    => $title,
									'ym_key'   => $article[$artKey]['ym_key'],
									'wtime'    => date('Y-m-d H:i:s'),
									'lang'     => $this->lang,
									'ip'       => Request::ip(),
									'pass'     => 0,
									'account'  => $v['profile']['title_lm'].$v['title'],
									'px'	   => 100,
								];
								$feed = FeedRecord::insert($data);
								Base::master_log('投喂文章：'.$article[$artKey]['title'].'到网站:'.$title);
								return json([
									'code' => 201,
									'msg' => $jsonResponse['msg'],
								]);
							}
						}catch(\Exception $e){
							return json([
								'code' => 402,
								'msg' => $e->getMessage(),
							]);
						}
					}
				}
				
			}
			if($webReleaseCount >= $count){
				return json([
					'code' => 400,
					'msg' => '已超出今日发布数量',
				]);
			}
		}
		return json([
			'code' => 200,
			'msg' => '投喂成功'
		]);
	}


	// 记录导出
	public function recordExport(){
		$param = Request::param();
		if(Request::isPost()){
			$id = $param['id'];
			// $checkbox = $param['checkbox']??'';
			// if(empty($checkbox)){
			// 	return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
			// }
			$idArr = '';
			foreach ($id as $key => $value){
				// if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
					$idArr .= ','.$value;
				// }
			}
			$idArr = ltrim($idArr,',');
			$result = FeedRecord::where('id','in',$idArr)->order('id desc,wtime desc')->select()->toArray();

			// 创建Spreadsheet对象  
			$spreadsheet = new Spreadsheet();  
			$sheet = $spreadsheet->getActiveSheet();  
			// 设置样式
			$sheet->getColumnDimension('A')->setWidth(10);
			$sheet->getColumnDimension('B')->setWidth(15);
			$sheet->getColumnDimension('C')->setWidth(25);
			$sheet->getColumnDimension('D')->setWidth(15);
			$sheet->getColumnDimension('E')->setWidth(30);
			$sheet->getColumnDimension('F')->setWidth(20);
			$sheet->getColumnDimension('G')->setWidth(30);
			$sheet->getColumnDimension('H')->setWidth(30);

			$sheet->getDefaultRowDimension()->setRowHeight(20);

			$headerStyle = [
				'alignment' => [
					'horizontal' => Alignment::HORIZONTAL_CENTER,
					'vertical' => Alignment::VERTICAL_CENTER,
				],
			];
			$sheet->getStyle('A1:H1')->applyFromArray($headerStyle);


			$sheet->getStyle('A2:H999')->applyFromArray($headerStyle);
			$sheet->getStyle('E')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
			// 设置表头  
			$cell = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
			$fields_name = 'ID,任务名,投喂账号,文章分类,文章标题,发布时间,链接,SITE';
			$fields_str = 'id,title,account,title_lm,name,wtime,web_url,web_url';

			$fieldsName = explode(',',$fields_name);
			foreach ($fieldsName as $key => $value) {
				$sheet->setCellValue($cell[$key].'1', $value);
			}
			$row = 2;
			$fields = explode(',',$fields_str);
			foreach ($result as $key => $value) {
				foreach ($fields as $fieldsKey => $fieldsValue) {
					if($cell[$fieldsKey] == 'H'){
						$sheet->setCellValue($cell[$fieldsKey].$row, 'site:'.$value[$fieldsValue]);
					}else{
						$sheet->setCellValue($cell[$fieldsKey].$row, $value[$fieldsValue]);
					}
				}
				$row ++ ;  
			}
			// 创建Writer对象  
			$writer = new Xlsx($spreadsheet);  
			$filename = date('YmdHis').'.xlsx';
			$filePath = 'storage/downfiles/'.$filename; // 例如：'/var/www/html/myapp/runtime/file.xlsx'  
			// 提取目录路径（不包含文件名）
			$dirPath = dirname($filePath);  
  
			// 判断目录是否存在，如果不存在则创建  
			if (!is_dir($dirPath)) {  
				// 使用 mkdir 函数创建目录，0777 是权限设置，可以根据需要调整  
				if (!mkdir($dirPath, 0777, true)) {  
					// 创建目录失败时处理错误  
					return json(['code'=>200,'msg'=>'创建目录失败'.$dirPath]);
				}
			}
			// 使用写入对象保存文件
			try {
				// 使用写入对象保存文件到本地
				$writer->save($filePath);
				return json(['code'=>200,'msg'=>'文件保存成功！','filePath'=>'/'.$filePath,'filename'=>$filename]);
			} catch (\Exception $e) {
				return json(['code'=>201,'msg'=>'文件保存失败：' . $e->getMessage()]);
			}

		}
	}

	public function record(){
		if(Request::isPost()){
			$data = Request::param();
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
			return json(['code'=>200,'where'=>$can_str,'msg'=>$this->langHtml['tip']['loading']]);

		}else{
			$searchItem = Request::param();
			$zt_val = $searchItem['zt_val']??'';
			$keyword = $searchItem['keyword']??'';

			$where[] = ['lang','=',$this->lang];
			if($zt_val == 'pass1'){
				$where[] = ["pass",'=', 1];
			}else if($zt_val == 'pass2'){
				$where[] = ["pass",'=', 0];
			}
			if(!empty($keyword)){
				$where[] = ["title", "like", "%" . $keyword . "%"];
			}
			if(!empty($searchItem['startDate'])){
				$where[] = ['wtime', '>', $searchItem['startDate']]; 
			}
			if(!empty($searchItem['endDate'])){
				$where[] = ['wtime', '<', $searchItem['endDate']]; 
			}
			$limit = !empty((input('request.limit'))) ? (input('request.limit')) : '10';
			$curr = !empty((input('request.curr'))) ? (input('request.curr')) : '1';

			$count = FeedRecord::where($where)->count();
			$lists = FeedRecord::where($where)->order('ding desc,px desc,id desc')->select();
			$pageItem = [
				'count' => $count,
				'curr' => $curr,
				'limit' => $limit,
			];
			View::assign([
				'lists' => $lists,
				'pageItem' => $pageItem,
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}

	// 删除信息
	public function recordDel(){
		$data = Request::param();
		$id = $data['id'];
		if(empty($id)){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
		}
		$find = FeedRecord::where('id',$id)->find();
		$data['delete_time'] = date('Y-m-d H:i:s',time());
		try{
			$update = FeedRecord::where('id',$id)->save($data);
			Base::master_log($this->langHtml['tip']['del'].$this->conf['sy']['name'].$this->langHtml['tip']['information'].'：'.$find['title']);
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail'].$e->getMessage()]);
		}
	}

	public function makeRecord(){
		$param = Request::param();
		if(Request::isPost()){
			$act = $param['act'];
			$id = $param['id'];
			$px = $param['px']??'';
			$checkbox = $param['checkbox']??'';
			if($act == 'del'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				$delete = 0;
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$data['pass'] = 1;
						$data['delete_time'] = date('Y-m-d H:i:s',time());
						$del = FeedRecord::where('id',$value)->save($data);
						if(!empty($del)){
							$delete ++;
						}
					}
				}
				if(empty($delete)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['noData'].$this->langHtml['tip']['del']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
			}
			if($act == 'delall'){
				$delete = 0;
				foreach ($id as $key => $value){
					$data['pass'] = 1;
					$data['delete_time'] = date('Y-m-d H:i:s',time());
					$del = FeedRecord::where('id',$value)->save($data);
					if(!empty($del)){
						$delete ++;
					}
				}
				if(empty($delete)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['noData'].$this->langHtml['tip']['del']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
			}
		}
	}
	public function recycleRecord(){
		if(Request::isPost()){
			

		}else{

			$searchItem = Request::param();
			$keyword = $searchItem['keyword']??'';
			$where[] = ['lang','=',$this->lang];
			if(!empty($keyword)){
				$where[] = ["title", "like", "%" . $keyword . "%"];
			}
			$limit = !empty((input('request.limit'))) ? (input('request.limit')) : '5';
			$curr = !empty((input('request.curr'))) ? (input('request.curr')) : '1';

			$count = FeedRecord::onlyTrashed()->where($where)->count();
			$lists = FeedRecord::onlyTrashed()->where($where)->order('ding desc,px desc,id desc')->page($curr,$limit)->select();
			$pageItem = [
				'count' => $count,
				'curr' => $curr,
				'limit' => $limit,
			];
			View::assign([
				'lists' => $lists,
				'pageItem' => $pageItem,
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}
	public function recycle_makeRecord(){
		$param = Request::param();
		if(Request::isPost()){
			$act = $param['act'];
			$id = $param['id']??'';
			$checkbox = $param['checkbox'] ?? '';
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['noData']]);
			}
			if($act == 'recovery'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				try{
					foreach ($id as $key => $value){
						if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
							FeedRecord::onlyTrashed()->find($value)->restore();
						}
					}
					return ['code'=>200,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['success']];
				}catch (\Exception $e){
					return ['code'=>201,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['fail'].$e->getMessage()];
				}
			}
			if($act == 'remove'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				try{
					foreach ($id as $key => $value){
						if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
							FeedRecord::destroy($value,true);
						}
					}
					return ['code'=>200,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['success']];
				}catch (\Exception $e){
					return ['code'=>201,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['fail'].$e->getMessage()];
				}
			}
		}
	}


	public function record1(){

		if(Request::isPost()){
			$data = Request::param();
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
			return json(['code'=>200,'where'=>$can_str,'msg'=>$this->langHtml['tip']['loading']]);

		}else{
			$searchItem = Request::param();
			$zt_val = $searchItem['zt_val']??'';
			$keyword = $searchItem['keyword']??'';

			$where[] = ['lang','=',$this->lang];
			if($zt_val == 'pass1'){
				$where[] = ["pass",'=', 1];
			}else if($zt_val == 'pass2'){
				$where[] = ["pass",'=', 0];
			}
			if(!empty($keyword)){
				$where[] = ["title", "like", "%" . $keyword . "%"];
			}
			$limit = !empty((input('request.limit'))) ? (input('request.limit')) : '10';
			$curr = !empty((input('request.curr'))) ? (input('request.curr')) : '1';

			$count = FeedRecord::where($where)->count();
			$lists = FeedRecord::where($where)->order('ding desc,px desc,id desc')->select();
			$pageItem = [
				'count' => $count,
				'curr' => $curr,
				'limit' => $limit,
			];
			View::assign([
				'lists' => $lists,
				'pageItem' => $pageItem,
				'searchItem' => $searchItem,
			]);
			return View::fetch('record_1');
		}
	}


}

?>