<?php  
declare (strict_types = 1);

namespace app\index\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Request;
use app\common\model\PersonCollect;
use app\common\model\PersonMessage;
use app\common\model\PersonAddress;
use app\common\model\Order;
use app\common\model\ProCo;
use app\common\model\ParamCo;
use app\common\model\Pl_info;
use app\common\model\Person as PI;

class Person extends Base{

	// use \app\index\common\Util;
	protected $middleware = ['UserCheck'];
	public $ny = 'index';

	public function initialize(){
		parent::initialize();
	}

	public function index(){
		$this->ny = 'index';

		$where = [['user_id','=',$this->userInfo['id']]];

		$collectCount = PersonCollect::where($where)->count();
		$infoCount = PersonMessage::where($where)->count();
		$wherePay = [['pay_status','=',0]];
		$count['pay'] = Order::where($where)->where($wherePay)->count();
		$whereDeliver = [
			['pay_status','=',1],
			['deliver_status','=',0]
		];
		$count['deliver'] = Order::where($where)->where($whereDeliver)->count();
		$whereReceive = [
			['pay_status','=',1],
			['deliver_status','=',1],
			['receive_status','=',0],
		];
		$count['receive'] = Order::where($where)->where($whereReceive)->count();
		$whereRefund = [
			['pay_status','=',1],
			['deliver_status','=',1],
			['receive_status','=',1],
			['refund_status','=',0],
		];
		$count['refund'] = Order::where($where)->where($whereRefund)->count();

		$productLists = ProCo::where($this->where)->order($this->orderCo)->where('tuijian',1)->limit(5)->select();

		foreach ($productLists as $key => $value) {

			$paramArr = [];
			$param_json = $value['param_json']? json_decode($value['param_json'],true) :[];
			$paramArr = ParamCo::with('profile')->where([['id','in',implode(',', $param_json)]])->order($this->orderCo)->select();
			$paramList = [];
			foreach ($paramArr as $k => $v) {
				$v['param_name'] = $v['title']??'';
				$v['param_id'] = $v['id']??'';
				$v['value'] = $v['profile']['id_lm']??'';
				$v['title'] = $v['profile']['title_lm']??'';
				$paramList[] = $v;
			}

			// $priceList = Pl_info::where($this->where)->where([['sy_id','=',3],['pl_id','=',$value['id']]])->field('title as num,price')->order('px desc,id asc')->select();

			$value['paramList'] = $paramList;
			// $value['priceList'] = $priceList;
			// $value['priceJSON'] = json_encode($priceList);
			$productList[] = $value;
		}

		View::assign([
			'ny' => $this->ny,
			'collectCount' => $collectCount,
			'infoCount' => $infoCount,
			'count' => $count,
			'productList' => $productList,
		]);

		return View::fetch();
	}

	public function message(){
		$this->ny = 'message';

		$user_id = $this->userInfo['id'];
		$infoList = PersonMessage::where('user_id','=',$user_id)->select();

		View::assign([
			'ny' => $this->ny,
			'infoList' => $infoList,
		]);
		return View::fetch();
	}

	public function showMessage(){

		$param = Request::param();
		$id = $param['id'] ?? '';

		$find = PersonMessage::where('id','=',$id)->find();
		$updata = [
			'check' => 1
		];
		try{
			$update = PersonMessage::where('id','=',$id)->update($updata);
			$user_id = $this->userInfo['id'];
			$infoList = PersonMessage::where('user_id','=',$user_id)->select();
		}catch(\Exception $e){
			
			return ['code'=>201,'msg'=>$e->getMessage()];
		}

		View::assign([
			'find' => $find
		]);

		return View::fetch('showMessage');
	}

