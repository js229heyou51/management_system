<?php  
declare (strict_types = 1);

namespace app\index\controller;

use think\facade\View;
use think\facade\Db;
use think\facade\Request;
use think\Response;
use think\facade\Config;
use think\exception\HttpResponseException;
use app\common\model\PersonCart;
use app\common\model\PersonCollect;
use app\common\model\ProCo;
use app\common\model\ProLm;
use app\common\model\ParamCo;
use app\common\model\PersonAddress as PA;
use app\common\model\Invoice;
use app\common\model\Order;
use app\common\model\OrderItems;
use app\common\model\OrderRecord;
use app\common\model\OrderAddress;
use app\common\model\OrderNotes;
use app\index\validate\Address as VA;

class Cart extends Base{


	use \app\index\common\Util;
	protected $middleware = ['UserCheck'];

	public function initialize(){
		parent::initialize();
	}

	public function index(){

		// $cartList = PersonCart::where('uid',$this->userInfo['id'])->select();
		$cartList = PersonCart::with(['product' => function($query){
			$query->with(['profile',
				'priceLists' => function($query){
					$query->order('id asc');
				}])->field('id,lm,title,stock,img_sl,param_json');
		}])->withCount(['collect'])->where('user_id',$this->userInfo['id'])->order('wtime desc,id desc')->select()->toArray();
		$cartList = getParamLists($cartList);

		View::assign([
			'cartList' => $cartList,
		]);

		return View::fetch();
	}

	public function addCart(){
		$params = Request::param();

		$pid = $params['pid']??'';
		$num = $params['num']??1;
		$user_id = $this->userInfo['id'];
		$data['wtime'] = date('Y-m-d H:i:s');
		$data['user_id'] = $user_id;

		$find = PersonCart::where([['pid','=',$pid],['user_id','=',$user_id]])->find();
		if($find){
			$num = $find['num'] + $num;
		}
		
		$product = ProCo::with('priceLists')->where('id',$pid)->order('px desc,id asc')->find()->toArray();

		$priceList = [];
		foreach ($product['priceLists'] as $key => $value) {
			if($num >= $value['title']){
				$price = $value['price'];
				continue;
			}
		}

		$data['pid'] = $pid;
		$data['num'] = $num;
		$data['price'] = $price;
		$data['total'] = $price * $num;
		$data['price_lists'] = $params['price_lists'];

		try {
			if($find){
				$updateData = [
					'num' => $num,
					'price' => $price,
				];
				$update = PersonCart::where([['pid','=',$pid],['user_id','=',$user_id]])->update($updateData);
			}else{

				$insert = PersonCart::insert($data);
			}
			$cartCount = PersonCart::where('user_id',$user_id)->count();

			return json(['code' => 200,'msg'=>'添加成功','cartCount'=>$cartCount]);
		} catch (\Exception $e) {
			return json(['code' => 201,'msg'=>'添加失败'.$e->getMessage()]);
		}

	}

	public function makeCart(){
		$data = Request::param();
		$act = $data['act']??'';

		if(empty($act)){
			return json(['code'=>201,'msg'=>'参数错误']);
		}

		if($act === 'delCart'){
			$id = $data['id']??'';
			if(empty($id)){
				return json(['code'=>201,'msg'=>'参数错误']);
			}
			try{
				$del = PersonCart::where('id',$id)->update(['delete_time'=>date('Y-m-d H:i:s')]);
				return json(['code'=>200,'msg'=>'删除成功']);
			}catch(Exception $e){
				return json(['code'=>201,'msg'=>'删除失败'.$e->getMessage()]);
			}
		}

		if($act === 'addCart'){
			$updateData['pid'] = $data['pid']??'';
			if(!$updateData['pid']){
				return json(['code'=>201,'msg'=>'参数错误']);
			}
			$updateData['num'] = $data['num']??0;

			$find = PersonCart::where([['pid','=',$updateData['pid']],['user_id','=',$this->userInfo['id']]])->find();
			$priceStr = $find['price_lists'];
			if($priceStr){
				$price = 0;
				$priceList = json_decode($priceStr,true);
				foreach ($priceList as $key => $value) {
					if($updateData['num'] >= $value['num']){
						$price = $value['price'];
						continue;
					}
				}
			}


			$updateData['price'] = $price;

			$updateData['total'] = $updateData['num'] * $updateData['price'];

			try {
				$update = PersonCart::where([['pid','=',$updateData['pid']],['user_id','=',$this->userInfo['id']]])->update($updateData);
				return json(['code'=>200,'msg'=>'操作成功']);
			} catch (\Exception $e) {
				return json(['code'=>201,'msg'=>'操作失败'.$e->getMessage()]);
			}
		}
	}

