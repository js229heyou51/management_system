<?php

use think\migration\Migrator;
use think\migration\db\Column;

class OrderItems extends Migrator
{
	/**
	 * Change Method.
	 *
	 * Write your reversible migrations using this method.
	 *
	 * More information on writing migrations is available here:
	 * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
	 *
	 * The following commands can be used in this method and Phinx will
	 * automatically reverse them when rolling back:
	 *
	 *    createTable
	 *    renameTable
	 *    addColumn
	 *    renameColumn
	 *    addIndex
	 *    addForeignKey
	 *
	 * Remember to call "create()" or "update()" and NOT "save()" when working
	 * with the Table class.
	 */
	 public function change()
	{
		$table = $this->table('order_items', [
			'engine' => 'InnoDB',
			'charset' => 'utf8mb4',
			'collation' => 'utf8mb4_unicode_ci',
			'comment' => '订单商品表'
		]);
		
		$table->addColumn('order_id', 'integer', [
			'signed' => false,
			'default' => 0,
			'comment' => '订单ID'
		])
		->addColumn('goods_id', 'integer', [
			'signed' => false,
			'default' => 0,
			'comment' => '商品ID'
		])
		->addColumn('goods_name', 'string', [
			'limit' => 255,
			'default' => '',
			'comment' => '商品名称'
		])
		->addColumn('goods_image', 'string', [
			'limit' => 500,
			'default' => '',
			'comment' => '商品图片'
		])
		->addColumn('goods_price', 'decimal', [
			'precision' => 10,
			'scale' => 2,
			'default' => 0,
			'comment' => '商品单价'
		])
		->addColumn('quantity', 'integer', [
			'signed' => false,
			'default' => 0,
			'comment' => '购买数量'
		])
        ->addColumn('specifications', 'string', [
			'limit' => 500,
			'default' => '',
            'comment' => '商品规格'
        ])
		->addColumn('total_price', 'decimal', [
			'precision' => 10,
			'scale' => 2,
			'default' => 0,
			'comment' => '商品总价'
		])
		->addColumn('created_at', 'datetime', [
			'null' => true,
			'comment' => '创建时间'
		])
		->addColumn('updated_at', 'datetime', [
			'null' => true,
			'comment' => '更新时间'
		])
		->addIndex(['order_id'])
		->addIndex(['goods_id'])
		->create();
	}
}
