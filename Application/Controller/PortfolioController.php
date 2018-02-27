<?php

namespace Application\Controller;

use Application\Components\AbstractController;
use Application\Components\View;

class PortfolioController extends AbstractController
{
    public function indexAction()
    {
        $view = new View();
        $view->setTemplate('portfolio/index');
        $view->setHeadTitle('Portfolio');
       
        $view->ready();
        
        return true;
    }
}
