<?php  
namespace app\index\controller;

use think\facade\View;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Cache;
use app\common\model\PersonCart as PC;
use app\common\model\Person;
use app\common\model\PersonMessage as PM;
use app\common\model\ProLm;
use app\common\model\HomeCo;
use app\common\model\SetupGl;

class Base extends \app\BaseController{
	public $userInfo = [];

	public $where = [];
	public $orderLm = 'px desc,id_lm asc';
	public $orderCo = 'ding desc,px desc,id desc';
	public $lang = '';
	public $cong = [];
	public $timeout = 3600;
	
	public function initialize(){
		$this->lang = Lang::getLangSet();
		$this->where[] = ['lang','=',$this->lang];
		$this->timeout = 3600;
		$token = Session::get('token');
		$cartCount = 0;
		$infoCount = 0;
		$messageCount = 0;
		$this->cong = SetupGl::where($this->where)->find();
		$icon = '';
		if($this->cong['icon']){
			$iconIds = $this->cong['icon'];
			if(!empty($iconIds) && is_array($iconIds)){
				$iconFind = \app\common\model\Gallery::where('status',1)->where('id','in',$iconIds)->field('path')->orderField('id', $iconIds)->find();
				if($iconFind){
					$icon = $iconFind['path'] ?? '';
				}
			}
			
		}
		if(!empty($token)){
			if(!$this->userInfo){
				$this->userInfo = Person::where('token',$token)->field('id,username,rename,img_sl,sex,email,address,z_body,discounts,phone,login_num,ltime,etime')->find();
				if($this->userInfo){
					$cartCount = PC::where('user_id',$this->userInfo['id'])->count();
					$messageCount = PM::where('user_id',$this->userInfo['id'])->where('check',0)->count();
				}
			}
		}

		View::assign([
			'userInfo' => $this->userInfo,
			'cartCount' => $cartCount,
			'messageCount' => $messageCount,
			'cong' => $this->cong,
			'icon' => $icon,
		]);
		$this->header();
		$this->footer();
	}

	protected function header(){
		$lists = $this->getTree();
		$productCategory = [];
		$i = 0;
		$k = 0;
		foreach ($lists as $key => $value) {
			$productCategory[$k][$i] = $value;
			$i ++;
			if($i%2 == 0){
				$i = 0;
				$k ++;
			}
		}
		View::assign([
			'productcategory' => $productCategory,
		]);
	}
	protected function getTree($fid = 0){
		$list = Prolm::where('fid',$fid)->where($this->where)->order($this->orderLm)->select();
		$result = [];
		foreach ($list as $item) {
			$item['children'] = $this->getTree($item['id_lm']);
			$result[] = $item;
		}
		return $result;
	}
	protected function footer(){
		$link = [];
		$footer['about'] = HomeCo::where($this->where)->order($this->orderCo)->where('lm',3)->select();
		$footer['guide'] = HomeCo::where($this->where)->order($this->orderCo)->where('lm',4)->select();
		$footer['pay'] = HomeCo::where($this->where)->order($this->orderCo)->where('lm',5)->select();
		$footer['sale'] = HomeCo::where($this->where)->order($this->orderCo)->where('lm',6)->select();
		View::assign([
			'link' => $link,
			'footer' => $footer,
		]);
	}
}
?>