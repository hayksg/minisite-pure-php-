<?php

namespace Application\Model;

use Application\Components\BaseModel;

class Comment extends BaseModel
{
    public static $table = 'comment';
    
    public function getCommentsInArticles()
    {
        $sql  = "
            SELECT c.id, c.article_id, c.user_nickname, c.user_email, c.content, c.entry_date, a.title 
            FROM comment AS c
            LEFT JOIN article AS a 
            ON c.article_id = a.id
            ORDER BY c.article_id
        ";
        
        $articlesWithComments = self::customQuery($sql);
        
        return $articlesWithComments ? $articlesWithComments : false;
    }
}
