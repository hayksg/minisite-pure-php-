<?php

namespace Application\Model;

use Application\Components\BaseModel;
use Application\Components\Session;
use Application\Components\FunctionsLibrary as FL;

class Authentication extends BaseModel
{
    public static $table = 'authentication';
    
    public static function login($email, $password, $remember)
    {
        $admin = self::getByColumns(['email' => $email]);
        if ($admin) {
            if (password_verify($password, $admin->password)) {
                if ($remember === 1) {  
                    $key = base64_encode('tramvai10avtobus5rubinyanc8');
                    $encrypted = FL::dataEncrypt($admin->email, $key);
                    setcookie('email', $encrypted, strtotime('1 month'), '/');
                }
                return $admin;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public static function auth($admin)
    {
        Session::setSession('admin', $admin, true);
    }
    
    
    
    
    
}  


















