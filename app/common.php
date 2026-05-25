<?php
use think\facade\Filesystem;  
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use app\common\model\ParamCo;

// 应用公共文件
//转换类型
if (!function_exists('changety')) {
	function changety($str){
		if ($str=='true'||$str==1){
			return true;
		}elseif($str=='false'||$str==0){
			return false;
		}else{
			return false;
		}
	}
}

/**
 * 为字符串或数组元素添加反斜杠
 */
if (!function_exists('addslash')) {
	function addslash($str){
		if(!is_array($str)){
			//如果传进来不不是数组
			$str = addslashes($str); //那么进行转义
			return $str;
		}else{
			return array_map("addslash",$str);
		}
	}
}

/**
 * 修改网站配置文件
 */
if (!function_exists('set_web')) {
	function set_web($data = [])
	{
		$str = "<?php\r\n/**\r\n * 系统配置文件\r\n */\r\nreturn [\r\n";
		foreach ($data as $key => $value) {
			if(is_array($value)){
				$str .= get_arr_tree($key,$value);
			}else{
				// 检查 $value 是否为布尔值，并相应地格式化  
				if (is_bool($value)) {  
					$value = $value ? 'true' : 'false';  
				} else{
					$value = '\''.$value.'\'';  
				}
				$str .= "\t'$key' => $value,";
				$str .= "\r\n";
			}
		}
		$str .= '];';
		@file_put_contents(config_path().'web.php', $str);
	}
}
if (!function_exists('get_arr_tree')) {
	/**
	 * 递归配置数组
	 */
	function get_arr_tree($key,$data,$level="\t")
	{
		$i = "$level'$key' => [\r\n";
		foreach ($data as $k => $v) {
			if(is_array($v)){
				$i .= get_arr_tree($k,$v,$level."\t");
			}else{
				// 检查 $value 是否为布尔值，并相应地格式化  
				if (is_bool($v)) {  
					$v = $v ? 'true' : 'false';  
				} else{
					$v = '\''.$v.'\'';  
				}
				$i .= "$level\t'$k' => $v,";
				$i .= "\r\n";
			}
		}
		return  $i."$level".'],'."\r\n";
	}
}

if (!function_exists('categoryTree')) {
	// 无限级分类
	function categoryTree($array=[] , $fid=0 , $i="",$lm=''){
		if($i == ''){
			$i = '• ';
		}else if($i == '• '){
			$i = '┗━━ ';
		}else{
			$i = '┗━━'.$i;
		}
		foreach ($array as $key => $value) {
			if($value['fid'] == $fid && $value['add_xia']=='yes'){
				echo '<option value="'.$value['id_lm'].'" '.($lm == $value['id_lm'] ? 'selected' : '').'>'.$i.$value['title_lm'].' </option>';
				categoryTree($array,$value['id_lm'],$i,$lm);
			}
		}
	}
}


if (!function_exists('listTree')) {
	// 分类
	function listTree($array=[] , $i="",$conf=[],$zt=false,$url,$type=0){
		if($i == ''){
			$i = '&nbsp;&nbsp; ';
		}else{
			$i = '&nbsp;&nbsp;&nbsp;'.$i;
		}
		foreach ($array as $key => $value) {
			echo '<tr>';
			echo '<td><div class="layui-input-inline"><input type="text" class="layui-input" value="'.$value['px'].'"></div></td>';
			echo '<td align="center"><span>'.$value['id_lm'].'</span></td>';
			echo '<td><span>'.$i.''.$value['title_lm'].'</span></td>';
			if($zt == true){
				echo '<td align="center">';
				if($conf['lm']['tuijian_lm'] == 'true'){
					echo '<input '.($value['tuijian'] == 1 ? 'checked' : '').' type="checkbox" name="tuijian" lay-skin="switch" lay-text="推荐|推荐" lay-filter="tuijian" data-id_lm="'.$value['id_lm'].'"> '; 
				}
				if ($conf['lm']['hot_lm'] == 'true'){
					echo '<input '.($value['hot'] == 1 ? 'checked' : '').' type="checkbox" name="hot" lay-skin="switch" lay-text="热门|热门" lay-filter="hot" data-id_lm="'.$value['id_lm'].'"> '; 
				}
				if($conf['lm']['pass_lm'] == 'true'){
					echo '<input '.($value['pass'] == 0 ? 'checked' : '').' type="checkbox" name="pass" lay-skin="switch" lay-text="屏蔽|屏蔽" lay-filter="pass" data-id_lm="'.$value['id_lm'].'"> '; 
				}
				echo '</td>';
			}
			echo '<td align="center">';
			if($type == 0){
				echo '<div class="layui-inline"><button type="button" class="layui-btn layui-btn-primary layui-btn-xs '.(($value['con_att'] == 3) ? 'layui-btn-disabled' : '').'" '.(($value['con_att'] == 1 || $value['con_att'] == 2) ? 'onclick="edit(\''.$url.'/edit?id_lm='.$value['id_lm'].'\')"' : '').' >';
				echo '<i class="layui-icon layui-icon-edit"></i>编辑';
				echo '</button></div> ';
				if ($conf['sy']['need_lm'] == 'true'){
					echo '<div class="layui-inline"><button type="button" class="layui-btn layui-btn-primary layui-btn-xs layui-bg-red '.(($value['con_att'] == 2 || $value['con_att'] == 3) ? 'layui-btn-disabled' : '').'" '.(($value['con_att'] == 1) ? 'onclick="del(\''.$url.'/del?id_lm='.$value['id_lm'].'\')"' : '').' >';
					echo '<i class="layui-icon layui-icon-delete"></i>删除';
					echo '</button></div>';
				}
			}
			echo '</td>';
			echo '</tr>';
			listTree($value['children'],$i,$conf,$zt,$url);
		}
	}
}