	public function settlement(){
		$data = Request::param();
		$pid = $data['pid']??'';
		$idStr = $data['idStr']??'';
		if(empty($pid) && empty($idStr)){
			$this->error('参数错误',null,0);
		}
		$cartList = [];
		$totalPrice = 0;
		$totalNum = 0;
		if(!empty($pid)){
			$product = ProCo::with(['priceLists'=>function($query){
				$query->order('id asc');
			}])->where('id',$pid)->find();
			foreach ($product['priceLists'] as $k => $v) {
				if($v['title']<=1){
					$price = $v['price'];
					break;
				}
			}
			$totalNum = 1;
			$totalPrice = $price;
			$cartList[0]['product'] = $product;
			$cartList[0]['paramLists'] = [];
			$cartList[0]['num'] = 1;
			$cartList[0]['price'] = $price;
			$cartList[0]['total'] = $totalPrice ;
			$paramJson = $product['param_json']??'';
			$param_json = $paramJson? json_decode($paramJson,true) : [];
			$paramArr = ParamCo::with('profile')->where([['id','in',implode(',', $param_json)]])->order('ding desc,px desc,id desc')->select();
			$paramList = [];
			foreach ($paramArr as $k => $v) {
				$paramList[$k]['param_name'] = $v['title']??'';
				$paramList[$k]['param_id'] = $v['id']??'';
				$paramList[$k]['value'] = $v['profile']['id_lm']??'';
				$paramList[$k]['title'] = $v['profile']['title_lm']??'';
			}
			$cartList[0]['paramLists'] = $paramList;
		}
		if(!empty($idStr)){
			$where = [['id','in',$idStr],['user_id','=',$this->userInfo['id']]];
			$cartList = PersonCart::with('product')->where($where)->order('wtime desc,id desc')->select();
			$cartList = getParamLists($cartList);
			$totalPrice = PersonCart::where($where)->sum('num*price');
			$totalNum = PersonCart::where($where)->count();
		}
		if(!empty($cartList)){
			$this->error('商品为空',null,0);
		}

		$addressList = PA::order('id desc,wtime desc')->select()->toArray();

		$invoiceList = Invoice::where('user_id',$this->userInfo['id'])->order('wtime desc,id desc')->select();

		$valueAdded = 0.13;
		$freightList = [];
		$freightPrice = 0;
		$cartIdStr = $idStr;
		$allPrice = round(($totalPrice + $freightPrice),2);
		View::assign([
			'pid' => $pid,
			'addressList' => $addressList,
			'invoiceList' => $invoiceList,
			'freightList' => $freightList,
			'cartList' => $cartList,
			'totalPrice' => $totalPrice,
			'totalNum' => $totalNum,
			'valueAdded' => $valueAdded,
			'freightPrice' => $freightPrice,
			'cartIdStr' => $cartIdStr,
			'allPrice' => $allPrice,
		]);
		return View::fetch();
	}

	public function address(){
		return  PA::where('user_id',$this->userInfo['id'])->order('id desc,wtime desc')->select()->toArray();
	}

	public function addAddress(){
		$data = Request::param();
		if(Request::isPost()){
			$validate = new VA;
			if(!$validate->scene('address')->check($data)){
				return json(['code'=>201,'msg'=>$validate->getError()]);
			}
			$data['user_id'] = $this->userInfo['id'];
			$data['lang'] = $this->lang;
			$data['wtime'] = date('Y-m-d H:i:s');

			$data['type'] = $data['type'] ?? 0;

			try {
				if($data['id']){

					$update = PA::update($data);
					$addressList = $this->address();
					return json(['code'=>200,'msg'=>'修改成功','data' => $addressList]);
				}else{
					$insert = PA::insert($data);
					$addressList = $this->address();
					return json(['code'=>200,'msg'=>'新增成功','data' => $addressList]);
				}
				
			} catch (\Exception $e) {
				return json(['code'=>201,'msg'=>'新增失败'.$e->getMessage()]);
			}
		}
	}

	public function editAddress(){
		$params = Request::param();
		$id = $params['id']??'';
		if(empty($id)){
			return json(['code'=>201,'msg'=>'参数错误']);
		}
		$addressFind = PA::where('user_id',$this->userInfo['id'])->where('id',$id)->field('id,province,city,district,address,rename,phone,type')->order('type desc, id desc')->find();

		if(empty($addressFind)){
			return json(['code'=>201,'msg'=>'地址不存在']);
		}
		return json(['code'=>200,'msg'=>'','data'=>$addressFind]);
	}

