<?php  
namespace app\admin\controller;

use think\facade\View;
use think\facade\Request;
use app\common\model\Order as OM;
use app\common\model\OrderItems as OIS;
use app\common\model\OrderRecord;
use app\common\model\OrderAddress;
use app\common\model\OrderNotes;
use app\common\model\OrderMessage;
use app\common\model\SetupSy as MS;
use app\common\service\OrderService;
use app\common\service\SetupSyService;

class Order extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];
	protected $sy_id = 2;
	protected OrderService $service;
	protected $route;
	protected $conf = [];

	protected function initialize() {
		parent::initialize();
		$this->route = Request::controller();
		$this->service = new OrderService();
		$this->conf = SetupSyService::getConfig($this->sy_id);
		$zt = SetupSyService::getCoZt($this->conf);
		View::assign([
			'route' => $this->route,
			'conf' => $this->conf,
			'zt'  => $zt,
		]);
	}

	//
	public function recycle(){
		if(Request::isPost()){
		}else{
			$searchItem = request()->param();
			View::assign([
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}
	
	public function recycle_make(){
		$param = request()->param();
		return $this->recycleMake($this->service,$param);
	}


	public function default(){
		if(Request::isPost()){
			$params = Request::param();
			// dump($params);
			$type = $params['type'] ?? '';

			$where = [];
			if($type == 'pay'){
				$where[] = ['pay_status','=',0];
				$where[] = ['pay_time','=',null];
			}else if($type == 'deliver'){
				$where[] = ['pay_status','=',1];
				$where[] = ['deliver_status','=',0];
			}else if($type == 'shipped'){
				$where[] = ['pay_status','=',1];
				$where[] = ['deliver_status','=',1];
			}else if($type == 'received'){
				$where[] = ['pay_status','=',1];
				$where[] = ['deliver_status','=',2];
			}
			
			$lists = OM::with([
				'userinfo',
				'orderItem' => function($query){
					$query -> with([
						'productInfo' => function($query){
							$query -> field('id,title,keyword,img_sl,param_one,price,stock,package');
						}
					]) -> field('id,order_id,goods_id,goods_name,goods_image,goods_price,quantity,specifications');
				}
			])->where($where)->order('wtime desc,id desc')->select()->toArray();

			return json(['code' => 0, 'data' => $lists, 'type' => $type]);
		}else{
			if(empty($this->conf)){
				return '<h1 style="text-align:center;padding-top:30px;">'.$this->langHtml['tip']['configSettingsFile'].'</h1>';
				die();
			}
			$where = [];
			$lists = OM::with([
				'userinfo',
				'orderItem' => function($query){
					$query -> with([
						'productInfo' => function($query){
							$query -> field('id,title,keyword,img_sl,param_one,price,stock,package');
						}
					]) -> field('id,order_id,goods_id,goods_name,goods_image,goods_price,quantity,specifications');
				}
			])->where($where)->order('wtime desc,id desc')->select()->toArray();

			$wherePay = [['pay_status','=',0]];
			$count['payCount'] = OM::where($wherePay)->count();
			$whereDeliver = [['pay_status','=',1],['deliver_status','=',0]];
			$count['deliverCount'] = OM::where($whereDeliver)->count();

			View::assign([
				'lists' => $lists,
				'count' => $count,
			]);
			return View::fetch();
		}
	}
	public function index(?array $params = []){
		$param = request()->param();
		$param['order'] = $params['order'] ?? 'wtime desc,id desc';
		$param['with'] = [
				'userinfo',
				'orderItem' => function($query){
					$query -> with([
						'productInfo' => function($query){
							$query -> field('id,title,keyword,img_sl,param_one,price,stock,package');
						}
					]) -> field('id,order_id,goods_id,goods_name,goods_image,goods_price,quantity,specifications');
				}
			];
		if(!empty($params)){
			$param = $params;
		}
		return $this->dataTable($this->service,$param);
	}

	public function setconfig(){

		if(Request::isPost()){
			$conf = Request::param();

			if(!empty($conf['co'])){
				foreach ($conf['co'] as $key => $value) {
					$conf['co'][$key] = changety($value);
				}	
			}
			$data['title'] = $conf['sy']['name'];
			$data['sy_id'] = $this->sy_id;
			$data['lang'] = $this->lang;
			$data['config'] = serialize($conf);
			$find = MS::where('sy_id',$this->sy_id)->where('lang',$this->lang)->find();

			if(!empty($find)){
				try{
					$update = MS::where('sy_id',$this->sy_id)->where('lang',$this->lang)->save($data);
					Base::master_log($this->langHtml['tip']['edit'].$conf['sy']['name'].$this->langHtml['tip']['system'].$this->langHtml['tip']['configFile']);
					return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
				}
			}else{
				try{
					$insert = MS::insert($data);
					Base::master_log($this->langHtml['tip']['edit'].$conf['sy']['name'].$this->langHtml['tip']['system'].$this->langHtml['tip']['configFile']);
					return json(['code'=>200,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['success']]);
				}catch (\Exception $e){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['edit'].$this->langHtml['tip']['fail'].$e->getMessage()]);
				}
			}

		}else{
			return View::fetch();
		}
	}


	// 
	public function make(){
		$param = Request::param();
		if(Request::isPost()){
			$act = $param['act'];
			$id = $param['id'];
			$px = $param['px']??'';
			$checkbox = $param['checkbox']??'';
			
			if($act == 'del'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				$delete = 0;
				foreach ($id as $key => $value){
					if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
						$data['pass'] = 1;
						$data['delete_time'] = date('Y-m-d H:i:s',time());
						$del = OM::where('id',$value)->save($data);
						if(!empty($del)){
							$delete ++;
						}
					}
				}
				if(empty($delete)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['noData'].$this->langHtml['tip']['del']]);
				}
				return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
			}
		}
	}

	public function edit(){
		if(Request::isPost()){

		}else{
			return View::fetch();
		}
	}

	public function orderDetail(){
		if(Request::isPost()){

		}else{
			$params = Request::param();

			$id = $params['id'] ?? '';

			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
			}

			$order = OM::with('userinfo')->where('id',$id)->find();

			$orderAddress = OrderAddress::where('order_id',$id)->find();

			$orderNotes = OrderNotes::where('order_id',$id)->find();

			$orderMessage = OrderMessage::where('order_id',$id)->find();

			$orderItems = OIS::where('order_id',$id)->select()->toArray();

			$orderRecord = OrderRecord::where('order_id',$id)->select()->toArray();

			View::assign([
				'order' => $order,
				'orderAddress' => $orderAddress,
				'orderNotes' => $orderNotes,
				'orderItems' => $orderItems,
				'orderRecord' => $orderRecord,
			]);
			return View::fetch();
		}
	}


	// 删除信息
	public function del(){
		$data = Request::param();
		$id = $data['id'];
		if(empty($id)){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['id'].$this->langHtml['tip']['cannotBeEmpty']]);
		}
		$find = OM::where('id',$id)->find();
		$data['delete_time'] = date('Y-m-d H:i:s',time());
		try{
			$update = OM::where('id',$id)->save($data);
			Base::master_log($this->langHtml['tip']['del'].$this->conf['sy']['name'].$this->langHtml['tip']['information'].'：'.$find['title']);
			return json(['code'=>200,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['success']]);
		}catch (\Exception $e){
			return json(['code'=>201,'msg'=>$this->langHtml['tip']['del'].$this->langHtml['tip']['fail'].$e->getMessage()]);
		}
	}
}