if(!function_exists('tableExists')){
	function tableExists($tableName){  
		// 假设你的数据库默认连接是 MySQL  
		$sql = "SELECT * FROM information_schema.tables WHERE table_schema = ? AND table_name = ?";  
		$result = \think\facade\Db::query($sql, [config('database.connections.mysql.database'), $tableName]);  

		// 检查是否找到了表  
		return count($result) > 0;  
	} 
}

if(!function_exists('checkusername')){
	/**
	 * 检查字母+数字-
	 *
	 * @parame  $pid     字符串
	 */
	function checkusername($pid){
		if(!is_array($pid)){
			return preg_match("/^(\w)+$/",$pid);
		}else{
			$str=true;
			foreach ($pid as $v){
				if (!checkusername($v)){
					$str=false;
					break;
				}
			}
			return $str;
		}
	}
}

if(!function_exists('checkpassword')){
	/**
	 * 检查非汉字
	 *
	 * @parame  $pid     字符串
	 */
	function checkpassword($pid){
		if (!is_array($pid)){
			return preg_match("/^[\w~`!@#\$%\^&\\*\(\)\-\+=\[\]\{\}\|\\<,>\.\?\/]+$/",$pid);
		}else{
			$str=true;
			foreach ($pid as $v){
				if (!checkpassword($v)){
					$str=false;
					break;
				}
			}
			return $str;
		}
	}
}

if(!function_exists('showseo')){
	/**
	 * 显示seo信息
	 *
	 * @parame  $sy_id  系统的id  
	 * @parame  $aparr  外部自定义的seo
	 */
	function showseo($sy_id='',$aparr=''){
		$lang = think\facade\Lang::getLangSet();
		$cong = app\common\model\SetupGl::where('lang',$lang)->find();
		$rs=array();
		$title='';
		$k=false;
		$show_html='';
		$ym_tit='';$ym_key='';$ym_des='';
		if(!empty($aparr)){
			$rs=$aparr;
			if (!empty($aparr['title'])){
				$title=$aparr['title'];
			}
			if (!empty($aparr['title_lm'])){
				$title=$aparr['title_lm'];
			}
			//如果列表页的rs为空就读取本系统seo
			if($aparr['ym_tit']==''&&checknum($sy_id)){
				$rs= think\facade\Db::name('setup_sy')->where('sy_id',$sy_id)->find();
			}
		//其他页面
		}else{
			//读取本系统seo
			if(checknum($sy_id)){
				$rs= think\facade\Db::name('setup_sy')->where('sy_id',$sy_id)->find();
			}
		}
		//如果上面的为空就读取全局seo
		if(empty($rs)||($rs&&$rs['ym_tit']=='')){
			$rs=$cong;
			$k=true;
		}
		if ($rs){
			$ym_tit=$rs['ym_tit'];
			$ym_key=$rs['ym_key'];
			$ym_des=$rs['ym_des'];
			//外部传入的字符串
			if (!empty($aparr['title'])){
				$ym_tit=$aparr['title'].(($ym_tit!='')?'--':'').$ym_tit;
			//如果调用全局的seo，那么就用 单页、详细页、列表页的“信息标题”+全局的标题
			}elseif (!empty($aparr['title_lm'])){
				$ym_tit=$aparr['title_lm'].(($ym_tit!='')?'--':'').$ym_tit;
			//如果调用全局的seo，那么就用 单页、详细页、列表页的“信息标题”+全局的标题
			}elseif (!empty($aparr) && !is_array($aparr) && !is_object($aparr)){
				$ym_tit=$aparr.(($ym_tit!='')?'--':'').$ym_tit;
			//如果调用全局的seo，那么就用 单页、详细页、列表页的“信息标题”+全局的标题
			}elseif (!empty($title)&&$k==true){
				$ym_tit=$title.(($ym_tit!='')?'--':'').$ym_tit;

			}
		}
		
		$show_html .= '<title>'.$ym_tit.'</title>'."\n";
		$show_html .= '<meta name="keywords" content="'.$ym_key.'"/>'."\n";
		$show_html .= '<meta name="description" content="'.$ym_des.'"/>'."\n";
		return $show_html;
	}
}

