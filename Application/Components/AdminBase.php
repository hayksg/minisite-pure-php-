<?php

namespace Application\Components;

use Application\Model\Authentication;

trait AdminBase
{
    public $admin = '';
    
    public function __construct()
    {
        $email = $this->isLogged();  
        $admin = Authentication::getByColumns(['email' => $email]);
        
        if ($admin) {
            if ($admin->role === 'super_admin' || $admin->role === 'admin') {
                $this->admin = $admin;
                return true;
            }
        }
        
        die('Access denied.');
        
    }
}
