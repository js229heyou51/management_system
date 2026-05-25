<?php  
namespace app\admin\controller;

use think\facade\Request;
use think\facade\View;
use think\facade\Db;
use think\facade\Filesystem;
use PhpOffice\PhpSpreadsheet\Spreadsheet;  
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use think\Response;

use app\common\model\Config as MP;

class PlDao extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];

	protected $conf = [];
	protected $route;
	protected $exportTableName;
	protected $importTableName;
	protected $imgUploadTableName;
	protected $plConf = [];

	protected function initialize() {
		parent::initialize();
		$this->route = Request::controller();
		$cong = MP::select();
		foreach ($cong as $key => $value) {
			if($value['type'] == 'imgUpload'){
				$this->imgUploadTableName = $value['table_name'];
				if(!empty($value['lists'])){
					$this->plConf['imgUploadConf'] = unserialize($value['lists']);
				}
			}
			if($value['type'] == 'import'){
				$this->importTableName = $value['table_name'];
				if(!empty($value['lists'])){
					$this->plConf['importConf'] = unserialize($value['lists']);
				}
			}
			if($value['type'] == 'export'){
				$this->exportTableName = $value['table_name'];
				if(!empty($value['lists'])){
					$this->plConf['exportConf'] = unserialize($value['lists']);
				}
			}
		}
		View::assign([
			'route' => $this->route,
			'plConf' => $this->plConf,
		]);
	}


	// 图片导入设置
	public function imgUploadSettings(){
		if(Request::isPost()){
			$conf = Request::param();
			$conf['sy']['need_lm'] = changety($conf['sy']['need_lm']);
			$conf['up']['allowext'] = $conf['sy']['allowext']??'jpg,gif,png,bmp';
			if($conf['up']['sm'] == 'yes'){
				$s_typ = $conf['s_typ'];
				foreach ($s_typ as $key => $value) {
					$um['s_nam'] = $conf['s_nam'][$key]; 
					$um['s_typ'] = $conf['s_typ'][$key]; 
					$um['s_wid'] = $conf['s_wid'][$key]; 
					$um['s_hei'] = $conf['s_hei'][$key];
					if(!empty($um['s_nam']) || !empty($um['s_wid']) || !empty($um['s_hei'])){
						$conf['sm'][]=$um;
					}else{
						return json(['code'=>201,'msg'=>'图片上传选择了生成缩略图，请填写缩略图的参数']);
					}
				}
			}else{
				$conf['sm'][]=array('s_nam'=>'','s_typ'=>false,'s_wid'=>0,'s_hei'=>0);
			}
			unset($conf['s_nam']);
			unset($conf['s_typ']);
			unset($conf['s_wid']);
			unset($conf['s_hei']);
			
			if(!tableExists($conf['sy']['table_co'])){
				return json(['code'=>201,'msg'=>'表不存在']);
			}
			$data['table_name'] = $conf['sy']['table_co'];
			$data['lists'] = serialize($conf);
			$data['type'] = 'imgUpload';

			$find = MP::where(['type'=>$data['type']])->find();
			if(!empty($find)){
				try{
					$update = MP::where(['type'=>$data['type']])->save($data);
					Base::master_log('修改图片导入系统：配置文件');
					return json(['code'=>200,'msg'=>'修改成功']);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>'修改失败'.$e->getMessage()]);
				}
			}else{
				try{
					$insert = MP::insert($data);
					Base::master_log('修改图片导入系统：配置文件');
					return json(['code'=>200,'msg'=>'修改成功']);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>'修改失败'.$e->getMessage()]);
				}
			}
		}else{
			$find = MP::where(['table_name'=>$this->importTableName,'type'=>'imgUpload'])->find();
			$category = [];
			$fields = '';
			if(!empty($find)){
				$find['lists'] = !empty($find['lists']) ? json_decode($find['lists'],1) : '';
				if(!empty($find['lists']['need_lm']) && $find['lists']['need_lm'] == 'true'){
					if(tableExists($find['lists']['table_lm'])){
						$fields = Db::getTableFields($this->importTableName);
						$category = Db::name($find['lists']['table_lm'])->order('px desc,id_lm asc')->select();
					}
				}
			}
			

			View::assign([
				'find' => $find,
				'category' => $category,
				'fields' => $fields,
			]);
			return View::fetch();
		}
	}

	public function pl_imru_tool(){
		if(Request::isPost()){
			$param = Request::param();
			$lm = $param['lm'];
			if($this->plConf['imgUploadConf']['sy']['need_lm'] == 'true' && $lm == ''){
				return json(['code'=>201,'msg'=>'请选择分类']);
			}
			if(!empty($this->plConf['imgUploadConf']['sy']['table_lm'])){
				$list_lm = Db::name($this->plConf['imgUploadConf']['sy']['table_lm'])->field('list_lm')->where('id_lm',$lm)->find();
				if(empty($list_lm)){
					return json(['code'=>201,'msg'=>'图片不能为空']);
				}
			}

			$img_sl = $param['img_sl'];
			if($img_sl == ''){
				return json(['code'=>201,'msg'=>'图片不能为空']);
			}
			foreach ($img_sl as $key => $value) {
				$arr['title'] = $param['title'][$key];
				$arr['img_sl'] = $param['img_sl'][$key];
				$arr['px'] = $param['px'][$key];
				$arr['lm'] = $lm;
				$arr['list_lm'] = $list_lm['list_lm']??'';
				$arr['ding'] = 0;
				$arr['tuijian'] = 0;
				$arr['hot'] = 0;
				$arr['pass'] = 1;
				$arr['wtime'] = date('Y-m-d H:i:s',time());
				$arr['ip'] = Request::ip();
				$arr['lang'] = $this->lang;
				$data[] = $arr;
			}
			try{
				$insert = Db::name($this->plConf['imgUploadConf']['sy']['table_co'])->insertAll($data);
				return json(['code'=>200,'msg'=>'添加成功']);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>'添加失败'.$e->getMessage()]);
			}
		}else{
			$find = MP::where(['table_name'=>$this->imgUploadTableName,'type'=>'imgUpload'])->find();
			if(!empty($find['lists'])){
				$find['lists'] = unserialize($find['lists']);
			}
			$category = [];
			if(isset($find['lists']['sy']['need_lm']) && $find['lists']['sy']['need_lm'] == true){
				if(tableExists($find['lists']['sy']['table_lm'])){
					$category = Db::name($find['lists']['sy']['table_lm'])->order('px desc,id_lm asc')->select();
				}
			}
			View::assign([
				'category' => $category,
			]);
			return View::fetch();
		}
	}


	public function importSettings(){
		if(Request::isPost()){
			$conf = Request::param();
			$conf['need_lm'] = changety($conf['need_lm']);
			$conf['fields'] = implode(',',$conf['fields']??[]);
			$conf['mr_fields'] = [];
			$conf['mr_fields_type'] = [];
			foreach ($conf['fname'] as $key => $value) {
				$conf['mr_fields'][$value] = $conf['fvalue'][$key];
				$conf['mr_fields_type'][$value] = $conf['ftype'][$key];
			}
			unset($conf['fname']);
			unset($conf['fvalue']);
			unset($conf['ftype']);
			if(!tableExists($conf['table_co'])){
				return json(['code'=>201,'msg'=>'表不存在']);
			}
			$data['table_name'] = $conf['table_co'];
			$data['lists'] = serialize($conf);
			$data['type'] = 'import';

			$find = MP::where(['type'=>$data['type']])->find();
			if(!empty($find)){
				try{
					$update = MP::where(['type'=>$data['type']])->save($data);
					Base::master_log('修改批量导入系统：配置文件');
					return json(['code'=>200,'msg'=>'修改成功']);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>'修改失败'.$e->getMessage()]);
				}
			}else{
				try{
					$insert = MP::insert($data);
					Base::master_log('修改批量导入系统：配置文件');
					return json(['code'=>200,'msg'=>'修改成功']);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>'修改失败'.$e->getMessage()]);
				}
			}

		}else{
			$find = MP::where(['table_name'=>$this->importTableName,'type'=>'import'])->find();
			$find['lists'] = !empty($find['lists']) ? unserialize($find['lists']) : '';
			$category = [];
			$fields = '';
			if(!empty($find['lists']['need_lm']) && $find['lists']['need_lm'] == 'true'){
				if(tableExists($find['lists']['table_lm'])){
					$fields = Db::getTableFields($this->importTableName);
					$category = Db::name($find['lists']['table_lm'])->order('px desc,id_lm asc')->select();
				}
			}

			View::assign([
				'fields' => $fields,
				'category' => $category,
				'find' => $find,
			]);
			return View::fetch();
		}
	}

	public function pl_daru_tool(){
		if(Request::isPost()){
			$data = Request::param();
			$lm = $data['lm'];
			if($this->plConf['importConf']['need_lm'] == 'true' && $lm == ''){
				return json(['code'=>201,'msg'=>'请选择分类']);
			}
			$list_lm = [];
			if(tableExists($this->plConf['importConf']['table_lm'])){
				$list_lm = Db::name($this->plConf['importConf']['table_lm'])->field('list_lm')->where('id_lm',$lm)->find();
			}

			$filename = $data['fil_sl'];
			if(empty($filename)){
				return json(['code'=>201,'msg'=>'请上传文件']);
			}

			$find = MP::where(['table_name'=>$this->importTableName,'type'=>'import'])->find();
			if(!empty($find['list'])){
				$find['lists'] = unserialize($find['lists']);
			}
			$mr_fields = !empty($find['lists']['mr_fields']) ? $find['lists']['mr_fields'] : '';
			$mr_fields_type = !empty($find['lists']['mr_fields_type']) ? $find['lists']['mr_fields_type'] : '';
			foreach ($mr_fields as $key => $value) {
				if($mr_fields_type[$key] == 'fun'){
					$temp[$key] = call_user_func($value);
				}else{
					$temp[$key] = $value;
				}
			}

			$result = Base::importExecl($filename);
			$fieldLists = explode(',',$this->plConf['importConf']['fieldLists']);
			if($result['column'] !== count($fieldLists)){
				return json(['code'=>201,'msg'=>'上传的文档的字段与设置的字段数量不一致']);
			}
			foreach ($result['data'] as $key => $value) {
				foreach ($fieldLists as $filedKey => $filedValue) {
					$temp[$filedValue] = $value[$filedKey];
				}
				$temp['lm'] = $lm;
				$temp['list_lm'] = $list_lm['list_lm']??'';
				$data[] = $temp;
			}
			try{
				$insert = Db::name($this->plConf['importConf']['table_co'])->insertAll($data);
				return json(['code'=>200,'msg'=>'导入成功，本次导入 '.($insert).' 条记录']);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>'导入失败'.$e->getMessage()]);
			}
			
		}else{
			$find = MP::where(['table_name'=>$this->importTableName,'type'=>'import'])->find();
			$category = [];
			$fieldLists  = [];
			if(!empty($find['list'])){
				$find['lists'] = unserialize($find['lists']);
				if($find['lists']['need_lm'] == true){
					if(tableExists($find['lists']['table_lm'])){
						$category = Db::name($find['lists']['table_lm'])->order('px desc,id_lm asc')->select();
					}
				}
				$fieldLists = $find['lists']['fieldLists'];
			}
			View::assign([
				'category' => $category,
				'fieldLists' => $fieldLists,
			]);
			return View::fetch();
		}
	}


	public function exportSettings(){
		if(Request::isPost()){
			$conf = Request::param();
			$conf['need_lm'] = changety($conf['need_lm']);
			$conf['fields'] = implode(',',$conf['fields']);


			if(!tableExists($conf['table_co'])){
				return json(['code'=>201,'msg'=>'表不存在']);
			}
			$data['table_name'] = $conf['table_co'];
			$data['lists'] = serialize($conf);
			$data['type'] = 'export';

			$find = MP::where(['type'=>$data['type']])->find();
			if(!empty($find)){
				try{
					$update = MP::where(['type'=>$data['type']])->save($data);
					Base::master_log('修改批量导出系统：配置文件');
					return json(['code'=>200,'msg'=>'修改成功']);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>'修改失败'.$e->getMessage()]);
				}
			}else{
				try{
					$insert = MP::insert($data);
					Base::master_log('修改批量导出系统：配置文件');
					return json(['code'=>200,'msg'=>'修改成功']);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>'修改失败'.$e->getMessage()]);
				}
			}

		}else{
			$find = MP::where(['table_name'=>$this->importTableName,'type'=>'import'])->find();
			$find['lists'] = !empty($find['lists']) ? json_decode($find['lists'],1) : '';
			$category = [];
			$fields = '';
			if(!empty($find['lists']['need_lm']) && $find['lists']['need_lm'] == 'true'){
				if(tableExists($find['lists']['table_lm'])){
					$fields = Db::getTableFields($this->exportTableName);
					$category = Db::name($find['lists']['table_lm'])->order('px desc,id_lm asc')->select();
				}
			}

			View::assign([
				'fields' => $fields,
				'category' => $category,
				'find' => $find,
			]);
			return View::fetch();
		}
	}

	public function pl_dacu_tool(){
		if(Request::isPost()){
			$data = Request::param();
			$lm = $data['lm'];
			if($this->plConf['exportConf']['need_lm'] == 'true' && $lm == ''){
				return json(['code'=>201,'msg'=>'请选择分类']);
			}
			$s_id = $data['s_id'];
			$e_id = $data['e_id'];
			$idlist = $data['idlist'];
			$s_wtime = strtotime($data['s_wtime']);
			$e_wtime = strtotime($data['e_wtime']);
			$result = [];
			if(tableExists($this->plConf['exportConf']['table_co'])){
				$query = Db::name($this->plConf['exportConf']['table_co'])->field($this->plConf['exportConf']['fields']);
				if(!empty($s_id) && !empty($e_id)){
					$query->whereBetween('id',[$s_id,$e_id]);
				}elseif (!empty($s_id)) {
					$query->where('id','>=',$s_id);
				}elseif (!empty($e_id)){
					$query->where('id','<=',$s_id);
				}
				if(!empty($idlist)){
					$query->where('id','in',$idlist);
				}
				if(!empty($s_wtime) && !empty($e_wtime)){
					$query->whereBetween('wtime',[$s_wtime,$e_wtime]);
				}elseif (!empty($s_wtime)) {
					$query->where('wtime','>=',$s_wtime);
				}elseif (!empty($e_wtime)){
					$query->where('wtime','<=',$e_wtime);
				}

				$result = $query->select()->toArray();
			}
			// dump($result);die;

			// 创建Spreadsheet对象  
			$spreadsheet = new Spreadsheet();  
			$sheet = $spreadsheet->getActiveSheet();  
			// 设置表头  
			$cell = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
			$fields = explode(',',$this->plConf['exportConf']['fields']);
			// dump($fields);die;
			foreach ($fields as $key => $value) {
				$sheet->setCellValue($cell[$key].'1', $value);
			}
			$row = 2;
			foreach ($result as $key => $value) {
				foreach ($fields as $fieldsKey => $fieldsValue) {
					if($fieldsValue == 'img_sl'){
						if(file_exists($value[$fieldsValue])){
							$drawing = new Drawing();
							$drawing->setName('Sample Image');
							$drawing->setDescription('Sample Image');
							$drawing->setPath($value[$fieldsValue]);
							$drawing->setCoordinates($cell[$fieldsKey].$row); // 设置图片插入的单元格位置
							$drawing->setWorksheet($sheet);
						}else{
							$sheet->setCellValue($cell[$fieldsKey].$row, $value[$fieldsValue]);
						}
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

		}else{
			$find = MP::where('table_name',$this->exportTableName)->find();
			$category = [];
			$fieldLists = '';
			if(!empty($find['lists'])){
				$find['lists'] = unserialize($find['lists']);
				if($find['lists']['need_lm'] == true){
					if(tableExists($find['lists']['table_lm'])){
						$category = Db::name($find['lists']['table_lm'])->order('px desc,id_lm asc')->select();
					}
				}
				$fieldLists = $find['lists']['fields'];
			}
			View::assign([
				'category' => $category,
				'fieldLists' => $fieldLists,
			]);

			return View::fetch();
		}
	}

	public function changeTable(){
		if(Request::isPost()){
			$data = Request::param();
			$table = $data['table'];
			if(!tableExists($table)){
				return json(['code'=>201,'msg'=>'表不存在']);
			}
			if(!empty($table)){
				$fields = Db::getTableFields($table);
				if(!empty($fields)){
					return json(['code'=>200,'fields'=>$fields]);
				}else{
					return json(['code'=>201,'msg'=>'参数错误']);
				}
			}else{
				return json(['code'=>201,'msg'=>'参数错误']);
			}
		}
	}

	public function deleteFile(){
		if(Request::isPost()){
			$filepath = trim(input('post.filepath'),'/');
			if(file_exists($filepath)){
				try {
					unlink($filepath);
					return '删除文件成功';
				}catch (\Exception $e){
					return $e->getMessage();
				}
			}
		}
	}

	public function uploadFile(){
		$file = request()->file();
		$files = request()->file('file');
		if(empty($file)){
			return json(['code'=>201,'msg'=>'没有文件上传']);
		}
		try {
			$extension = pathinfo($files->getOriginalName(), PATHINFO_EXTENSION);
			$fileExt = 'csv,xls,xlsx';
			if(strpos($fileExt,$extension)===false){
				return json(['code'=>201,'msg'=>'请选择一个有效的文件，支持的格式有（"'.$fileExt.'"）']);
			}
			$filesize = '20480';
			validate(['image'=>'filesize:'.$filesize.'|fileExt:'.$fileExt.''])->check($file);
			$info = \think\facade\Filesystem::disk('public')->putFile('upxls',$files,'null');
		} catch (\think\exception\ValidateException $e) {
			return json(['code'=>201,'msg'=>$e->getMessage()]);
		}
		$info = str_replace("\\","/",$info);
		$path = 'storage/';
		$file = $path.$info;
		return json(['code'=>200,'data'=>$file,'url'=>"".$file]);
	}


	// 图片上传
	public function pl_upload(){
		$file = request()->file();
		$files = request()->file('file_up');
		// dump($file);die;
		if(empty($file)){
			return json(['code'=>201,'msg'=>'没有文件上传']);
		}
		if(empty($files)){
			return json(['code'=>201,'msg'=>'没有文件上传']);
		}
		try {
			validate(['image'=>'filesize:10240|fileExt:jpg,png,gif,jpeg'])->check($file);
			$info = \think\facade\Filesystem::disk('public')->putFile('upimg',$files,'null');
		} catch (\think\exception\ValidateException $e) {
			return json(['code'=>201,'msg'=>$e->getMessage()]);
		}
		$info = str_replace("\\","/",$info);
		$path = '/storage/';
		$img = $path.$info;
		echo $img;
		exit;
	}
}

?>