	public function order(){
		$params = Request::param();
		$where = [['user_id','=',$this->userInfo['id']]];
		$wherePay = [['pay_status','=',0]];
		$count['pay'] = Order::where($where)->where($wherePay)->count();
		$whereDeliver = [
			['pay_status','=',1],
			['deliver_status','=',0]
		];
		$count['deliver'] = Order::where($where)->where($whereDeliver)->count();
		$whereReceive = [
			['pay_status','=',1],
			['deliver_status','=',1],
			['receive_status','=',0],
		];
		$count['receive'] = Order::where($where)->where($whereReceive)->count();
		$whereRefund = [
			['pay_status','=',1],
			['deliver_status','=',1],
			['receive_status','=',1],
			['refund_status','=',0],
		];
		$count['refund'] = Order::where($where)->where($whereRefund)->count();

		if(isset($params['zt'])){
			if($params['zt'] == 1){
				$where[] = $wherePay;
			}else if($params['zt'] == 2){
				$where[] = $whereDeliver;
			}else if($params['zt'] == 3){
				$where[] = $whereReceive;
			}else if($params['zt'] == 4){
				$where[] = $whereRefund;
			}
		}
		$orderList = Order::with(['orderItem'])->withCount([
			'orderItem' => function($query,&$alias){
				$alias = 'proOrderConut';
			}])->where($where)->paginate(5);

		$page = $orderList->render();
		$this->ny = 'order';
		View::assign([
			'ny' => $this->ny,
			'count' => $count,
			'orderList' => $orderList,
			'zt' => $params['zt']??'0',
			'page' => $page,
		]);
		return View::fetch();
	}

	public function collect(){
		$this->ny = 'collect';

		$collectList = PersonCollect::with(['product'])->where('user_id',$this->userInfo['id'])->paginate(5);

		$page = $collectList->render();

		View::assign([
			'ny' => $this->ny,
			'collectList' => $collectList,
			'page' => $page,
		]);
		return View::fetch();
	}

	public function information(){
		return View::fetch();
	}


	public function avatar(){
		$params = Request::param();
		$base64_image_content = $params['image'] ?? '';
		//匹配出图片的格式
		if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
			$type = $result[2];
			$new_file = 'storage/uphead/'.$this->userInfo['username'];
			if (!is_dir($new_file)){
				//第三个参数是“true”表示能创建多级目录，iconv防止中文目录乱码
				$res = mkdir(iconv("UTF-8", "GBK", $new_file),0777,true);
			}
			$new_file = $new_file.'/'.time().".{$type}";
			if(file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
				$new_file = '/'.$new_file;

				try{
					$update = PI::where('id',$this->userInfo['id'])->update(['img_sl'=>$new_file]);
					return json(['code'=>200,'msg'=>'上传成功','file'=>$new_file]);
				}catch(Exception $e){
					return json(['code'=>201,'msg'=>'上传失败'.$e->getMessage()]);
				}
			}
		}
	}

	public function saveInfo(){
		$params = Request::param();
		
		$id = $params['id'] ?? '';
		if(!$id){
			return json(['code' => 201,'msg' => '参数错误']);
		}
		try{
			$update = PI::where('id',$id)->update($params);
			return json(['code'=>200,'msg'=>'保存成功']);
		}catch(Exception $e){
			return json(['code'=>201,'msg'=>'保存失败'.$e->getMessage()]);
		}

	}


	public function address(){
		$this->ny = 'address';

		$user_id = $this->userInfo['id'];

		$addressList = PersonAddress::where('user_id',$user_id)->order('id desc,wtime desc')->select()->toArray();
		View::assign([
			'addressList' => $addressList,
		]);
		return View::fetch();
	}

	public function editAddress(){
		$params = Request::param();
		$id = $params['id'] ?? '';
		$type = $params['type'] ?? '';

		$data = [];
		if($id){
			$data = PersonAddress::where('id',$id)->find();
		}
		View::assign([
			'type' => $type,
			'data' => $data,
		]);
		return View::fetch();
	}

	public function safe(){

		return View::fetch();
	}

	public function orderDetail(){

		$params = Request::param();

		$order_sn = $params['order_sn'] ??'';

		if(empty($order_sn)){
			$this->error('参数错误',null,0);
		}

		$find = Order::with(['orderItem','address'])->where('order_sn',$order_sn)->find();
		if(empty($find)){
			$this->error('订单不存在',null,0);
		}
		View::assign([
			'find' => $find,
		]);

		return View::fetch();
	}
}