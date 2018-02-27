<?php

namespace Application\Controller;

use Application\Components\View;
use Application\Components\AbstractController;
use Application\Components\AdminBase;

class AdminController extends AbstractController
{
    use AdminBase;    
    
    public function indexAction()
    {  
        $view = new View([
            'admin' => $this->admin,
        ]);
        $view->setTemplate('admin/index');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Admin area');
        $view->ready();
        
        return true;
    }
}