if(!function_exists('checknum')){
	/**
	 * 检查数字
	 *
	 * @parame  $pid     数字
	 */
	function checknum($pid){
		if (!is_array($pid)){
			return preg_match("/^[0-9]+$/",$pid);
		}else{
			$str=true;
			foreach ($pid as $v){
				if (!checknum($v)){
					$str=false;
					break;
				}
			}
			return $str;
		}
	}
}

if(!function_exists('html')){
	/**
 * 格式化标签 例如：把空格转为&nbsp;
 *
 * @parame  $str     字符串
 */
	function html($str = ''){
		if(!empty($str)){
			if(!is_array($str)){
				$str = str_replace('  ', '&nbsp;', $str);
				$str = str_replace('<', '&lt', $str);
				$str = str_replace('>', '&gt', $str);
				$str = str_replace("\"", '&quot;', $str);
				$str = str_replace("'", '&rsquo;', $str);
				$str = str_replace("\r\n", '<br />', $str);
				$str = str_replace("\r", '<br />', $str);
				$str = str_replace("\n", '<br />', $str);
				return $str;
			}else{
				return array_map("html",$str);
			}
		}
	}
}

if(!function_exists('importExecl')){
	// 导入
	function importExecl($savePath){
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
}

if(!function_exists('getRandom')){
	function getRandom($param){
		$str="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$key = "";
		for($i=0;$i<$param;$i++){
			$randomIndex = mt_rand(0, 32);
			$key .= $str[$randomIndex];    //生成php随机数
		}
		return $key;
	}
}

if(!function_exists('getParamLists')){
	function getParamLists($productLists = []){
		if(empty($productLists)){
			return [];
		}
		$arr = [];
		foreach ($productLists as $key => $value) {
			$paramJson = $value['product']['param_json']??'';
			if(empty($paramJson)){
				return $productLists;
			}
			$param_json = $paramJson? json_decode($paramJson,true) : [];
			$paramArr = ParamCo::with('profile')->where([['id','in',implode(',', $param_json)]])->order('ding desc,px desc,id desc')->select()->toArray();
			$paramList = [];
			foreach ($paramArr as $k => $v) {
				$paramList[$k]['param_name'] = $v['title']??'';
				$paramList[$k]['param_id'] = $v['id']??'';
				$paramList[$k]['value'] = $v['profile']['id_lm']??'';
				$paramList[$k]['title'] = $v['profile']['title_lm']??'';
			}
			$value['paramLists'] = $paramList;
			$arr[] = $value;
		}
		return $arr;
	}
}

if(!function_exists('translate')){
	//翻译入口
	function translate($query, $from, $to){
		$args = array(
			'q' => $query,
			'appid' => env('API_TRANS_APP_ID'),
			'salt' => rand(10000,99999),
			'from' => $from,
			'to' => $to,
		);
		$args['sign'] = buildSign($query, env('API_TRANS_APP_ID'), $args['salt'], env('API_TRANS_SEC_KEY'));
		$ret = call(env('API_TRANS_URL'), $args);
		$ret = json_decode($ret, true);

		$res = $ret['trans_result'][0]['dst'] ?? [];

		return $res; 
	}
}

if(!function_exists('buildSign')){
	function buildSign($query, $appID, $salt, $secKey){
		$str = $appID . $query . $salt . $secKey;
		$ret = md5($str);
		return $ret;
	}
}
if(!function_exists('call')){
	function call($url, $args=null, $method="post", $testflag = 0, $timeout = 10, $headers=array()){
		$timeout = env('API_TRANS_CURL_TIMEOUT');
		$ret = false;
		$i = 0; 
		while($ret === false) {
			if($i > 1) break;
			if($i > 0) {
				sleep(1);
			}
			$ret = callOnce($url, $args, $method, false, $timeout, $headers);
			$i++;
		}
		return $ret;
	}
}

if(!function_exists('callOnce')){
	function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = CURL_TIMEOUT, $headers=array()){
		$ch = curl_init();
		if($method == "post") 
		{
			$data = convert($args);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_POST, 1);
		}
		else 
		{
			$data = convert($args);
			if($data) 
			{
				if(stripos($url, "?") > 0) 
				{
					$url .= "&$data";
				}
				else 
				{
					$url .= "?$data";
				}
			}
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if(!empty($headers)) 
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		if($withCookie)
		{
			curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
		}
		$r = curl_exec($ch);
		curl_close($ch);
		return $r;
	}
}

