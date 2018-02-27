<?php

namespace Application\Controller;

use Application\Components\AbstractController;
use Application\Components\View;
use Application\Model\Category;
use Application\Model\Article;
use Application\Model\Slider;

class IndexController extends AbstractController
{
    public function indexAction()
    {
        $slider     = Slider::getAllByColumns(['is_visible' => 1]);
        $categories = Category::getCategories();
        $articles   = Article::getAllByColumns(['is_public' => 1], 'id', true);
        
        $view = new View([
            'slider'     => $slider,
            'categories' => $categories,
            'articles'   => $articles,
        ]);
        $view->setTemplate('index/index');
        $view->setHeadTitle('Breaking News');
       
        $view->ready();
        
        return true;
    }
}
