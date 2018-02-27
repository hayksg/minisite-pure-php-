<?php

namespace Application\Components;

use Application\Components\Session;
use Application\Components\FunctionsLibrary as FL;

abstract class AbstractBase
{
    public function clearStr($str = '', $nl2br = false)
    {
        if ($nl2br) {
            return nl2br(trim(htmlentities($str, ENT_QUOTES)));
        } else {
            return trim(htmlentities($str, ENT_QUOTES));
        }
    }
    
    public function entityDecode($str) {
        if (strlen($str) > 0) {
            return html_entity_decode($str);
        }      
    }
    
    public function clearInt($int)
    {
        return abs((int)$int);
    }
    
    public function getDateTime($value, $time = false)
    {
        $output = '';
        $segments = explode(' ', $value);
        
        if ($time) {
            $output = $this->clearStr($segments[0] . ' | ' . $segments[1]);
        } else {
            $output = $this->clearStr($segments[0]);
        }
        return $output;
    }
    
    public function setMessage(string $name, string $value)
    {
        Session::setSession($name, $this->clearStr($value));
        return true;
    }
    
    public function getMessage(string $name)
    {
        $result = Session::getSession($name);
        Session::deleteSession($name);
        return $result;
    }
    
    public function setErrorMessage(string $name, string $value)
    {
        $_SESSION['form-error'][$name] = $value;
        return true;
    }
    
    public function getErrorMessage(string $name)
    {       
        $result = Session::getSessionArray('form-error', $name);
        Session::deleteSessionArray('form-error', $name);
        return $result;
    }
    
    public function hasErrorMessage()
    {
        if (isset($_SESSION['form-error']) && ! empty($_SESSION['form-error'])) {
            return true;
        }
        return false;
    }  
    
    public function deleteErrorMessage()
    {
        Session::deleteSession('form-error');
        return true;
    }
    
    public function debugData($data, $exit = true)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        if ($exit) {
            exit;
        }
    }
    
    protected function filePutContent($path, $content)
    {
        $file = ROOT . 'data/' . $path;
        
        if (is_file($file)) {
            $numberOfBytes = file_put_contents($file, $content);
            
            if ($numberOfBytes) {
                return $numberOfBytes;    
            }
        }
        
        return false;
    }
    
    protected function fileGetContent($path)
    {
        $file = ROOT . 'data/' . $path;
        
        if (is_file($file)) {
            $content = file_get_contents($file);
            return html_entity_decode($content);
        }
        
        return false;
    }
}
