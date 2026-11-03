<?php  
namespace app\admin\controller;

use think\facade\View;
use app\common\service\SetupSyService;
use app\common\service\ArticleService as IS;
use app\common\service\ArticleCategoryService as CS;
use think\ai\Client;
use app\common\trait\ParseListParams;
use Parsedown;

class ArticleCo extends Base{
	use ParseListParams;

	protected $middleware = ['AdminCheck','AdminPermission'];

	protected $sy_id = 8;
	protected $conf = [];
	protected IS $service;
	protected CS $categoryService;


	/**
	 * [initialize description]
	 * @return view [description]
	 */
	protected function initialize(){
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

	/**
	 * [recycle 回收站]
	 * @return View                 [description]
	 */
	public function recycle(){
		if(request()->isPost()){
			
		}else{
			$searchItem = request()->param();
			$category = $this->categoryService->getCategoryList();
			View::assign([
				'category' => $category,
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}

	/**
	 * [recycle_make 回收站操作]
	 * @return array          [description]
	 */
	public function recycle_make(){
		$param = request()->param();
		return $this->recycleMake($this->service,$param);
	}

	/**
	 * [default 信息首页]
	 * @return View                  [description]
	 */
	public function default(){
		if(request()->isPost()){
			$param = request()->param();
			return $this->index($param);
		}else{
			if(empty($this->conf)){
				return '<h1 style="text-align:center;padding-top:30px;">'.$this->langHtml['tip']['configSettingsFile'].'</h1>';
				die();
			}
			$searchItem = request()->param();
			$category = $this->categoryService->getCategoryList([],['info']);
			View::assign([
				'category' => $category,
				'searchItem' => $searchItem
			]);
			return View::fetch();
		}
	}

	public function index(?array $params = []){
		$params = $this->parseListParams(['lm']);
		$params['with'] = 'profile';
		return $this->dataTable($this->service,$params);
	}

	/**
	 * [add 添加]
	 */
	public function add(){
		if(request()->isPost()){
			$data = request()->param();
			return $this->addCommon($this->service,$data,$this->conf,$this->sy_id);
		}else{
			$category = $this->categoryService->getCategoryList();
			View::assign([
				'category' => $category,
			]);
			return View::fetch('edit');
		}
	}

	/**
	 * [edit 编辑]
	 * @return View                  [description]
	 */
	public function edit(){
		$data = request()->param();
		if(request()->isPost()){
			return $this->editCommon($this->service,$data,$this->conf);
		}else{
			$id = $data['id']??'';
			if(empty($id)){
				return '<h1 style="text-align:center;padding-top:30px;">'.$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty'].'</h1>';
				die();
			}
			$find = $this->service->getById($id);
			$category = $this->categoryService->getCategoryList();
			View::assign([
				'category' => $category,
				'find' => $find,
			]);
			return View::fetch('edit');
		}
	}

	/**
	 * [del 删除]
	 * @return array          [description]
	 */
	public function del(){
		$data = request()->param();
		return $this->delCommon($this->service,$data,$this->conf);
	}

	/**
	 * [make 操作]
	 * @return array [description]
	 */
	public function make(){
		$params = request()->param();
		return $this->statusMake($this->service,$params,$this->conf);
	}


	public function createArticle(){

		$data = request()->param();

		$client = new Client(env('THINKAI_KEY'));

		$role = $this->admin->username ?? 'user';

		$tit = $data['content'] ?? '';
		$is_auth = $data['is_auth'] ?? '';
		if(empty($tit)){
			return json(['code' => 201,'msg'=> '参数错误']);
		}

		$content = '用“'.$tit.'”生成一篇通俗易懂的文章';

		if($is_auth == 1){
			$content .= '，然后在文章里面适当位置，添加网易外贸通经销商--广州合优网络科技有限公司的介绍和电话020-82591888';
		}
		if($is_auth == 2){
			$content .= '，然后在文章里面适当位置，添加咨询电话400-669-6649';
		}

		$result = $client->chat()->completions([
			'model' => env('THINKAI_MODEL'),
			'messages' => [
				['role' => 'user', 'content' => $content],
			],
			'stream'=>false,
		]);

		if($result['finish_reason'] == 'length'){
			return json(['code' => 201,'msg'=> '到达 tokens 长度上限']);
		}

		$Parsedown = new Parsedown();
		$Parsedown->setSafeMode(true); // 这一行是关键
		$safeHtml = $Parsedown->text($result['message']['content']); // $safeHtml 是安全的 HTML

		$title = $this->getH1Text($safeHtml);
		if($title == ''){
			$title = $tit;
		}
		$content = $this->removeH1Tags($safeHtml);
		$brief = $this->getBrief($content, 150);

		if($result['finish_reason'] == 'stop'){
			return json(['code' => 200, 'reply' => ['title' => $title, 'brief' => $brief, 'content' => $content ]]);
		}
	}

	private function getH1Text(?string $html) {
		$dom = new \DOMDocument();
		@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		$xpath = new \DOMXPath($dom);
		$nodes = $xpath->query('//h1');
		if ($nodes->length > 0) {
			return trim($nodes->item(0)->textContent); // 只取第一个 h1
		}
		return '';
	}

	private function removeH1Tags(?string $html) {
		$dom = new \DOMDocument();
		@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		$xpath = new \DOMXPath($dom);
		$nodes = $xpath->query('//h1');
		foreach ($nodes as $node) {
			$node->parentNode->removeChild($node);
		}
		// 提取 body 内的 HTML
		$body = $dom->getElementsByTagName('body')->item(0);
		$innerHtml = '';
		foreach ($body->childNodes as $child) {
			$innerHtml .= $dom->saveHTML($child);
		}
		return trim(ltrim($innerHtml));
	}

	private function getBrief(?string $html, $length = 150)
    {
        // 1. 去除所有 HTML 标签，得到纯文本
        $text = strip_tags($html);

        // 2. 将 HTML 实体转回普通字符（如 &nbsp; 转为空格）
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 3. 去除多余空白（多个空格、换行等合并为一个空格）
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // 4. 按字符数截取（对中文、英文混合友好）
        if (mb_strlen($text, 'UTF-8') > $length) {
            $text = mb_substr($text, 0, $length, 'UTF-8') . '…'; // 加省略号
        }

        return $text;
    }
}

?>