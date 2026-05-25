<?php  
declare (strict_types = 1);

namespace app\index\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use app\common\model\BrandCo;
use app\common\model\ProCo;
use app\common\model\ParamCo;
use app\common\model\Pl_info;

class Bom extends Base{

	public $nav = 'bom';

	public function initialize(){
		parent::initialize();
		View::assign([
			'nav' => $this->nav,
		]);
	}
	
	public function index(){
		return View::fetch();
	}
	public function search(){

		$data = Request::param();
		$file = $data['file']??'';
		$list = [];
		if($file){
			$data = importExecl(ltrim($file,'/'));
			$list = $data['data'];
		}
		$content = $data['content']??'';
		if($content){
			$tempArray = explode("\r\n",$content);
			foreach ($tempArray as $key => $value) {
				if(!empty($value)){
					if(strpos($value,"\t")!==false){
						$list[] = explode("\t",$value);
					}else{
						$list[] = explode(" ",$value);
					}
				}
			}
		}
		$dataList = [];
		$count = 0;
		foreach ($list as $key => $value) {
			$dataList[$key]['title'] = $value[0]??'';
			$num = $value[1]??0;
			$dataList[$key]['num'] = $num;
			$proInfo = ProCo::where($this->where)->where('title',$dataList[$key]['title'])->order($this->orderCo)->limit(1)->find();
			$dataList[$key]['proInfo'] = $proInfo;
			$dataList[$key]['paramBrand'] = '';
			$dataList[$key]['priceList'] = [];
			$dataList[$key]['price'] = 0;
			$dataList[$key]['totalNum'] = 0;
			$dataList[$key]['total'] = 0;
			if(!empty($proInfo)){
				$dataList[$key]['totalNum'] = $num;
				if($proInfo['stock'] <= $num){
					$dataList[$key]['totalNum'] = $proInfo['stock'];
				}
				$param_json = $proInfo['param_json'] ? json_decode($proInfo['param_json'],true) : [];
				$paramArr = ParamCo::with('profile')->where([['id','in',implode(',', $param_json)]])->order($this->orderCo)->select();
				$paramList = [];
				foreach ($paramArr as $k => $v) {
					$v['param_name'] = $v['title']??'';
					$v['param_id'] = $v['id']??'';
					$v['value'] = $v['profile']['id_lm']??'';
					$v['title'] = $v['profile']['title_lm']??'';
					$paramList[] = $v;
				}
				foreach ($paramList as $k => $v) {
					if($v['title'] == '品牌'){
						$dataList[$key]['paramBrand'] = $v['param_name'];
					}
				}
				$priceList = Pl_info::where($this->where)->where([['sy_id','=',3],['pl_id','=',$proInfo['id']]])->field('title as num,price')->order('px desc,id asc')->select();
				if(empty($priceList)){
					return json(['code'=>201,'msg'=>'存在没定价产品，请联系管理员']);
				}
				$price = 0;
				foreach ($priceList as $k => $v) {
					if($num > $v['num']){
						$price = $v['price'];
					}
				}
				$dataList[$key]['price'] = $price;
				$dataList[$key]['total'] = $num * $price;
				$dataList[$key]['priceList'] = $priceList;
				$count ++;
			}
		}
		View::assign([
			'count' => $count,
			'dataList' => $dataList,
		]);

		return View::fetch();
	}

	public function upload(){
		return View::fetch();
	}

	public function uploadFile(){
		$file = Request::file();
		$files = Request::file('file');

		if(empty($file)){
			return json(['code'=>201,'msg'=>'没有文件上传']);
		}
		if(empty($files)){
			return json(['code'=>201,'msg'=>'没有文件上传']);
		}
		try {
			validate(['image'=>'filesize:10240|fileExt:jpg,png,gif,jpeg'])->check($file);
			$info = \think\facade\Filesystem::disk('public')->putFile('upxls',$files,'null');
		} catch (\think\exception\ValidateException $e) {
			return json(['code'=>201,'msg'=>$e->getMessage()]);
		}
		$info = str_replace("\\","/",$info);
		$path = '/storage/';
		$filename = $path.$info;
		return json(['code'=>200,'msg'=>'上传成功','file'=>$filename]);
	}
}