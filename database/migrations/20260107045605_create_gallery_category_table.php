<?php  
// database/migrations/20240102_create_gallery_category_table.php
use think\migration\Migrator;
use think\migration\db\Column;

class CreateGalleryCategoryTable extends Migrator
{
    public function change()
    {
        $table = $this->table('gallery_category', [
            'engine' => 'InnoDB', 
            'comment' => '图库分类表',
            'signed' => false
        ]);
        
        $table->addColumn('parent_id', 'integer', [
            'default' => 0,
            'comment' => '父级ID',
            'signed' => false
        ])
        ->addColumn('name', 'string', [
            'limit' => 100,
            'default' => '',
            'comment' => '分类名称'
        ])
        ->addColumn('slug', 'string', [
            'limit' => 100,
            'default' => '',
            'comment' => '分类标识（URL友好）'
        ])
        ->addColumn('description', 'text', [
            'null' => true,
            'comment' => '分类描述'
        ])
        ->addColumn('cover_image_id', 'integer', [
            'default' => 0,
            'comment' => '封面图片ID',
            'signed' => false
        ])
        ->addColumn('type', 'integer', [
            'limit' => 1,
            'default' => 2,
            'comment' => '分类类型:1系统分类,2用户分类'
        ])
        ->addColumn('sort_order', 'integer', [
            'default' => 0,
            'comment' => '排序'
        ])
        ->addColumn('status', 'integer', [
            'limit' => 1,
            'default' => 1,
            'comment' => '状态:1启用,0禁用'
        ])
        ->addColumn('icon', 'string', [
            'limit' => 100,
            'default' => '',
            'comment' => '分类图标'
        ])
        ->addColumn('color', 'string', [
            'limit' => 20,
            'default' => '',
            'comment' => '分类颜色'
        ])
        ->addColumn('seo_data', 'text', [
            'null' => true,
            'comment' => 'SEO数据(JSON)'
        ])
        ->addColumn('user_id', 'integer', [
            'default' => 0,
            'comment' => '创建用户ID',
            'signed' => false
        ])
        ->addColumn('create_time', 'integer', [
            'default' => 0
        ])
        ->addColumn('update_time', 'integer', [
            'default' => 0
        ])
        ->addColumn('delete_time', 'integer', [
            'null' => true,
            'default' => null
        ])
        
        ->addIndex(['parent_id'], ['name' => 'idx_parent'])
        ->addIndex(['slug'], ['name' => 'idx_slug', 'unique' => true])
        ->addIndex(['type'], ['name' => 'idx_type'])
        ->addIndex(['status'], ['name' => 'idx_status'])
        ->addIndex(['sort_order'], ['name' => 'idx_sort_order'])
        ->addIndex(['user_id'], ['name' => 'idx_user'])
        ->addIndex(['create_time'], ['name' => 'idx_create_time'])
        
        ->create();
    }
}
?>