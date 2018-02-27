<?php

namespace Application\Controller;

use Application\Components\AbstractController;
use Application\Components\View;

class ErrorController extends AbstractController
{
    public function indexAction($e)
    {
        $errorMessage = '';
        
        $message = $e->getMessage();
        $line    = $e->getLine();
        $file    = $e->getFile();
        
        $errorMessage .= "Date: " . date('d F Y H:i:s') . "\r";
        $errorMessage .= "File: {$file}\r";
        $errorMessage .= "Line: {$line}\r";
        $errorMessage .= "Message: {$message}\n\r";
        
        //mail('testxamppphp@gmail.com', 'Error message', $errorMessage);
        
        $this->filePutContent('errors/log.txt',  $errorMessage);

        $view = new View();
        $view->setTemplate('error/index');
        $view->setHeadTitle('An error occurred, pleace try again later.');
       
        $view->ready();
        
        return true;
    }
}
