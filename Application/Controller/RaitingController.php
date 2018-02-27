<?php

namespace Application\Controller;

use Application\Components\AbstractController;
use Application\Components\View;
use Application\Model\Raiting;

class RaitingController extends AbstractController
{
    public function addAction()
    {
        $responseArr = [];
        
        if (isset($_POST['ratingStars'], $_POST['articleId'])) {
            
            $ratingStars = $this->clearInt($_POST['ratingStars']);
            $articleId   = $this->clearInt($_POST['articleId']);
            $ipAddress   = $_SERVER['REMOTE_ADDR'];
                         
            if (! empty($ratingStars) && ! empty($articleId)) {
                $ipAddressExists = Raiting::getByColumns(['ip_address' => $ipAddress, 'article_id' => $articleId]);
                
                if ($ipAddressExists) {
                    
                    $userRaitingCount = $ipAddressExists->raiting_count;
                    $responseArr['userRaitingCount'] = $userRaitingCount;
                    
                } else {
                    
                    $raiting = new Raiting();
                    $raiting->article_id    = $articleId;
                    $raiting->raiting_count = $ratingStars;
                    $raiting->ip_address    = $ipAddress;

                    $raiting->save();
                 
                    $raitingsCount   = Raiting::getRowsCount('article_id', $articleId);

                    $responseArr['ipAddress']     = $ipAddress;            
                    $responseArr['raitingsCount'] = $raitingsCount;
                } 
                
                $avgRaiting                = Raiting::getAVGRaiting($articleId); 
                $responseArr['avgRaiting'] = $avgRaiting;
            } 
        }
        
        echo json_encode(['output' => $responseArr]);
        return true;
    }
}
