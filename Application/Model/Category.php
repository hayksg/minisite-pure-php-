<?php

namespace Application\Model;

use Application\Components\BaseModel;

class Category extends BaseModel
{
    public static $table = 'category';
    
    public static function getCategories()
    {
        $categories = self::getAllByColumns(['is_visible' => 1], 'category_order');
        
        $row = [];
        if (is_array($categories) && count($categories) > 0) {
            foreach ($categories as $category) {
                $row[$category->parent_id][] = $category;
            }
        }
        
        return self::buildTree(array_reverse($row, 1), null);
    }
    
    private static function buildTree($categories, $categoryId)
    {
        $output = '';
        
        if (is_array($categories) && isset($categories[$categoryId])) {
            $output .= '<ul class="topnav list-group list-unstyled">';
            foreach ($categories[$categoryId] as $category) {
                $output .= '<li><a class="list-group-item" href="/category/' . $category->id . '">' . $category->name . '</a>';
                $output .= self::buildTree($categories, $category->id);
                $output .= '</li>';
            }
            $output .= '</ul>';
        }
        
        return $output;
    }
}
