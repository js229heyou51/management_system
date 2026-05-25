<?php  
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\Order
 *
 * @property float $coupon_amount 优惠
 * @property float $member_benefits 会员福利
 * @property float $pay_amount 实际支付金额
 * @property float $points 积分
 * @property float $postage 邮费
 * @property float $price
 * @property float $user_discounts 用户折扣
 * @property int $address_id 订单地址ID
 * @property int $check
 * @property int $deliver_status 发货状态
 * @property int $express_id
 * @property int $handle
 * @property int $id
 * @property int $invoice_id 发票ID
 * @property int $need_invoice 需要发票，0不要，1要
 * @property int $pass
 * @property int $pay_status 支付状态
 * @property int $receive_status 收货状态
 * @property int $refund_status 退款状态
 * @property int $user_id 用户ID
 * @property string $delete_time
 * @property string $deliver_time 发货时间
 * @property string $ip
 * @property string $num
 * @property string $order_sn
 * @property string $pay_time 支付时间
 * @property string $pay_type 支付类型
 * @property string $receive_time 收货时间
 * @property string $refund_time 退款时间
 * @property string $remarks
 * @property string $title
 * @property string $wtime
 * @property-read \app\common\model\OrderAddress $address
 * @property-read \app\common\model\OrderItems[] $order_item
 * @property-read \app\common\model\OrderMessage $order_message
 * @property-read \app\common\model\OrderNotes $order_notes
 * @property-read \app\common\model\OrderRecord[] $order_record
 * @property-read \app\common\model\Person $userinfo
 * @property-read mixed $order_status
 * @property-read mixed $pay_status_html
 * @property-read mixed $pay_status_text
 * @property-read mixed $ship_status_html
 * @property-read mixed $ship_status_text
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class Order extends Model{
	use SoftDelete;

	// 设置表名
	protected $name = 'order';
	
	// 设置自动时间戳
	protected $autoWriteTimestamp = true;
	protected $createTime = 'created_at';
	protected $updateTime = 'updated_at';
	protected $dateFormat = 'Y-m-d H:i:s';

	// 支付状态常量
	const PAY_UNPAID = 0;     // 未支付
	const PAY_PAID = 1;       // 已支付
	const PAY_FAILED = 2;     // 支付失败
	const PAY_REFUNDED = 3;   // 已退款

	// 发货状态常量
	const SHIP_UNSHIPPED = 0;  // 未发货
	const SHIP_SHIPPED = 1;    // 已发货
	const SHIP_RECEIVED = 2;   // 已签收
	const SHIP_RETURNING = 3;  // 退货中
	const SHIP_RETURNED = 4;   // 已退货
	
	// 支付状态映射
	protected $payStatusMap = [
		self::PAY_UNPAID   => '未支付',
		self::PAY_PAID     => '已支付',
		self::PAY_FAILED   => '支付失败',
		self::PAY_REFUNDED => '已退款'
	];
	
	// 发货状态映射
	protected $shipStatusMap = [
		self::SHIP_UNSHIPPED  => '未发货',
		self::SHIP_SHIPPED    => '已发货',
		self::SHIP_RECEIVED   => '已签收',
		self::SHIP_RETURNING  => '退货中',
		self::SHIP_RETURNED   => '已退货'
	];
	
	// // 支付状态获取器
	// public function getPayStatusAttr($value, $data)
	// {
	// 	return $this->payStatusMap[$value] ?? '未知支付状态';
	// }
	
	// // 发货状态获取器
	// public function getShipStatusAttr($value, $data)
	// {
	// 	return $this->shipStatusMap[$value] ?? '未知发货状态';
	// }
	
	// 综合状态获取器（根据支付和发货状态组合）
	public function getOrderStatusAttr($value,$data)
	{
		$payStatus = $data['pay_status'] ?? 0;
		$shipStatus = $data['deliver_status'] ?? 0;

		// 逻辑判断综合状态
		if ($payStatus == self::PAY_UNPAID) {
			$text = '待支付';
			$class = '';
		} elseif ($payStatus == self::PAY_PAID && $shipStatus == self::SHIP_UNSHIPPED) {
			$text = '待发货';
			$class = 'layui-bg-blue';
		} elseif ($payStatus == self::PAY_PAID && $shipStatus == self::SHIP_SHIPPED) {
			$text = '待收货';
			$class = 'layui-bg-orange';
		} elseif ($payStatus == self::PAY_PAID && $shipStatus == self::SHIP_RECEIVED) {
			$text = '已完成';
			$class = 'layui-bg-green';
		} elseif ($payStatus == self::PAY_REFUNDED) {
			$text = '已退款';
			$class = 'layui-bg-black';
		} elseif ($shipStatus == self::SHIP_RETURNING) {
			$text = '退货中';
			$class = 'layui-bg-black';
		} elseif ($shipStatus == self::SHIP_RETURNED) {
			$text = '已退货';
			$class = 'layui-bg-gray';
		} elseif ($payStatus == self::PAY_FAILED) {
			$text = '支付失败';
			$class = 'layui-bg-gray';
		} else {
			$text = '未知状态';
			$class = 'layui-bg-gray';
		}
		$arr['statusText'] = $text;
		$arr['statusHtml'] = sprintf('<span class="layui-badge %s">%s</span>', $class, $text);

		return $arr;
	}
	
	// 带样式的支付状态
	public function getPayStatusHtmlAttr($value, $data)
	{
		$classMap = [
			self::PAY_UNPAID   => 'badge badge-warning',
			self::PAY_PAID     => 'badge badge-success',
			self::PAY_FAILED   => 'badge badge-danger',
			self::PAY_REFUNDED => 'badge badge-info'
		];
		
		$text = $this->payStatusMap[$data['pay_status']] ?? '未知';
		$class = $classMap[$data['pay_status']] ?? 'badge badge-secondary';
		
		return sprintf('<span class="%s">%s</span>', $class, $text);
	}
	
	// 带样式的发货状态
	public function getShipStatusHtmlAttr($value, $data)
	{
		$classMap = [
			self::SHIP_UNSHIPPED  => 'badge badge-secondary',
			self::SHIP_SHIPPED    => 'badge badge-info',
			self::SHIP_RECEIVED   => 'badge badge-success',
			self::SHIP_RETURNING  => 'badge badge-warning',
			self::SHIP_RETURNED   => 'badge badge-danger'
		];
		
		$text = $this->shipStatusMap[$data['deliver_status']] ?? '未知';
		$class = $classMap[$data['deliver_status']] ?? 'badge badge-secondary';
		
		return sprintf('<span class="%s">%s</span>', $class, $text);
	}
	
	// 追加虚拟字段
	protected $append = [
		'pay_status_text',     // pay_status 获取器的别名
		'ship_status_text',    // ship_status 获取器的别名
		'order_status'         // 综合状态
	];
	
	// 支付状态文本（给追加字段使用）
	public function getPayStatusTextAttr($value, $data)
	{
		return $this->payStatusMap[$data['pay_status']] ?? '未知支付状态';
	}
	
	// 发货状态文本（给追加字段使用）
	public function getShipStatusTextAttr($value, $data)
	{
		return $this->shipStatusMap[$data['deliver_status']] ?? '未知发货状态';
	}
	
	// 生成订单号
	public static function generateOrderSn()
	{
		return 'os'.date('YmdHis') . str_pad(''.mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
	}

	//
	public function userinfo(){
		return $this->hasOne(Person::class, 'id', 'user_id');
	}

	public function orderItem(){
		return $this->hasMany(OrderItems::class, 'order_id', 'id');
	}
	
	public function orderRecord(){
		return $this->hasMany(OrderRecord::class, 'order_id', 'id');
	}
	
	public function orderNotes(){
		return $this->hasOne(OrderNotes::class, 'order_id', 'id');
	}
	
	public function orderMessage(){
		return $this->hasOne(OrderMessage::class, 'order_id', 'id');
	}

	public function address(){
		return $this->hasOne(OrderAddress::class, 'id', 'address_id');
	}
}

?>