<?php  
namespace app\common\service;

use app\common\model\FeedCo;
use app\common\trait\CrudTrait;
use think\facade\Log;
use think\facade\Request;
use think\facade\Lang;
use app\common\model\ArticleLm;
use app\common\model\WebCo;
use app\common\model\ArticleCo;
use app\common\model\FeedRecord;
use GuzzleHttp\Client;

class FeedService
{
	use CrudTrait;

	protected $categoryService;

	public function __construct(){
		$this->model = new FeedCo();  // 给 Trait 中的 $model 赋值
		$this->categoryService = new FeedCategoryService();
	}

	public function ceshi($id){

		Log::info('定时文章发布任务执行中...'.$id, ['time' => date('Y-m-d H:i:s')]);
	}

	public function createWeb($id = ''){
		$param = Request::param();
		$id = $param['id'] ?? $id;

		if(empty($id)){
			Log::info('投喂文章：'.Lang::get('tip')['noData']);
			return json(['code'=>201,'msg'=>Lang::get('tip')['noData']]);
		}
		$find = $this->model->where('id',$id)->field('id,title,article_str,web_str,num')->where('pass','=',1)->find();
		if(empty($find)){
			Log::info('投喂文章：该任务已屏蔽');
			return json(['code'=>400,'msg'=>'该任务已屏蔽']);
		}
		$num = $find['num'] ?? 1;
		$title = $find['title'] ?? '';
		if(empty($title)){
			Log::info('投喂文章：'.Lang::get('tip')['noData']);
			return json(['code'=>400,'msg'=>Lang::get('tip')['noData']]);
		}
		$where[] = ['lang','=', Lang::getLangSet()];
		if($find['web_str']){
			// 有多少个网站需要发布文章
			$web = WebCo::with([
				'profile' => function($query){
					$query->field('id_lm,title_lm');
				}
			])->where('id','in',$find['web_str'])->field('lm,tlm,title,tfile,table_co,table_lm,link_url,web_url,id,release,folder_path')->where('pass','=',1)->select()->toArray();
			$count = WebCo::where('id','in',$find['web_str'])->where('pass','=',1)->count();
			if($count <= 0){
				Log::info('投喂文章：'.Lang::get('tip')['noData']);
				return json(['code'=>400,'msg'=>Lang::get('tip')['noData']]);
			}
			
			// 随机获取 $num 篇文章
			$article = ArticleCo::with('profile')->where('lm','=',$find['article_str'])->where('pass','=',1)->where($where)->where('read_num',0)->orderRaw('rand()')->limit($num)->select()->toArray();
			$artCount = ArticleCo::where('lm','=',$find['article_str'])->where('pass','=',1)->where('read_num',0)->where($where)->orderRaw('rand()')->limit($num)->count();
			if($artCount <= 0){
				Log::info('投喂文章：当前没有可用文章');
				return json([
					'code' => 400,
					'msg' => '当前没有可用文章',
				]);
			}

			$recordTotal = FeedRecord::where('feed_id',$id)->whereTime('wtime','today')->count();

			if($recordTotal >= $num){
				Log::info('投喂文章：该任务已超出今日发布数量');
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
								Log::info('投喂文章：链接不能为空');
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
								Log::info('投喂文章：标题不能为空');
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
									'lang'     => Lang::getLangSet(),
									'ip'       => Request::ip(),
									'pass'     => 1,
									'account'  => $v['profile']['title_lm'].$v['title'],
									'px'	   => 100,
								];
								$feed = FeedRecord::insert($data);
								$updateData['read_num'] = $article[$artKey]['read_num'] + 1;
								$update = ArticleCo::where('id',$article[$artKey]['id'])->update($updateData);
								Log::info('投喂文章：'.$article[$artKey]['title'].'到网站:'.$title);
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
									'lang'     => Lang::getLangSet(),
									'ip'       => Request::ip(),
									'pass'     => 0,
									'account'  => $v['profile']['title_lm'].$v['title'],
									'px'	   => 100,
								];
								$feed = FeedRecord::insert($data);
								Log::info('投喂文章：'.$article[$artKey]['title'].'到网站:'.$title);
								return json([
									'code' => 201,
									'msg' => $jsonResponse['msg'],
								]);
							}
						}catch(\Exception $e){
							Log::info($e->getMessage());
							return json([
								'code' => 402,
								'msg' => $e->getMessage(),
							]);
						}
					}
				}
				
			}
			if($webReleaseCount >= $count){
				Log::info('已超出今日发布数量');
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
}
?>