	public function getInvoiceList(){
		return Invoice::where('user_id',$this->userInfo['id'])->order('id desc,wtime desc')->select()->toArray();
	}

	public function invoice(){
		$params = Request::param();

		$params['user_id'] = $this->userInfo['id'];
		$params['lang'] = $this->lang;
		$params['wtime'] = date('Y-m-d H:i:s');

		try {
			if($params['id']){
				$update = Invoice::update($params);
				$invoiceList = $this->getInvoiceList();
				return json(['code'=>200,'msg'=>'修改成功','data'=>$invoiceList]);
			}else{
				$insert = Invoice::insert($params);
				$invoiceList = $this->getInvoiceList();
				return json(['code'=>200,'msg'=>'新增成功','data'=>$invoiceList]);
			}
		} catch (\Exception $e) {
			return json(['code'=>201,'msg'=>'新增失败'.$e->getMessage()]);
		}
	}

	public function editInvoice(){
		$params = Request::param();
		$id = $params['id']??'';
		if(empty($id)){
			return json(['code'=>201,'msg'=>'参数错误']);
		}
		$invoiceFind = Invoice::where('user_id',$this->userInfo['id'])->where('id',$id)->order('wtime desc, id desc')->find();

		if(empty($invoiceFind)){
			return json(['code'=>201,'msg'=>'地址不存在']);
		}
		return json(['code'=>200,'msg'=>'','data'=>$invoiceFind]);
	}


	public function addOrder(){
		$params = Request::param();

		$cartIdStr = $params['cartIdStr'] ?? '';
		$pid = $params['pid'] ?? '';
		if(empty($cartIdStr) && empty($pid)){
			return json(['code'=>201,'msg'=>'参数错误']);
		}

		if(!empty($cartIdStr)){
			$cartLists = PersonCart::with([
				'product' => function($q){
					$q->with(['profile']);
				}
			])->where('user_id',$this->userInfo['id'])->where('id','in',$cartIdStr)->order('wtime desc,id desc')->select()->toArray();
			if(empty($cartLists)){
				return json(['code'=>201,'msg'=>'购物车为空，请添加']);
			}
		}

		// 立即购买
		if(!empty($pid)){
			$product = ProCo::with(['priceLists'=>function($query){
				$query->order('id asc');
			}])->where('id',$pid)->find();
			foreach ($product['priceLists'] as $k => $v) {
				if($v['title']<=1){
					$price = $v['price'];
					break;
				}
			}
			$totalNum = 1;
			$totalPrice = $price;
			$cartLists[0]['product'] = $product;
			$cartLists[0]['paramLists'] = [];
			$cartLists[0]['num'] = 1;
			$cartLists[0]['price'] = $price;
			$cartLists[0]['total'] = $totalPrice ;
			$paramJson = $product['param_json']??'';
			$param_json = $paramJson? json_decode($paramJson,true) : [];
			$paramArr = ParamCo::with('profile')->where([['id','in',implode(',', $param_json)]])->order('ding desc,px desc,id desc')->select();
			$paramList = [];
			foreach ($paramArr as $k => $v) {
				$paramList[$k]['param_name'] = $v['title']??'';
				$paramList[$k]['param_id'] = $v['id']??'';
				$paramList[$k]['value'] = $v['profile']['id_lm']??'';
				$paramList[$k]['title'] = $v['profile']['title_lm']??'';
			}
			$cartLists[0]['paramLists'] = $paramList;
		}


		$order['num'] = 0;
		$order['price'] = $params['totalPrice'];
		$order['order_sn'] = Order::generateOrderSn();
		$order['user_id'] = $this->userInfo['id'];
		$order['address_id'] = $params['addressId'];
		$order['need_invoice'] = $params['needInvoice']??'0';
		$order['invoice_id'] = $params['invoiceId']??'';
		$order['coupon_amount'] = 0;
		$order['points'] = $params['totalPrice'] * 10;
		$order['postage'] = $params['freightPrice']??'0';
		$order['user_discounts'] = $this->userInfo['discounts'];
		$order['member_benefits'] = 0;
		$order['wtime'] = date("Y-m-d H:i:s");
		$order['ip'] = Request::ip();
		$order['remarks'] = $params['remarks']??'';
		$order['pay_amount'] = $order['price'] + $order['postage'];

		try{
			$insertOrder = Order::insert($order);
			$order_id = Order::getLastInsID();

			foreach ($cartLists as $k => $v) {
				$param_json = $v['product']['param_json']? json_decode($v['product']['param_json'],true) :[];
				$paramList = [];
				foreach ($param_json as $key => $val) {
					$children = ParamCo::with(['profile'])->where($this->where)->where('id',$val)->order($this->orderCo)->find();
					if(isset($children['profile']['title_lm']) && isset($children['title'])){
						$paramList[] = $children['profile']['title_lm'].'：'.$children['title'];
					}
				}
				$specifications = implode(' ', $paramList);
				$order['num'] += $v['num'];
				$orderItem['order_id'] = $order_id;
				$orderItem['goods_id'] = $v['product']['id'];
				$orderItem['goods_name'] = $v['product']['title'];
				$orderItem['goods_image'] = $v['product']['img_sl'];
				$orderItem['goods_price'] = $v['price'];
				$orderItem['quantity'] = $v['num'];
				$orderItem['specifications'] = $specifications;
				$orderItem['total_price'] = $v['num'] * $v['price'];
				$orderItem['created_at'] = date('Y-m-d H:i:s');
				$insertItem = OrderItems::insert($orderItem);
				$stock = $v['product']['stock'] - $v['num'];
				$updateProduct = ProCo::where('id',$v['product']['id'])->update(['stock'=>$stock]);
				if(!empty($cartIdStr)){
					$delCart = PersonCart::where('id',$v['id'])->update(['delete_time'=>date('Y-m-d H:i:s')]);
				}
			}
			$addressFind = PA::where('id',$order['address_id'])->find();

			$orderAddress['order_id'] = $order_id;
			$orderAddress['name'] = $addressFind['rename'];
			$orderAddress['province'] = $addressFind['province'];
			$orderAddress['city'] = $addressFind['city'];
			$orderAddress['district'] = $addressFind['district'];
			$orderAddress['address'] = $addressFind['address'];
			$orderAddress['phone'] = $addressFind['phone'];
			$orderAddress['wtime'] = date('Y-m-d H:i:s');

			$insertOrderAddress = OrderAddress::insert($orderAddress);

			$updateOrder = Order::where('id',$order_id)->update(['num'=>$order['num']]);
			
			// 订单记录
			$recordDate['order_id'] = $order_id;
			$recordDate['record'] = '订单生成';
			$recordDate['wtime'] = date('Y-m-d H:i:s');
			$orderRecord = OrderRecord::insert($recordDate);

			// 订单备注
			$notesData['order_id'] = $order_id;
			$notesData['user_id'] = $this->userInfo['id'];
			$notesData['notes'] = $params['remarks'];
			$notesData['wtime'] = date('Y-m-d H:i:s');
			$orderNotes = OrderNotes::insert($notesData);

			return json(['code'=>200,'msg'=>'提交成功','orderId'=>$order_id]);
		}catch (\Exception $e) {
			return json(['code'=>201,'msg'=>'生成订单失败'.$e->getMessage()]);
		}

		// return View::fetch();
	}

