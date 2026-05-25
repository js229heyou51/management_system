<?php  
// database/migrations/20240101_create_gallery_table.php
use think\migration\Migrator;
use think\migration\db\Column;

class CreateGalleryTable extends Migrator
{
	public function change()
	{
		$table = $this->table('gallery', ['engine' => 'InnoDB', 'comment' => '图片库']);
		
		$table->addColumn('title', 'string', ['limit' => 100, 'default' => '', 'comment' => '图片标题'])
			  ->addColumn('filename', 'string', ['limit' => 255, 'comment' => '原始文件名'])
			  ->addColumn('path', 'string', ['limit' => 500, 'comment' => '存储路径'])
			  ->addColumn('size', 'integer', ['default' => 0, 'comment' => '文件大小(字节)'])
			  ->addColumn('mime_type', 'string', ['limit' => 100, 'default' => '', 'comment' => '文件类型'])
			  ->addColumn('upload_type', 'string', ['limit' => 100, 'default' => '', 'comment' => '上传方式'])
			  ->addColumn('extension', 'string', ['limit' => 10, 'default' => '', 'comment' => '文件扩展名'])
			  ->addColumn('width', 'integer', ['default' => 0, 'comment' => '图片宽度'])
			  ->addColumn('height', 'integer', ['default' => 0, 'comment' => '图片高度'])
			  ->addColumn('md5', 'string', ['limit' => 32, 'default' => '', 'comment' => '文件MD5'])
			  ->addColumn('category_id', 'integer', ['default' => 0, 'comment' => '分类ID'])
			  ->addColumn('user_id', 'integer', ['default' => 0, 'comment' => '上传用户ID'])
			  ->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'comment' => '状态:1正常,0删除'])
			  ->addColumn('meta_data', 'text', ['null' => true, 'comment' => '元数据(JSON)'])
			  ->addColumn('description', 'text', ['null' => true, 'comment' => '描述'])
			  ->addColumn('create_time', 'integer', ['default' => 0])
			  ->addColumn('update_time', 'integer', ['default' => 0])
			  ->addColumn('delete_time', 'integer', ['null' => true, 'default' => null])
			  
			  ->addIndex(['md5'], ['unique' => false, 'name' => 'idx_md5'])
			  ->addIndex(['category_id'], ['name' => 'idx_category'])
			  ->addIndex(['user_id'], ['name' => 'idx_user'])
			  ->addIndex(['create_time'], ['name' => 'idx_create_time'])
			  ->addIndex(['status'], ['name' => 'idx_status'])
			  
			  ->create();
	}
}

?>