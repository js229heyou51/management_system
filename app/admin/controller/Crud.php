<?php
declare (strict_types = 1);

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Lang;
use think\facade\Request;
use app\common\service\MasterMenuService;
use app\common\service\SetupSyService;
use app\common\service\MasterActionService;


class Crud extends Base{
	
	public static $data;

	// 生成菜单
	public static function goMenu($data){
		$menuName = $data['sy']['name']??'';
		$setup_sy['title'] = $menuName;
		$setup_sy['sy_id'] = $data['sy']['id']??'';
		$setup_sy['lang'] = Lang::getLangSet();
		$lang = Lang::getLangSet();
		$conf = $data;
		// 分类表
		$conf['sy']['table_lm'] = $data['sy']['pre'].'_lm';
		// 信息表
		$conf['sy']['table_co'] = $data['sy']['pre'].'_co';
		$setup_sy['config'] = serialize($conf);
		$find = SetupSyService::getBySyId($setup_sy['sy_id']);
		if(empty($find)){
			$setupSy = SetupSyService::update($setup_sy['sy_id'],$conf);
		}else{
			$setupSy = SetupSyService::update($setup_sy['sy_id'],$conf);
		}
		$insDate = [
			'ty' => 1,
			'fid' => 0,
			'title' => $data['sy']['name'].lang('tip')['manage'],
			'pass' => 1,
			'px' => 1,
			'lang' => $lang
		];
		$memuService = new MasterMenuService();
		$insert = $memuService->create($insDate,false);
		$fid = $insert->getLastInsID();
		$MM_Data = [
			[
				'ty' => 2,
				'fid' => $fid,
				'title' => lang('tip')['system'].lang('tip')['settings'],
				'link_url' => $data['sy']['table_lm'].'/setconfig',
				'pass' => 1,
				'px' => 2,
				'lang' => $lang
			],[
				'ty' => 3,
				'fid' => $fid,
				'title' => lang('tip')['seo'].lang('tip')['settings'],
				'link_url' => 'SetupSy/edit?sy_id='.$data['sy']['id'],
				'pass' => 1,
				'px' => 3,
				'lang' => $lang
			],[
				'ty' => 1,
				'fid' => $fid,
				'title' => $data['sy']['name'].lang('tip')['category'].lang('tip')['manage'],
				'link_url' => $data['sy']['table_lm'].'/default',
				'pass' => 1,
				'px' => 4,
				'lang' => $lang
			],[
				'ty' => 1,
				'fid' => $fid,
				'title' => $data['sy']['name'].lang('tip')['information'].lang('tip')['manage'],
				'link_url' => $data['sy']['table_co'].'/default',
				'pass' => 1,
				'px' => 5,
				'lang' => $lang
			],
		];
		try {
			$insert = $memuService->batchCreate($MM_Data,false);
		}catch (\Exception $e){
			return ['code'=>201,'msg'=>$e->getMessage()];
		}
		$MA_Data = [
			[
				'fid' => $fid,
				'title' => lang('tip')['add'].lang('tip')['category'],
				'title_val' => $data['sy']['table_lm'].'_add',
				'pass' => 1,
				'px' => 1,
				'lang' => $lang
			],[
				'fid' => $fid,
				'title' => lang('tip')['del'].lang('tip')['category'],
				'title_val' => $data['sy']['table_lm'].'_del',
				'pass' => 1,
				'px' => 2,
				'lang' => $lang
			],[
				'fid' => $fid,
				'title' => lang('tip')['edit'].lang('tip')['category'],
				'title_val' => $data['sy']['table_lm'].'_edit',
				'pass' => 1,
				'px' => 3,
				'lang' => $lang
			],[
				'fid' => $fid,
				'title' => lang('tip')['view'].lang('tip')['category'],
				'title_val' => $data['sy']['table_lm'].'_default',
				'pass' => 1,
				'px' => 4,
				'lang' => $lang
			],[
				'fid' => $fid,
				'title' => lang('tip')['category'].lang('tip')['recycle'],
				'title_val' => $data['sy']['table_lm'].'_recycle',
				'pass' => 1,
				'px' => 5,
				'lang' => $lang
			],[
				'fid' => $fid,
				'title' => lang('tip')['add'].lang('tip')['information'],
				'title_val' => $data['sy']['table_co'].'_add',
				'pass' => 1,
				'px' => 6,
				'lang' => $lang
			],[
				'fid' => $fid,
				'title' => lang('tip')['del'].lang('tip')['information'],
				'title_val' => $data['sy']['table_co'].'_del',
				'pass' => 1,
				'px' => 7,
				'lang' => $lang
			],[
				'fid' => $fid,
				'title' => lang('tip')['edit'].lang('tip')['information'],
				'title_val' => $data['sy']['table_co'].'_edit',
				'pass' => 1,
				'px' => 8,
				'lang' => $lang
			],[
				'fid' => $fid,
				'title' => lang('tip')['view'].lang('tip')['information'],
				'title_val' => $data['sy']['table_co'].'_default',
				'pass' => 1,
				'px' => 9,
				'lang' => $lang
			],[
				'fid' => $fid,
				'title' => lang('tip')['information'].lang('tip')['recycle'],
				'title_val' => $data['sy']['table_co'].'_recycle',
				'pass' => 1,
				'px' => 10,
				'lang' => $lang
			]
		];
		try {
			$actionService = new MasterActionService();
			$insert = $actionService->batchCreate($MA_Data,false);
		}catch (\Exception $e){
			return ['code'=>201,'msg'=>$e->getMessage()];
		}
	}

