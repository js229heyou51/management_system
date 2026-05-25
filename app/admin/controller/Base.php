<?php
declare (strict_types = 1);

namespace app\admin\controller;

use think\facade\Session;
use think\facade\Cookie;
use think\facade\View;
use think\facade\Lang;
use think\facade\Filesystem;  
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use app\common\model\SetupGl as MS;
use app\common\model\Master as MM;
use app\common\model\MasterLog;
use app\common\model\Config;
use app\common\model\KeyCo as KC;
use app\common\service\PlinfoService;
use app\common\service\PlfileService;
use app\common\service\PlimageService;
use app\common\service\SetupGlService;
use app\common\service\SetupSyService;
use app\common\service\MasterService;
use app\common\service\MasterMenuService;
/**
 * 
 */
class Base extends \app\BaseController{
	public static $cong;
	protected $admin;
	public static $data;
	public $color;
	public $langHtml;
	public $lang;
	protected $route;

	protected function initialize(){
		$this->route = request()->controller();
		$rememberToken = cookie('remember_token')??session('remember_token');
		$this->admin = new MasterService()->getByWhere(['remember_token'=>$rememberToken]);
		$this->lang = Lang::getLangSet();
		$this->langHtml = Lang::get();
		$this->color = cookie('color');
		$setup = SetupGlService::getById(1);
		$logo = '';
		$icon = '';
		$web_logo = '';
		$web_icon = '';
		if($setup['logo']){
			$logo = getGalleryList($setup['logo']);
			$web_logo = $logo[0]['path'] ?? '';
		}
		if($setup['icon']){
			$icon = getGalleryList($setup['icon']);
			$web_icon = $icon[0]['path'] ?? '';
		}
		if(empty(self::$cong)){
			$config = Config::where('type','config')->find();
			self::$cong = unserialize($config['lists']);
		}
		View::assign([
			'route' => $this->route,
			'web_logo' => $web_logo,
			'web_icon' => $web_icon,
			'admin' => $this->admin,
			'color' => $this->color,
			'langHtml' => $this->langHtml,
		]);
	}