if(!function_exists('convert')){
	function convert(&$args){
		$data = '';
		if (is_array($args))
		{
			foreach ($args as $key=>$val)
			{
				if (is_array($val))
				{
					foreach ($val as $k=>$v)
					{
						$data .= $key.'['.$k.']='.rawurlencode($v).'&';
					}
				}
				else
				{
					$data .="$key=".rawurlencode($val)."&";
				}
			}
			return trim($data, "&");
		}
		return $args;
	}
}

if(!function_exists('getDatabaseTables')){
	/**
	 * 获取数据库所有表信息
	 * @param bool $withStructure 是否包含表结构
	 * @return array
	 */
	function getDatabaseTables($withStructure = false)
	{
		try {
			$tables = think\facade\Db::connect()->getTables();
			$result = [];

			
			foreach ($tables as $table) {
				$tableInfo = ['name' => $table];
				
				if ($withStructure) {
					// 获取表详细信息
					$status = think\facade\Db::query("SHOW TABLE STATUS LIKE '{$table}'");
					if ($status) {
						// $tableInfo['comment'] = $status[0]['Comment'];
						// $tableInfo['engine'] = $status[0]['Engine'];
						$tableInfo['rows'] = $status[0]['Rows'];
						// $tableInfo['create_time'] = $status[0]['Create_time'];
					}
					
					// 获取字段信息
					$tableInfo['fields'] = think\facade\Db::query("DESC `{$table}`");
				}
				
				$result[] = $tableInfo;
			}
			
			return $result;
		} catch (\Exception $e) {
			return ['error' => $e->getMessage()];
		}
	}
}

if(!function_exists('getStateWidth')){
	function getStateWidth($conf){
		$stateWidth = 0;
		if((isset($conf['co']['ding']) && $conf['co']['ding']) && $stateWidth == 0){
			$stateWidth += 87;
		}else if((isset($conf['co']['ding']) && $conf['co']['ding']) && $stateWidth != 0){
			$stateWidth += 61;
		}else{
			$stateWidth += 0;
		}

		if((isset($conf['co']['tuijian']) && $conf['co']['tuijian']) && $stateWidth == 0){
			$stateWidth += 87;
		}else if((isset($conf['co']['tuijian']) && $conf['co']['tuijian']) && $stateWidth != 0){
			$stateWidth += 61;
		}else{
			$stateWidth += 0;
		}

		if((isset($conf['co']['hot']) && $conf['co']['hot']) && $stateWidth == 0){
			$stateWidth += 87;
		}else if((isset($conf['co']['hot']) && $conf['co']['hot']) && $stateWidth != 0){
			$stateWidth += 61;
		}else{
			$stateWidth += 0;
		}

		if((isset($conf['co']['pass']) && $conf['co']['pass']) && $stateWidth == 0){
			$stateWidth += 99;
		}else if((isset($conf['co']['pass']) && $conf['co']['pass']) && $stateWidth != 0){
			$stateWidth += 73;
		}else{
			$stateWidth += 0;
		}

		return $stateWidth;
	}
}

if(!function_exists('getLmStateWidth')){
	function getLmStateWidth($conf){
		$stateWidth = 0;

		if(empty($conf)){
			return $stateWidth;
		}

		if($conf['lm']['tuijian_lm'] && $stateWidth == 0){
			$stateWidth += 87;
		}else if($conf['lm']['tuijian_lm'] && $stateWidth != 0){
			$stateWidth += 61;
		}else{
			$stateWidth += 0;
		}

		if($conf['lm']['hot_lm'] && $stateWidth == 0){
			$stateWidth += 87;
		}else if($conf['lm']['hot_lm'] && $stateWidth != 0){
			$stateWidth += 61;
		}else{
			$stateWidth += 0;
		}

		if($conf['lm']['pass_lm'] && $stateWidth == 0){
			$stateWidth += 99;
		}else if($conf['lm']['pass_lm'] && $stateWidth != 0){
			$stateWidth += 73;
		}else{
			$stateWidth += 0;
		}

		return $stateWidth;
	}
}


if(!function_exists('getGalleryList')){
	function getGalleryList($galleryIds = [])
	{
		if (empty($galleryIds)) {
			return collect([]);
		}
		
		return \app\common\model\Gallery::whereIn('id', $galleryIds)
			->orderRaw('FIELD(id, ' . implode(',', $galleryIds) . ')')
			->select();
	}
}

if(!function_exists('getVideoList')){
	function getVideoList($videoIds = [])
	{
		if (empty($videoIds)) {
			return collect([]);
		}
		
		return \app\common\model\Video::whereIn('id', $videoIds)
			->orderRaw('FIELD(id, ' . implode(',', $videoIds) . ')')
			->select();
	}
}