	// 添加数据库
	public static function goAddDb($data,$cong)
	{
		if (!preg_match('/^[a-z]+$/i',$data['sy']['pre'])) return ['code' => 201, 'msg' => '表前缀格式不正确'];
		// 分类表
		$table_lm = $data['sy']['pre'].'_lm';
		// 信息表
		$table_co = $data['sy']['pre'].'_co';
		//数据验证
		if (!preg_match('/^[a-z]+_[a-z]+$/i',$table_lm)) return ['code' => 201, 'msg' => '分类表名格式不正确'];
		try {

			Db::execute('CREATE TABLE '.config('database.connections.mysql.prefix').$table_lm.'(
				`id_lm` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "id_lm",
				`fid` int(11) NULL DEFAULT NULL COMMENT "上一级",
				`list_lm` varchar(255) NULL DEFAULT NULL COMMENT "所有父级",
				`level_lm` int(11) NULL DEFAULT NULL COMMENT "所有父级",
				`url_lm` varchar(255) NULL DEFAULT NULL COMMENT "跳转链接",
				`apname_lm` varchar(255) NULL DEFAULT NULL COMMENT "页面名称",
				`title_lm` varchar(255) NULL DEFAULT NULL COMMENT "标题",
				`f_body_lm` text NULL DEFAULT NULL COMMENT "简要介绍",
				`z_body_lm` text NULL DEFAULT NULL COMMENT "详细介绍",
				`ym_tit` text NULL DEFAULT NULL COMMENT "seo标题",
				`ym_key` text NULL DEFAULT NULL COMMENT "seo关键词",
				`ym_des` text NULL DEFAULT NULL COMMENT "seo介绍",
				`img_sl_lm` varchar(255) NULL DEFAULT NULL COMMENT "图片",
				`pic_sl_lm` varchar(255) NULL DEFAULT NULL COMMENT "图片2",
				`add_xx` varchar(4) NULL DEFAULT NULL COMMENT "分类是否可以添加信息",
				`add_xia` varchar(4) NULL DEFAULT NULL COMMENT "是否有下一级分类",
				`con_att` varchar(4) NULL DEFAULT NULL COMMENT "分类属性",
				`tuijian` tinyint(1) NULL DEFAULT NULL COMMENT "推荐",
				`hot` tinyint(1) NULL DEFAULT NULL COMMENT "热门",
				`pass` tinyint(1) NULL DEFAULT NULL COMMENT "屏蔽",
				`px` int(11) NULL DEFAULT NULL COMMENT "排序",
				`ip` varchar(50) NULL DEFAULT NULL COMMENT "ip",
				`lang` varchar(50) NULL DEFAULT NULL COMMENT "语言",
				`wtime` timestamp NULL DEFAULT NULL COMMENT "创建时间",
				`delete_time` timestamp NULL DEFAULT NULL COMMENT "删除时间",
				PRIMARY KEY (`id_lm`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT="'.$data['sy']['name'].'";
				');
		}catch (\Exception $e){
			return ['code'=>201,'msg'=>$e->getMessage()];
		}

		if (!preg_match('/^[a-z]+_[a-z]+$/i',$table_co)) return ['code' => 201, 'msg' => '信息表名格式不正确'];
		try {
			Db::execute('CREATE TABLE '.config('database.connections.mysql.prefix').$table_co.'(
				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "id",
				`lm` int(11) NULL DEFAULT NULL COMMENT "上一级",
				`list_lm` varchar(255) NULL DEFAULT NULL COMMENT "所有父级",
				`link_url` varchar(255) NULL DEFAULT NULL COMMENT "跳转链接",
				`apname` varchar(255) NULL DEFAULT NULL COMMENT "页面名称",
				`title` varchar(255) NULL DEFAULT NULL COMMENT "标题",
				`keyword` varchar(255) NULL DEFAULT NULL COMMENT "关键词",
				`f_body` text NULL DEFAULT NULL COMMENT "简要介绍",
				`z_body` text NULL DEFAULT NULL COMMENT "详细介绍",
				`t_body` text NULL DEFAULT NULL COMMENT "其他介绍",
				`g_body` text NULL DEFAULT NULL COMMENT "其他介绍",
				`ym_tit` text NULL DEFAULT NULL COMMENT "seo标题",
				`ym_key` text NULL DEFAULT NULL COMMENT "seo关键词",
				`ym_des` text NULL DEFAULT NULL COMMENT "seo介绍",
				`img_sl` varchar(255) NULL DEFAULT NULL COMMENT "图片",
				`pic_sl` varchar(255) NULL DEFAULT NULL COMMENT "图片2",
				`fil_sl` varchar(255) NULL DEFAULT NULL COMMENT "文件",
				`vid_sl` varchar(255) NULL DEFAULT NULL COMMENT "视频",
				`ding` tinyint(1) NULL DEFAULT NULL COMMENT "置顶",
				`tuijian` tinyint(1) NULL DEFAULT NULL COMMENT "推荐",
				`hot` tinyint(1) NULL DEFAULT NULL COMMENT "热门",
				`pass` tinyint(1) NULL DEFAULT NULL COMMENT "屏蔽",
				`read_num` int(11) NULL DEFAULT NULL COMMENT "浏览次数",
				`px` int(11) NULL DEFAULT NULL COMMENT "排序",
				`ip` varchar(50) NULL DEFAULT NULL COMMENT "ip",
				`lang` varchar(50) NULL DEFAULT NULL COMMENT "语言",
				`wtime` timestamp NULL DEFAULT NULL COMMENT "创建时间",
				`delete_time` timestamp NULL DEFAULT NULL COMMENT "删除时间",
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT="'.$data['sy']['name'].'";
				');
		}catch (\Exception $e){
			return ['code'=>201,'msg'=>$e->getMessage()];
		}
	}

	// CRUD生成
	public static function goCrud($data)
	{
		//参数
		self::$data = $data;
		$ds = DIRECTORY_SEPARATOR;
		//路径
		$tpl = root_path().'extend'.$ds.'tpl'.$ds;
		$view_lm = $data['sy']['pre'].'_lm';
		$view_co = $data['sy']['pre'].'_co';
		$commom = root_path().'app'.$ds.'common'.$ds;
		$crud = [];
		$appPath = app('http')->getName();
		$arr = [
			app_path().'controller'.$ds.ucfirst(self::$data['sy']['pre']).'Lm.php' => self::getController($tpl.'controllerlm.tpl'),
			app_path().'controller'.$ds.ucfirst(self::$data['sy']['pre']).'Co.php' => self::getController($tpl.'controllerco.tpl'),
			$commom.'model'.$ds.ucfirst(self::$data['sy']['pre']).'Lm.php' => self::getModel($tpl.'modellm.tpl'),
			$commom.'model'.$ds.ucfirst(self::$data['sy']['pre']).'Co.php' => self::getModel($tpl.'modelco.tpl'),
			$commom.'service'.$ds.ucfirst(self::$data['sy']['pre']).'CategoryService.php' => self::getService($tpl.'servicelm.tpl'),
			$commom.'service'.$ds.ucfirst(self::$data['sy']['pre']).'Service.php' => self::getService($tpl.'serviceco.tpl'),

			root_path().'view'.$ds.$appPath.$ds.$view_lm.$ds.'default.html' => self::getDefault($tpl.$ds.'lm'.$ds.'default.tpl'),
			root_path().'view'.$ds.$appPath.$ds.$view_lm.$ds.'edit.html' => self::getAddLm($tpl.$ds.'lm'.$ds.'edit.tpl'),
			root_path().'view'.$ds.$appPath.$ds.$view_lm.$ds.'recycle.html' => self::getRecycle($tpl.$ds.'lm'.$ds.'recycle.tpl'),
			root_path().'view'.$ds.$appPath.$ds.$view_lm.$ds.'setconfig.html' => self::getSet($tpl.$ds.'lm'.$ds.'setconfig.tpl'),
			root_path().'view'.$ds.$appPath.$ds.$view_co.$ds.'default.html' => self::getDefault($tpl.$ds.'co'.$ds.'default.tpl'),
			root_path().'view'.$ds.$appPath.$ds.$view_co.$ds.'edit.html' => self::getAddCo($tpl.$ds.'co'.$ds.'edit.tpl'),
			root_path().'view'.$ds.$appPath.$ds.$view_co.$ds.'recycle.html' => self::getRecycle($tpl.$ds.'co'.$ds.'recycle.tpl'),
		];
		$crud = array_merge($crud,$arr);

		
		foreach ($crud as $k=>$v) {
			@mkdir(dirname($k), 0755, true);
			@file_put_contents($k, $v);
		}
		return true;
	}
	// 控制器
	public static function getController($tpl)
	{
		return str_replace(['{{$sy_id}}','{{$table}}'],
		[self::$data['sy']['id'],ucfirst(self::$data['sy']['pre'])],file_get_contents($tpl));
	}
	// 模型
	public static function getModel($tpl)
	{
		return str_replace(['{{$table}}'],
		[ucfirst(self::$data['sy']['pre'])],file_get_contents($tpl));
	}
	// 模型
	public static function getService($tpl)
	{
		return str_replace(['{{$table}}'],
		[ucfirst(self::$data['sy']['pre'])],file_get_contents($tpl));
	}

	// 模板
	public static function getDefault($tpl)
	{
		return str_replace([],[],file_get_contents($tpl));
	}

	// 模板
	public static function getRecycle($tpl)
	{
		return str_replace([],[],file_get_contents($tpl));
	}
	// 模板
	public static function getAddLm($tpl)
	{
		return str_replace([],[],file_get_contents($tpl));
	}
	// 
	public static function getAddCo($tpl)
	{
		return str_replace([],[],file_get_contents($tpl));
	}
	// 
	public static function getSet($tpl)
	{
		return str_replace([],[],file_get_contents($tpl));
	}
}




?>