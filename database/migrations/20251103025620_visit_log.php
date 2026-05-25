<?php

use think\migration\Migrator;
use think\migration\db\Column;

class VisitLog extends Migrator
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
		// 创建表
		$table = $this->table('visit_log', ['comment' => '访问记录表', 'engine' => 'InnoDB', 'encoding' => 'utf8mb4']);
		$table->addColumn('ip', 'string', ['limit' => 45, 'comment' => 'IP地址'])
			  ->addColumn('url', 'string', ['limit' => 500, 'comment' => '访问URL'])
			  ->addColumn('user_agent', 'string', ['limit' => 500, 'comment' => '用户代理'])
			  ->addColumn('referer', 'string', ['limit' => 500, 'default' => '', 'comment' => '来源页面'])
			  ->addColumn('session_id', 'string', ['limit' => 128, 'default' => '', 'comment' => 'Session ID'])
			  ->addColumn('visit_time', 'datetime', ['comment' => '访问时间'])
			  ->addIndex(['ip'])
			  ->addIndex(['visit_time'])
			  ->addIndex(['session_id'])
			  ->create();
	}
}
