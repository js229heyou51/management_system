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
use app\common\model\ProCo;
use app\common\model\SetupSy as MS;

class Order extends Base{

	protected $middleware = ['AdminCheck','AdminPermission'];
	protected $sy_id = 2;
	protected $route;
	protected $tableLmName;
	protected $tableCoName;
	protected $conf = [];

	protected function initialize() {
		parent::initialize();
		$this->route = Request::controller();
		$lists = MS::where('sy_id',$this->sy_id)->where('lang',$this->lang)->find();
		if(!empty($lists['config'])){
			$this->conf = unserialize($lists['config']);
		}
		$zt = false;
		if(!empty($this->conf)){
			$this->tableCoName = $this->conf['sy']['table_co']??'';
			if((isset($this->conf['co']['tuijian']) && $this->conf['co']['tuijian'] == true) ||
				(isset($this->conf['co']['hot']) && $this->conf['co']['hot'] == true) ||
				(isset($this->conf['co']['pass']) && $this->conf['co']['pass'] == true) ){
				$zt = true;
			}else{
				$zt = false;
			}
		}
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

			$searchItem = Request::param();
			$keyword = $searchItem['keyword']??'';
			$where = [];
			if(!empty($keyword)){
				$where[] = ["title", "like", "%" . $keyword . "%"];
			}

			$count = OM::onlyTrashed()->with([
				'userinfo',
				'orderItem' => function($query){
					$query -> with([
						'productInfo' => function($query){
							$query -> field('id,title,keyword,img_sl,param_one,price,stock,package');
						}
					]) -> field('id,order_id,goods_id,goods_name,goods_image,goods_price,quantity,specifications');
				}
			])->where($where)->count();
			$lists = OM::onlyTrashed()->with([
				'userinfo',
				'orderItem' => function($query){
					$query -> with([
						'productInfo' => function($query){
							$query -> field('id,title,keyword,img_sl,param_one,price,stock,package');
						}
					]) -> field('id,order_id,goods_id,goods_name,goods_image,goods_price,quantity,specifications');
				}
			])->where($where)->order('wtime desc,id desc')->select()->toArray();
			View::assign([
				'lists' => $lists,
				'searchItem' => $searchItem,
			]);
			return View::fetch();
		}
	}
	public function recycle_make(){
		$param = Request::param();
		if(Request::isPost()){
			$act = $param['act'];
			$id = $param['id']??'';
			$checkbox = $param['checkbox'] ?? '';
			if(empty($id)){
				return json(['code'=>201,'msg'=>$this->langHtml['tip']['noData']]);
			}
			if($act == 'recovery'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				try{
					foreach ($id as $key => $value){
						if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
							OM::onlyTrashed()->find($value)->restore();
						}
					}
					return ['code'=>200,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['success']];
				}catch (\Exception $e){
					return ['code'=>201,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['fail'].$e->getMessage()];
				}
			}
			if($act == 'remove'){
				if(empty($checkbox)){
					return json(['code'=>201,'msg'=>$this->langHtml['tip']['selectData']]);
				}
				try{
					foreach ($id as $key => $value){
						if(isset($checkbox[$value]) && $checkbox[$value] == 'on'){
							$delFind = OM::onlyTrashed()->with(['orderItem'])->where('id',$value)->find();
							if($delFind['pay_status'] == 0){
								foreach ($delFind['orderItem'] as $k => $v) {
									$proFind = ProCo::where('id',$v['goods_id'])->field('stock')->find();
									$proData['stock'] = $proFind['stock'] + $v['quantity'];
									ProCo::where('id',$v['goods_id'])->update($proData);
								}
							}
							OM::destroy($value,true);
							OIS::destroy(['order_id' => $value],true);
						}
					}
					return ['code'=>200,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['success']];
				}catch (\Exception $e){
					return ['code'=>201,'msg'=>$this->langHtml['tip']['operate'].$this->langHtml['tip']['fail'].$e->getMessage()];
				}
			}
		}
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