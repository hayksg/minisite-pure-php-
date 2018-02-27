<?php

namespace Application\Controller;

use Application\Components\AbstractController;
use Application\Components\View;
use Application\Model\Authentication;
use Application\Components\Session;

class AuthenticationController extends AbstractController
{
    public function indexAction()
    {
        $view = new View();
        $view->setTemplate('authentication/index');
        $view->setHeadTitle('Authentication');
       
        $view->ready();
        
        return true;
    }
    
    public function loginAction()
    { 
        if (isset($_POST['login'])) {
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf   = $this->getCsrf();
            $this->deleteCsrf();
            
            if ($hidden !== $csrf) {            
                $this->setErrorMessage('csrfError', 'Please try again');             
            } 
            
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            
            if (! $email) {
                $this->setErrorMessage('emailError', 'Wrong email type');
            }
            
            $password = $this->clearStr($_POST['password']);
            
            $rememberMe = $_POST['remember_me'] ?? 0;
            $rememberMe = $this->clearInt($rememberMe);
            
            $loginResult = Authentication::login($email, $password, $rememberMe);
            
            if ($loginResult) {
                Authentication::auth($loginResult);
            } else {
                $this->setErrorMessage('doNotMatch', 'Incorrect Email or Password');
            }
            
            if ($this->hasErrorMessage()) {             
                $this->redirectTo('/login');
            }
            
            $this->deleteErrorMessage(); // To delete error messages session if exists;
            $this->redirectTo('/admin');
        }
        
        $view = new View([
            
        ]);
        $view->setTemplate('authentication/index');
        $view->setHeadTitle('Authentication');
       
        $view->ready();
        
        return true;
    }
    
    public function logoutAction()
    {       
        SESSION::deleteSession('admin');
        
        if (isset($_COOKIE['email'])) {
            setcookie('email', '', time() - 3600, '/');
        }
        
        $this->deleteCsrf(); // Removing all session data related to csrf
        
        $this->redirectTo('/');
    }
}
