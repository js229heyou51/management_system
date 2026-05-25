<?php
declare (strict_types = 1);

namespace app\admin\controller;

use think\facade\View;
use think\facade\Session;
use think\facade\Request;
use app\common\model\Master as M;
use app\common\model\MasterMenu;
use app\common\model\Config;
use app\admin\validate\Master as V;
use app\common\model\ArticleCo;
use app\common\model\FeedRecord;
use app\common\model\ProCo;
use app\common\model\VisitLog;
use app\common\service\ProService;
use app\common\service\ArticleService;

class Index extends Base{

	protected $middleware = ['AdminCheck'];

	public $route;

	protected function initialize() {
		parent::initialize();
	}

	public function index(){
		
		$where = 'pass=1';
		$orWhere = [];
		$menu_list = $this->admin['menu_list'];
		if(!empty($menu_list) && $menu_list != 'all'){
			$where .= ' and (id in ('.$menu_list.') or fid in ('.$menu_list.'))';
		}

		$list = Config::where('type','config')->field('lists')->find();

		$langList = unserialize($list['lists']??'');

		$menus = MasterMenu::where('lang',$this->lang)->whereRaw($where)->order('fid asc,px asc,id asc')->select()->toArray();

		if(!$menus){
			
		}

		$menuTree = [];
		foreach ($menus as $key => $value) {
			if($value['fid'] == 0){
				if(isset($menuTree[$value['id']])){
					$menuTree[$value['id']] = array_merge($value,$menuTree[$value['id']]);
				}else{
					$menuTree[$value['id']] = $value;
				}
			}else{
				$menuTree[$value['fid']]['children'][] = $value;
			}
		}


		View::assign([
			'menuTree' => $menuTree,
			'langList' => $langList['mlang']??'',
		]);
		return View::fetch();
	}
	public function welcome(){
		$proService = new ProService();
		$articleService = new ArticleService();
		// $today = strtotime('today');
		// $yesterday = strtotime('yesterday');
		// $week = strtotime('this week monday');
		// $month = strtotime(date('Y-m-d 00:00:00',strtotime('first day of this month')));
		$statistics = [];
		$where = [
			['lang','=',$this->lang],
		];
		if(tableExists('feed_record')){
			$statistics['article']['today'] = number_format(FeedRecord::where($where)->whereTime('wtime','today')->count());
		}
		if(tableExists('article_co')){
			$statistics['article']['all'] = number_format($articleService->count());
		}
		if(tableExists('pro_co')){
			$statistics['product']['all'] = number_format($proService->count());
		}
		$statistics['monthArr'] = [];
		$statistics['countArr'] = [];
		if(tableExists('visit_log')){
			$statistics['visit']['today'] = number_format(VisitLog::whereTime('visit_time','today')->count());
			$monthlyStats = VisitLog::field("DATE_FORMAT(visit_time, '%Y-%m') as month, COUNT(*) as count")
			->whereYear('visit_time',date('Y'))
			->group('month')
			->order('month', 'asc')
			->select();
			foreach ($monthlyStats as $k => $v) {
				$statistics['monthArr'][] = $v['month'];
				$statistics['countArr'][] = $v['count'];
			}
		}

		$serverInfo['os'] = PHP_OS;
		$serverInfo['space'] = round((disk_free_space('.')/(1024*1024)),2).'M';
		$serverInfo['addr'] = $_SERVER['HTTP_HOST'];
		$serverInfo['run'] = Request::server('SERVER_SOFTWARE');
		$serverInfo['php'] = PHP_VERSION;
		$serverInfo['php_run'] = php_sapi_name();
		$serverInfo['mysql'] = function_exists('mysql_get_server_info')?mysql_get_server_info():\think\facade\Db::query('SELECT VERSION() as mysql_version')[0]['mysql_version'];
		$serverInfo['think'] = app()->version();
		$serverInfo['upload'] = ini_get('upload_max_filesize');
		$serverInfo['max'] = ini_get('max_execution_time').'秒';

		View::assign([
			'statistics' => $statistics,
			'serverInfo' => $serverInfo,
		]); 
		return View::fetch();
	}

	public function changePassword(){
		if(Request::isPost()){
			$data = Request::param();
			if(empty($data['username'])){
				return json(['code'=>201,'msg'=>'参数错误']);
			}
			if(empty($data['old_password'])){
				return json(['code'=>201,'msg'=>'请输入原密码']);
			}
			$find = M::where('username',$data['username'])->find();

			if(!password_verify($data['old_password'],$find['password'])){
				return json(['code'=>201,'msg'=>'原密码错误']);
			}
			$validate = new V;
			if(!$validate->scene('Master')->check($data)){
				return ['code'=>201,'msg'=>$validate->getError()];
			}
			if($data['password'] !== $data['confirm_password']){
				return json(['code'=>201,'msg'=>'确认密码与新密码不一致']);
			}
			$password = password_hash($data['password'], PASSWORD_DEFAULT);
			$update = [
				'username' => $data['username'],
				'password' => $password,
			];
			try{
				$update = M::where('username',$data['username'])->save($update);
			}catch (\Exception $e){
				return ['code'=>201,'msg'=>'修改失败'.$e->getMessage()];
			}
			Session::delete('admin');
			return json(['code'=>200,'msg'=>'修改成功']);
		}
		View::assign([
			'username' => $this->admin['username'],
		]);
		return View::fetch();
	}
	# 清除缓存
	public function clear(){
		$a = delete_dir_file(Env::get('runtime_path').'cache/');
		$b = delete_dir_file(Env::get('runtime_path').'temp/');
		// $redis = Cache::store('redis')->handler();
		// $redis->flushDb();
		return json(['code'=>200,'msg'=>'清除成功']);
		if ($a || $b) {
			return json(['code'=>200]);
		} else {
			return json(['code'=>201,'msg'=>'清除失败']);
		}
	}



	// 不存在则翻译
	public function translateAll($lang = 'en'){

		ini_set('max_execution_time', 1000);
		// 获取全部数据表 $lang = $this->lang
		$tableLists = getDatabaseTables(true);
		foreach ($tableLists as $k => $v) {
			$fields = [];
			foreach ($v['fields'] as $key => $value) {
				$fields[] = $value['Field'];
			}
			if($v['rows'] > 0){
				if(in_array('lang', $fields)){
					$lists = Db::name($v['name'])->select()->toArray();
					if($v['name'] == 'setup_sy'){
						$data = [];
						foreach ($lists as $lk => $lv) {
							$data = $lv;
							$data['title'] = translate($lv['title'],'auto',$lang);
							
							try{
								if($lv['lang'] == $lang){
									$update = Db::name($v['name'])->update($data);
								}else{
									unset($data['id']);
									$data['lang'] = $lang;
									$insert = Db::name($v['name'])->insert($data);
									$id = Db::name($v['name'])->getLastInsID();
								}

							}catch(\Exception $e){
								return json([
									'code'=>400,
									'msg'=>$e->getMessage()
								]);
							}
						}
					}
				}
			}
		}
	}
}
