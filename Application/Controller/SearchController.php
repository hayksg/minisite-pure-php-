<?php

namespace Application\Controller;

use Application\Components\AbstractController;
use Application\Components\View;
use Application\Model\Article;

class SearchController extends AbstractController
{
    public function indexAction()
    {
        $searchResult = false;
        
        if (isset($_GET['search'])) {
            $searchString = $this->clearStr($_GET['search']);
            
            if (! empty($searchString) && strlen($searchString) > 1) {
                $searchResult = Article::search($searchString, 'title');
                
                if (! $searchResult) {
                    $searchResult = Article::search($searchString, 'content');
                }
            }  
        }
        
        $view = new View([
            'searchString' => $searchString,
            'searchResult' => $searchResult,
        ]);
        $view->setTemplate('search/index');
        $view->setHeadTitle('Search Results for: "' . $searchString . '"');
       
        $view->ready();
        
        return true;
    }
}