	/**
	 * [setWhere 设置查询条件]
	 * @param [array] $params [查询条件数组]
	 */
	public function setWhere($params){
		$where = [];
		if(!empty($params['lm'])){
			$where[] = ["list_lm", "like", "%," . $params['lm'] . ",%"];
		}
		$zt_val = $params['zt_val']??'';
		if($zt_val == 'ding1'){
			$where[] = ["ding",'=', 1];
		}else if($zt_val == 'ding2'){
			$where[] = ["ding",'=', 0];
		}else if($zt_val == 'tuijian1'){
			$where[] = ["tuijian",'=', 1];
		}else if($zt_val == 'tuijian2'){
			$where[] = ["tuijian",'=', 0];
		}else if($zt_val == 'hot1'){
			$where[] = ["hot",'=', 1];
		}else if($zt_val == 'hot2'){
			$where[] = ["hot",'=', 0];
		}else if($zt_val == 'pass1'){
			$where[] = ["pass",'=', 1];
		}else if($zt_val == 'pass2'){
			$where[] = ["pass",'=', 0];
		}

		if(!empty($params['startDate'])){
			$where[] = ['wtime', '>', $params['startDate']]; 
		}
		if(!empty($params['endDate'])){
			$where[] = ['wtime', '<', $params['endDate']]; 
		}

		return $where;
	}
	/**
	 * [recycleMake 回收站操作]
	 * @param  [service] $service [信息的service]
	 * @param  [array] $params  [act操作类型，id操作的id，checkbox选中的数据]
	 * @return [json]          [code状态码200成功，msg提示信息]
	 */
	public function recycleMake($service,$params){
		$act = $params['act'];
		$id = $params['id']??'';
		$checkbox = $params['checkbox'] ?? '';
		if(empty($id)){
			return json(['code'=>201,'msg'=>lang('tip')['noData']]);
		}
		if(empty($checkbox)){
			return json(['code'=>201,'msg'=>lang('tip')['selectData']]);
		}
		if($act == 'recovery'){
			try{
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$service->restore($value);
					}
				}
				return ['code'=>200,'msg'=>lang('tip')['operate'].lang('tip')['success']];
			}catch (\Exception $e){
				return ['code'=>201,'msg'=>lang('tip')['operate'].lang('tip')['fail'].$e->getMessage()];
			}
		}
		if($act == 'remove'){
			try{
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$service->destroy($value);
					}
				}
				return ['code'=>200,'msg'=>lang('tip')['operate'].lang('tip')['success']];
			}catch (\Exception $e){
				return ['code'=>201,'msg'=>lang('tip')['operate'].lang('tip')['fail'].$e->getMessage()];
			}
		}
	}
	/**
	 * [statusMake 信息状态操作]
	 * @param  [service] $service [信息的service]
	 * @param  [array] $params  [act操作类型，id操作的id，checkbox选中的数据]
	 * @return [json]          [code状态码200成功，msg提示信息]
	 */
	public function statusMake($service,$params){
		$act = $params['act'];
		$id = $params['id'];
		$px = $params['px']??'';
		$checkbox = $params['checkbox']??'';

		if($act == 'ding1'){
			if(empty($checkbox)){
				return json(['code'=>201,'msg'=>lang('tip')['selectData']]);
			}
			$data = [];
			foreach ($id as $key => $value){
				if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
					$data[] = [
						'id' => $value,
						'ding' => 1
					];
				}
			}
			$update = $service->batchUpdate($data, 'id', ['ding']);

			if(empty($update)){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}

		if($act == 'ding2'){
			if(empty($checkbox)){
				return json(['code'=>201,'msg'=>lang('tip')['selectData']]);
			}
			$data = [];
			foreach ($id as $key => $value){
				if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
					$data[] = [
						'id' => $value,
						'ding' => 0
					];
				}
			}
			$update = $service->batchUpdate($data, 'id', ['ding']);
			if(empty($update)){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}

		if($act == 'tj1'){
			if(empty($checkbox)){
				return json(['code'=>201,'msg'=>lang('tip')['selectData']]);
			}
			$data = [];
			foreach ($id as $key => $value){
				if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
					$data[] = [
						'id' => $value,
						'tuijian' => 1
					];
				}
			}
			$update = $service->batchUpdate($data, 'id', ['tuijian']);

			if(empty($update)){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}

		if($act == 'tj2'){
			if(empty($checkbox)){
				return json(['code'=>201,'msg'=>lang('tip')['selectData']]);
			}
			
			$data = [];
			foreach ($id as $key => $value){
				if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
					$data[] = [
						'id' => $value,
						'tuijian' => 0
					];
				}
			}
			$update = $service->batchUpdate($data, 'id', ['tuijian']);

			if(empty($update)){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}

		if($act == 'hot1'){
			if(empty($checkbox)){
				return json(['code'=>201,'msg'=>lang('tip')['selectData']]);
			}

			$data = [];
			foreach ($id as $key => $value){
				if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
					$data[] = [
						'id' => $value,
						'hot' => 1
					];
				}
			}
			$update = $service->batchUpdate($data, 'id', ['hot']);

			if(empty($update)){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}

		if($act == 'hot2'){
			if(empty($checkbox)){
				return json(['code'=>201,'msg'=>lang('tip')['selectData']]);
			}

			$data = [];
			foreach ($id as $key => $value){
				if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
					$data[] = [
						'id' => $value,
						'hot' => 0
					];
				}
			}
			$update = $service->batchUpdate($data, 'id', ['hot']);

			if(empty($update)){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}

		if($act == 'pass1'){
			if(empty($checkbox)){
				return json(['code'=>201,'msg'=>lang('tip')['selectData']]);
			}
			
			$data = [];
			foreach ($id as $key => $value){
				if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
					$data[] = [
						'id' => $value,
						'pass' => 0
					];
				}
			}
			$update = $service->batchUpdate($data, 'id', ['pass']);

			if(empty($update)){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}

		if($act == 'pass2'){
			if(empty($checkbox)){
				return json(['code'=>201,'msg'=>lang('tip')['selectData']]);
			}
			
			$data = [];
			foreach ($id as $key => $value){
				if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
					$data[] = [
						'id' => $value,
						'pass' => 1
					];
				}
			}
			$update = $service->batchUpdate($data, 'id', ['pass']);

			if(empty($update)){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}
		if($act == 'del'){
			if(empty($checkbox)){
				return json(['code'=>201,'msg'=>lang('tip')['selectData']]);
			}
			$delete = 0;
			foreach ($id as $key => $value){
				if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
					$del = $service->delete($value);
					if(!empty($del)){
						$delete ++;
					}
				}
			}
			if(empty($delete)){
				return json(['code'=>201,'msg'=>lang('tip')['noData'].lang('tip')['del']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['del'].lang('tip')['success']]);
		}

		// 单个
		if($act == 'ding'){
			if($id == ''){
				return json(['code'=>201,'msg'=>lang('tip')['id'].lang('tip')['cannotBeEmpty']]);
			}
			$find = $service->getById($id);

			if($find['ding'] == 1){
				$data['ding'] = 0;
			}else{
				$data['ding'] = 1;
			}
			$update = $service->update($id,$data,false);

			if(empty($update)){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}
		if($act == 'tuijian'){
			if($id == ''){
				return json(['code'=>201,'msg'=>lang('tip')['id'].lang('tip')['cannotBeEmpty']]);
			}
			$find = $service->getById($id);

			if($find['tuijian'] == 1){
				$data['tuijian'] = 0;
			}else{
				$data['tuijian'] = 1;
			}
			$update = $service->update($id,$data,false);

			if(empty($update)){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}
		if($act == 'hot'){
			if($id == ''){
				return json(['code'=>201,'msg'=>lang('tip')['id'].lang('tip')['cannotBeEmpty']]);
			}
			$find = $service->getById($id);

			if($find['hot'] == 1){
				$data['hot'] = 0;
			}else{
				$data['hot'] = 1;
			}
			$update = $service->update($id,$data,false);

			if(empty($update)){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}
		if($act == 'pass'){
			if($id == ''){
				return json(['code'=>201,'msg'=>lang('tip')['id'].lang('tip')['cannotBeEmpty']]);
			}
			$find = $service->getById($id);

			if($find['pass'] == 1){
				$data['pass'] = 0;
			}else{
				$data['pass'] = 1;
			}
			$update = $service->update($id,$data,false);

			if(empty($update)){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}
		
		if($act == 'sort'){
			if($id == ''){
				return json(['code'=>201,'msg'=>lang('tip')['id'].lang('tip')['cannotBeEmpty']]);
			}
			$find = $service->getById($id);

			$data['px'] = $param['px'] ?? '100';
			$update = $service->update($id,$data,false);

			if(empty($update)){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail']]);
			}
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}
	}
	/**
	 * [statusLmMake 分类状态操作]
	 * @param  [categoryService] $categoryService [分类的service]
	 * @param  [array] $params          [act操作类型，id操作的id，checkbox选中的数据]
	 * @return [json]                  [code状态码200成功，msg提示信息]
	 */
	public function statusLmMake($categoryService,$params){
		$act = $params['act'];
		$id_lm = $params['id']??'';
		$px = $params['px']??'';
		$find = $categoryService->getCategoryById($id_lm);
		if($act == 'px'){
			$data['px'] = $px;
			try{
				$up = $categoryService->updateCategory($id_lm,$data,false);;
				$this->master_log(lang('tip')['sort'].$this->conf['sy']['name'].lang('tip')['category'].'：'.$find['title_lm']);
				return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>lang('tip')['edit'].lang('tip')['fail'].$e->getMessage()]);
			}
		}

		if($act == 'tuijian_lm'){
			if($id_lm == ''){
				return json(['code'=>201,'msg'=>lang('tip')['id'].lang('tip')['cannotBeEmpty']]);
			}
			if($find['tuijian'] == 1){
				$data['tuijian'] = 0;
			}else{
				$data['tuijian'] = 1;
			}
			$update = $categoryService->updateCategory($id_lm,$data,false);;
			if($update['tuijian'] == 0){
				$tuijianTitle = lang('tip')['cancel'].lang('tip')['tuijianTitle'];
			}else{
				$tuijianTitle = lang('tip')['tuijianTitle'];
			}
			$this->master_log($tuijianTitle.$this->conf['sy']['name'].lang('tip')['category'].'：'.$find['title_lm']);
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}
		if($act == 'hot_lm'){
			if($id_lm == ''){
				return json(['code'=>201,'msg'=>lang('tip')['id'].lang('tip')['cannotBeEmpty']]);
			}

			if($find['hot'] == 1){
				$data['hot'] = 0;
			}else{
				$data['hot'] = 1;
			}
			$update = $categoryService->updateCategory($id_lm,$data,false);
			if($update['hot'] == 0){
				$hotTitle = lang('tip')['cancel'].lang('tip')['hotTitle'];
			}else{
				$hotTitle = lang('tip')['hotTitle'];
			}
			$this->master_log($hotTitle.$this->conf['sy']['name'].lang('tip')['category'].'：'.$find['title_lm']);
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}
		if($act == 'pass_lm'){
			if($id_lm == ''){
				return json(['code'=>201,'msg'=>lang('tip')['id'].lang('tip')['cannotBeEmpty']]);
			}
			if($find['pass'] == 1){
				$data['pass'] = 0;
			}else{
				$data['pass'] = 1;
			}
			$update = $categoryService->updateCategory($id_lm,$data,false);
			if($update['pass'] == 1){
				$passTitle = lang('tip')['cancel'].lang('tip')['passTitle'];
			}else{
				$passTitle = lang('tip')['passTitle'];
			}
			$this->master_log($passTitle.$this->conf['sy']['name'].lang('tip')['category'].'：'.$find['title_lm']);
			return json(['code'=>200,'msg'=>lang('tip')['edit'].lang('tip')['success']]);
		}
	}

	/**
	 * [plInfoCreate 批量信息]
	 * @param  int    $id    [当前插入的数据id]
	 * @param  int    $sy_id [系统id]
	 * @return [type]        [description]
	 */
	public function plInfoCreate(int $id,int $sy_id): bool{
		$plinfoService = new PlinfoService();
		$sesname = $plinfoService->getSesname();
		$pr_id = Session::get($sesname);
		if(!empty($pr_id)){
			$update['pl_id'] = $id;
			$update['sy_id'] = $sy_id;
			$up = $plinfoService->updateByPlId($pr_id,$update);
			Session::delete($sesname);
			return true;
		}
		return true;
	}

	/**
	 * [plFileCreate 批量文件]
	 * @param  int    $id    [当前插入的数据id]
	 * @param  int    $sy_id [系统id]
	 * @return [type]        [description]
	 */
	public function plFileCreate(int $id,int $sy_id): bool{
		$plfileService = new PlfileService();
		$sesname = $plfileService->getSesname();
		$pr_id = Session::get($sesname);
		if(!empty($pr_id)){
			$update['pl_id'] = $id;
			$update['sy_id'] = $sy_id;
			$up = $plfileService->updateByPlId($pr_id,$update);
			Session::delete($sesname);
			return true;
		}
		return true;
	}

	public function theme(){
		$data = request()->param();
		$color = $data['color']??'009688';
		cookie('color',$color);
		return json(['code'=>200,'msg'=>'']);
	}


	// 文件上传
	public function upload_file(){
		$file = request()->file();
		$files = request()->file('file');
		if(empty($file)){
			return json(['code'=>201,'msg'=>lang('tip')['noFilesUploaded']]);
		}
		try {
			$fileExt = 'gz,rar,zip,tar,tgz,gzip,gif,jpg,png,bmp,tif,tiff,pdf,csv,txt,xml,doc,docx,ppt,pptx,xls,xlsx,jpg,png,gif,jpeg';
			$filesize = '20480';
			validate(['image'=>'filesize:'.$filesize.'|fileExt:'.$fileExt.''])->check($file);
			$info = \think\facade\Filesystem::disk('public')->putFile('upfile',$files,'null');
		} catch (\think\exception\ValidateException $e) {
			return json(['code'=>201,'msg'=>$e->getMessage()]);
		}
		$info = str_replace("\\","/",$info);
		$path = '/storage/';
		$file = $path.$info;
		return json(['code'=>200,'data'=>$file,'url'=>"".$file]);
	}

	// 视频上传
	public function upload_video(){
		$file = request()->file();
		$files = request()->file('file');
		if(empty($file)){
			return json(['code'=>201,'msg'=>lang('tip')['noVideoUploaded']]);
		}
		try {
			$fileExt = 'aiff,asf,avi,fla,flv,mid,mov,mp3,mp4,mpc,mpeg,mpg,qt,ram,rm,rmi,rmvb,swf,wav,wma,wmv';
			$filesize = '20480';
			validate(['image'=>'filesize:'.$filesize.'|fileExt:'.$fileExt.''])->check($file);
			$info = \think\facade\Filesystem::disk('public')->putFile('upvideo',$files,'null');
		} catch (\think\exception\ValidateException $e) {
			return json(['code'=>201,'msg'=>$e->getMessage()]);
		}
		$info = str_replace("\\","/",$info);
		$path = '/storage/';
		$file = $path.$info;
		return json(['code'=>200,'data'=>$file,'url'=>"".$file]);
	}

	//搜索结果中的关键字高亮
	public function rekey($text){
		if (self::$cong['key']==true){
			$arr=$this->read_key();
			if ($arr[0]!='kong'){
				foreach($arr as $v){
					if(preg_match('/'.$v['title'].'/iSU', $text)){
						if(!preg_match('/<a([^>]*)>'.$v['title'].'<\/a>/iSU', $text)){
							$text = str_replace($v['title'],addslash('<a href="'.$v['link_url'].'" target="_blank">'.$v['title'].'</a>'),$text);
						}
					}
				}
			}
		}
		return $text;
	}

	//读取整站关键字，用于内链
	public function read_key(){
		static $result=array();
		if (!empty($result)){
			return $result;
		}else{
			$where[] = ['lang','=',$this->lang];
			$arr= KC::where($where)->order('px desc,id desc')->select();
			foreach($arr as $v){
				$result[]=array('title'=>$v['title'],'link_url'=>$v['link_url']);
			}
			if (empty($result)){
				$result[0]='kong';	
			}
			return $result;
		}
	}
	//记录后台操作日志
	public function master_log($z_body,$username=''){
		$conf=self::$cong;
		if ($conf['log']==true){
			if($username==''){
				$username=$this->admin['username']??'';
			}
			$data['username'] = $username;
			$data['z_body'] = $z_body;
			$data['ip'] = request()->ip();
			$data['wtime'] = time();
			$data['create_at'] = date('Y-m-d H:i:s');
			$data['lang'] = Lang::getLangSet();
			MasterLog::insert($data);
		}
	}
	
	public function addTextToImage($text,$fileName,$arr){
		// 图片路径
		$imagePath = 'static/admin/images/zhengshu.jpg';
		// 要添加的文字
		$text = $text;
		// 字体文件路径（需要安装字体文件）
		$fontPath = '/static/admin/fonts/Alibaba-PuHuiTi-Regular.ttf';
		// 创建图片资源
		$image = imagecreatefromjpeg($imagePath);
		// 获取图片宽度和高度
		$width = imagesx($image);
		$height = imagesy($image);
		// 设置文字颜色（RGB）
		$textColor = imagecolorallocate($image, 255, 255, 255); // 白色
		// 设置字体大小
		$fontSize = 12;
		// 计算文字宽度和高度，确定文字在图片上的位置
		$bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
		$textWidth = $bbox[2] - $bbox[0];
		$textHeight = $bbox[7] - $bbox[1];
		$x = ($width - $textWidth) / 2; // 水平居中
		$y = ($height + $textHeight) / 2; // 垂直居中 
		foreach($arr as $v){
			//$textsx=$v['x']-($v['s']*0.08)-1;
			//$textsy=$v['y']+$v['s']-1;
			$textxx=$v['x']-($v['s']*0.08);
			$textxy=$v['y']+$v['s'];
			//文件 字体 角度 文字在图片上x轴坐标 文字在图片上y轴坐标 文字水印字体文件路径 文字
			//imagettftext($sim,$font,0,$textsx,$textsy,$black,$ttf,$v['t']);
			imagettftext($image,(int)$v['s'],0,(int)$textxx,(int)$textxy,$textColor,$fontPath,$v['t']);	
		}
		// 将文字添加到图片上
		// imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $text);
		// 保存带有文字的图片
		$filePath = 'storage/upage/';
		if(!is_dir($filePath)){
			mkdir($filePath, 0777, true);
		}
		$fileName = ''.$filePath.$fileName.'.jpg';
		imagejpeg($image, $fileName);
		// 释放图片资源
		imagedestroy($image);
		// 返回成功消息或输出图片
		return $fileName;
	}

	// 导入

	public function importExecl($savePath){
		$spreadsheet = IOFactory::load($savePath);  
		$worksheet = $spreadsheet->getActiveSheet();  
		// 获取第一行的最高列索引
		$highestColumn = $worksheet->getHighestColumn();
		// 将最高列索引转换为列号（例如：'A' 转换为 1，'B' 转换为 2，等等）
		$data = []; 
		$data['column'] = Coordinate::columnIndexFromString($highestColumn);   
		foreach ($worksheet->getRowIterator(2) as $row) {  
			$rowData = [];  
			$cellIterator = $row->getCellIterator();  
			$cellIterator->setIterateOnlyExistingCells(false);  
			foreach ($cellIterator as $cell) {  
				$rowData[] = $cell->getValue();  
			}  

			$data['data'][] = $rowData;
		} 
		return $data;
	}

	/**
	 * 扫描文件夹获取所有文件
	 */
	function scanFolder($folderPath, $recursive = true) {
		if (!is_dir($folderPath)) {
			return json(['status' => 401,'msg' => '文件夹不存在' . $folderPath]);
		}
		$images = [];
		// $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
		$allowedExtensionsStr = 'gz,rar,zip,tar,tgz,gzip,gif,jpg,png,bmp,tif,tiff,pdf,csv,txt,xml,doc,docx,ppt,pptx,xls,xlsx,jpg,png,gif,jpeg';
		$allowedExtensions = explode(',', $allowedExtensionsStr);
		$this->scanDirectory($folderPath, $images, $recursive, $allowedExtensions);
		return $images;
	}

	/**
	 * 递归扫描目录
	 */
	function scanDirectory($dir, &$images, $recursive, $allowedExtensions) {
		$files = scandir($dir);
		
		foreach ($files as $file) {
			if ($file == '.' || $file == '..') continue;
			
			$fullPath = $dir . '/' . $file;
			
			if (is_dir($fullPath) && $recursive) {
				// 递归扫描子目录
				$this->scanDirectory($fullPath, $images, $recursive, $allowedExtensions);
			} elseif (is_file($fullPath)) {
				// 检查是否是图片文件				
				$extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
				if (in_array($extension, $allowedExtensions)) {
					$images[$extension][] = '/'.$fullPath;
				}
			}
		}
	}

	public function replaceImagePlaceholders($content, $imageGallery, $placeholder = '{图片占位符}'){
		// 如果图库为空，返回原内容
		if (empty($imageGallery)) {
			return $content;
		}
		
		// 统计占位符数量
		$placeholderCount = substr_count($content, $placeholder);
		
		// 如果没有占位符，直接返回
		if ($placeholderCount === 0) {
			return $content;
		}
		
		// 为每个占位符随机选择图片
		for ($i = 0; $i < $placeholderCount; $i++) {
			// 随机选择一张图片
			$randomIndex = array_rand($imageGallery);
			$randomImage = $imageGallery[$randomIndex];
			
			// 生成图片HTML标签
			$imageTag = '<div class="picture"><img class="article-image" style="display: block; margin-left: auto; margin-right: auto;" src="' . $randomImage . '" alt="文章配图"></div>';
			
			// 替换第一个占位符
			$content = preg_replace('/' . preg_quote($placeholder, '/') . '/', $imageTag, $content, 1);
		}
		
		return $content;
	}
}




?>