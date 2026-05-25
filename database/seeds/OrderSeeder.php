<?php
namespace database\seeds;

use think\migration\Seeder;
use app\common\model\Order;

class OrderSeeder extends Seeder
{
	public function run(): void
	{
		$faker = \Faker\Factory::create('zh_CN');
		$orders = [];
		
		// 生成100条测试订单数据
		for ($i = 0; $i < 100; $i++) {
			$totalAmount = $faker->randomFloat(2, 10, 1000);
			$payAmount = $totalAmount - $faker->randomFloat(2, 0, 50);
			$status = $faker->numberBetween(0, 4);
			
			$order = [
				'order_sn' => Order::generateOrderSn(),
				'user_id' => $faker->numberBetween(1, 50),
				'total_amount' => $totalAmount,
				'pay_amount' => $payAmount,
				'status' => $status,
				'pay_status' => $status > 0 ? 1 : 0,
				'consignee' => $faker->name,
				'mobile' => $faker->phoneNumber,
				'address' => $faker->address,
				'created_at' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'),
				'updated_at' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s'),
			];
			
			// 根据状态设置相应的时间
			if ($status >= Order::STATUS_PAID) {
				$order['payment_time'] = $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d H:i:s');
			}
			if ($status >= Order::STATUS_SHIPPED) {
				$order['ship_time'] = $faker->dateTimeBetween('-20 days', 'now')->format('Y-m-d H:i:s');
			}
			if ($status >= Order::STATUS_COMPLETED) {
				$order['confirm_time'] = $faker->dateTimeBetween('-10 days', 'now')->format('Y-m-d H:i:s');
			}
			
			$orders[] = $order;
		}
		
		$this->table('orders')->insert($orders)->save();
	}
}