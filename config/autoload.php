<?php

function applicationAutoload($class)
{   
    defined('DS') ? null: define('DS', DIRECTORY_SEPARATOR); 
    
    $className = substr($class, strrpos($class, '\\') + 1);
    $pathString = mb_strimwidth($class, 0, strrpos($class, '\\'));
    
    if ($pathString == 'Application\Components') {
        $path = ROOT . 'Application' . DS . 'Components' . DS;
    } elseif ($pathString == 'Application\Controller') {
        $path = ROOT . 'Application' . DS . 'Controller' . DS;
    } elseif ($pathString == 'Application\Model') {
        $path = ROOT . 'Application' . DS . 'Model' . DS;
    }
    
    $file = $path . $className . '.php';
    if (is_file($file)) {
        include_once($file);
    }
}

spl_autoload_register('applicationAutoload');
