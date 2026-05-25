<?php
use think\migration\Migrator;
use think\migration\db\Column;

class Orders extends Migrator
{
    public function change()
    {
        $table = $this->table('orders', [
            'engine' => 'InnoDB',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '订单表'
        ]);
        
        $table->addColumn('order_sn', 'string', [
            'limit' => 32,
            'default' => '',
            'comment' => '订单编号'
        ])
        ->addColumn('user_id', 'integer', [
            'signed' => false,
            'default' => 0,
            'comment' => '用户ID'
        ])
        ->addColumn('total_amount', 'decimal', [
            'precision' => 10,
            'scale' => 2,
            'default' => 0,
            'comment' => '订单总金额'
        ])
        ->addColumn('pay_amount', 'decimal', [
            'precision' => 10,
            'scale' => 2,
            'default' => 0,
            'comment' => '实际支付金额'
        ])
        ->addColumn('status', 'integer', [
            'limit' => 1,
            'default' => 0,
            'comment' => '订单状态：0-待支付，1-已支付，2-已发货，3-已完成，4-已取消'
        ])
        ->addColumn('pay_status', 'integer', [
            'limit' => 1,
            'default' => 0,
            'comment' => '支付状态：0-未支付，1-已支付'
        ])
        ->addColumn('consignee', 'string', [
            'limit' => 50,
            'default' => '',
            'comment' => '收货人姓名'
        ])
        ->addColumn('mobile', 'string', [
            'limit' => 20,
            'default' => '',
            'comment' => '收货人手机'
        ])
        ->addColumn('address', 'string', [
            'limit' => 255,
            'default' => '',
            'comment' => '收货地址'
        ])
        ->addColumn('payment_time', 'datetime', [
            'null' => true,
            'comment' => '支付时间'
        ])
        ->addColumn('ship_time', 'datetime', [
            'null' => true,
            'comment' => '发货时间'
        ])
        ->addColumn('confirm_time', 'datetime', [
            'null' => true,
            'comment' => '确认收货时间'
        ])
        ->addColumn('created_at', 'datetime', [
            'null' => true,
            'comment' => '创建时间'
        ])
        ->addColumn('updated_at', 'datetime', [
            'null' => true,
            'comment' => '更新时间'
        ])
        ->addIndex(['order_sn'], ['unique' => true])
        ->addIndex(['user_id'])
        ->addIndex(['status'])
        ->addIndex(['created_at'])
        ->create();
    }
}