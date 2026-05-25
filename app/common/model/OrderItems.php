<?php 
declare (strict_types = 1);

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * Class app\common\model\OrderItems
 *
 * @property float $goods_price 商品单价
 * @property float $total_price 商品总价
 * @property int $goods_id 商品ID
 * @property int $id
 * @property int $order_id 订单ID
 * @property int $quantity 购买数量
 * @property string $created_at 创建时间
 * @property string $delete_time 删除时间
 * @property string $goods_image 商品图片
 * @property string $goods_name 商品名称
 * @property string $specifications 商品规格
 * @property string $updated_at 更新时间
 * @property-read \app\common\model\Order $order_info
 * @property-read \app\common\model\ProCo $product_info
 * @method static \think\db\Query onlyTrashed()
 * @method static \think\db\Query withTrashed()
 */
class OrderItems extends Model{
	use SoftDelete;

	public function orderInfo(){
		return $this->hasOne(Order::class, 'id', 'order_id');
	}
	public function productInfo(){
		return $this->hasOne(ProCo::class, 'id', 'goods_id');
	}
}

?>