	public function payOrder(){
		$params = Request::param();
		$orderId = $params['orderId'] ?? '';
		if(empty($orderId)){
			$this->error('参数错误！',null,0);
		}
		$orderInfo = Order::where('user_id',$this->userInfo['id'])->where('id',$orderId)->find();
		if(!empty($orderId)){
			$this->error('订单不存在或已删除',null,0);
		}

		$startTime = strtotime($orderInfo['wtime']) + 3600;

		$address = OrderAddress::where('order_id',$orderId)->find();

		View::assign([
			'orderInfo'=>$orderInfo,
			'address'=>$address,
			'startTime'=>$startTime,
		]);
		return View::fetch();
	}


	public function collect(){
		$params = Request::param();
		$pid = $params['id'] ?? '';
		if(empty($pid)){
			return json(['code'=>201,'msg'=>'参数错误！']);
		}
		$where = [['user_id','=',$this->userInfo['id']]];
		$where[] = ['pid','=',$pid];
		$find = PersonCollect::where($where)->find();

		if($find){
			$updateData['delete_time'] = date('Y-m-d H:i:s');
			try{
				$update = PersonCollect::where($where)->update($updateData);
				PersonCollect::destroy($find['id'],true);
				return json(['code'=>200,'msg'=>'取消收藏','text'=>'移入收藏']);
			}catch(Exception $e){
				return json(['code'=>201,'msg'=>'操作失败'.$e->getMessage()]);
			}
		}else{
			$insertData['pid'] = $pid;
			$insertData['user_id'] = $this->userInfo['id'];
			$insertData['wtime'] = date('Y-m-d H:i:s');
			try{
				$inser = PersonCollect::insert($insertData);
				return json(['code'=>200,'msg'=>'成功收藏','text'=>'取消收藏']);
			}catch(Exception $e){
				return json(['code'=>201,'msg'=>'操作失败'.$e->getMessage()]);
			}
		}

	}
}
?>
