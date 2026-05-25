<?php  
// app/common/model/VideoCategory.php
namespace app\common\model;

use think\Model;

/**
 * Class app\common\model\VideoCategory
 *
 * @property int $id
 * @property int $parent_id 父分类ID
 * @property int $sort_order 排序
 * @property int $status 状态
 * @property string $create_time
 * @property string $description 分类描述
 * @property string $icon 分类图标
 * @property string $name 分类名称
 * @property-read \app\common\model\VideoCategory $parent
 * @property-read \app\common\model\VideoCategory[] $children
 * @property-read \app\common\model\Video[] $videos
 */
class VideoCategory extends Model
{
    protected $table = 'video_category';
    protected $pk = 'id';
    
    protected $type = [
        'status' => 'integer',
        'sort_order' => 'integer',
        'parent_id' => 'integer',
    ];
    // 状态常量
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 0;
    /**
     * 子分类
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
    
    /**
     * 父分类
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }
    
    /**
     * 该分类下的视频
     */
    public function videos()
    {
        return $this->hasMany(Video::class, 'category_id', 'id');
    }
    
    /**
     * 获取树形分类列表
     */
    public static function getTree($parentId = 0, &$result = [])
    {
        $list = self::where('parent_id', $parentId)
            ->where('status', 1)
            ->order('sort_order', 'asc')
            ->select();
            
        foreach ($list as $item) {
            $result[] = $item;
            self::getTree($item->id, $result);
        }
        
        return $result;
    }

    /**
     * 获取分类选项（用于下拉选择）
     */
    public static function getCategoryOptions($parentId = 0, $prefix = '', $excludeId = null)
    {
        $options = [];
        
        $query = self::where('parent_id', $parentId)
            ->where('status', self::STATUS_ACTIVE);
        
        if ($excludeId) {
            $query->where('id', '<>', $excludeId);
        }
        
        $categories = $query->order('sort_order asc, id asc')->select();
        
        foreach ($categories as $category) {
            $options[$category->id] = $prefix . $category->name;
            $childOptions = self::getCategoryOptions($category->id, $prefix . '├─ ', $excludeId);
            $options += $childOptions;
        }
        return $options;
    }

    /**
     * 获取分类下所有子分类ID（包括自身）
     */
    public static function getAllChildrenIds($categoryId, $includeSelf = true)
    {
        $ids = $includeSelf ? [(int)$categoryId] : [];
        
        $children = self::where('parent_id', $categoryId)
            ->where('status', self::STATUS_ACTIVE)
            ->column('id');
        
        foreach ($children as $childId) {
            $ids = array_merge($ids, self::getAllChildrenIds($childId, true));
        }
        
        return array_unique($ids);
    }
}
?>