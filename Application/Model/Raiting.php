<?php

namespace Application\Model;

use Application\Components\BaseModel;

class Raiting extends BaseModel
{
    public static $table = 'raiting';
    
    public static function getAVGRaiting($articleID)
    {
        $sql  = "
            SELECT AVG(raiting_count)
            AS count_of_raitings
            FROM raiting 
            WHERE article_id = :article_id
        ";
        
        $avgRaiting = self::customQuery($sql, ['article_id' => $articleID]);
        
        if ($avgRaiting) {
            $output = $avgRaiting[0]->count_of_raitings;
            if ($output) {
                return round($output);
            }
        }
        
        return false;
    }
}
