<?php

namespace Application\Controller;

use Application\Components\AbstractController;
use Application\Components\View;

class AboutUsController extends AbstractController
{
    public function indexAction()
    {
        $view = new View();
        $view->setTemplate('about-us/index');
        $view->setHeadTitle('About Us');
       
        $view->ready();
        
        return true;
    } 
}
