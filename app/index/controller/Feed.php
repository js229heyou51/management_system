<?php  
declare (strict_types = 1);

namespace app\index\controller;

use think\facade\Request;
use app\common\model\ArticleLm;
use app\common\model\ArticleCo;

class Feed extends Base{

	public function index(){

		$params = Request::param();
		if(Request::isPost()){
			$data['title'] = $params['row']['title'];
			$data['z_body'] = $params['row']['content'];
			$data['f_body'] = mb_substr(strip_tags($data['z_body']),0,150).'...';

			$data['px'] = 100;
			$data['lm'] = $params['lm'] ?? 1;
			$data['wtime'] = !empty($data['wtime']) ? $data['wtime'] : date('Y-m-d H:i:s',time());
			$data['ding'] = 0;
			$data['pass'] = 1;
			$data['hot'] = 0;
			$data['tuijian'] = 0;
			$data['ip'] = Request::ip();
			$data['lang'] = $this->lang;
			if(!empty($data['lm'])){
				$list_lm = ArticleLm::where('id_lm',$data['lm'])->find();
				$data['list_lm'] = $list_lm['list_lm'];
			}

			try{
				$insert = ArticleCo::insert($data);
				return json(['code'=>200,'msg'=>'添加成功']);
			}catch (\Exception $e){
				return json(['code'=>201,'msg'=>$e->getMessage()]);
			}
		}

		

		// $article = Article::where('lm',$lm)->

		// dump($params);
	}

}

?>