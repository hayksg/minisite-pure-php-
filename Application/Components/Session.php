<?php

namespace Application\Components;

class Session
{
    public static function setSession($sessionName, $sessionValue, $serialize = false)
    {
        if ($serialize) {
            $sessionValue = serialize($sessionValue);
            $_SESSION[$sessionName] = $sessionValue;
        } else {
            $_SESSION[$sessionName] = $sessionValue;
        }
    }
    
    public static function getSession($sessionName)
    {
        if (isset($_SESSION[$sessionName])) {
            return $_SESSION[$sessionName];
        }
    }
    
    public static function deleteSession($sessionName)
    {
        if (isset($_SESSION[$sessionName]) || empty($_SESSION[$sessionName])) {
            unset($_SESSION[$sessionName]);
        }
    }
    
    public static function setSessionArray($sessionName, $sessionValue)
    {
        $_SESSION[$sessionName][] = $sessionValue;
    }
    
    public static function getSessionArray($firstName, $secondName)
    {
        if (isset($_SESSION[$firstName][$secondName])) {
            return $_SESSION[$firstName][$secondName];
        }
    }
    
    public static function setSessionArrayWithKey($sessionName, $sessionKey, $sessionValue)
    {
        $_SESSION[$sessionName][$sessionKey] = $sessionValue;
    }
    
    public static function deleteSessionArray($firstName, $secondName)
    {
        if (isset($_SESSION[$firstName][$secondName]) || empty($_SESSION[$firstName][$secondName])) {
            unset($_SESSION[$firstName][$secondName]);
        }       
    }
}
