<?php

namespace Application\Components;

abstract class AbstractView extends AbstractBase
{
    public function getYear()
    {
        $year = date('Y');
        return ($year > 2017) ? "2017 - {$year}" : $year;
    }
    
    public function getNestedCategories($category)
    {
        $output = '';
        $parentId = $this->clearInt($category->parent_id) ?: null;
        $result = $category->getByColumns(['id' => $parentId]);
        if ($result) {
            $output = $this->clearStr($result->name);
        } else {
            $output = 'No parent category';
        }
        
        return $output;
    }
    
    public function cutString($str, $cnt = 10, $titleCnt = 1000)
    {
        if (strlen($str) <= $cnt) {
            $output = $str;
        } else {
            if (strlen($str) <= $titleCnt) {
                $output = "
                    <span title='" . substr(strip_tags($str), 0, $titleCnt) . "' data-toggle='tooltip' data-placement='right'>"
                        . substr($str, 0, $cnt) . '....' .
                    "</span>
                ";
            } else {
                $output = "
                    <span title='" . substr(strip_tags($str), 0, $titleCnt) . '....' . "' data-toggle='tooltip' data-placement='right'>"
                        . substr($str, 0, $cnt) . '....' .
                    "</span>
                ";
            }
        }
        return $output;
    }
    
    public static function isIssetAdmin()
    {
        if (isset($_SESSION['admin']) || isset($_COOKIE['email'])) {
            return true;
        } else {
            return false;
        }
    }
}
