<?php

namespace Application\Controller;

use Application\Components\AbstractController;
use Application\Components\View;

class ContactController extends AbstractController
{
    public function indexAction()
    {
        $view = new View();
        $view->setTemplate('contact/index');
        $view->setHeadTitle('Contact Us');
       
        $view->ready();
        
        return true;
    }
    
    public function mailAction()
    {
        if (isset($_POST['send-message'])) {
            
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrf();
            $this->deleteCsrf();
            
            if ($hidden !== $csrf) {            
                $this->setErrorMessage('csrfError', 'Please try again');             
            }                       
            
            $email = $this->clearStr($_POST['email']);
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);         
            if ( ! $email ) {
                $this->setErrorMessage('emailError', 'Wrong email type'); 
            }
            
            $subject = $this->clearStr($_POST['subject']);         
            if ( ! $subject ) {
                $this->setErrorMessage('subjectError', 'Subject error'); 
            }
            
            $message = $this->clearStr($_POST['message']);            
            if ( ! $message ) {
                $this->setErrorMessage('messageError', 'Message error'); 
            }           
            
            if ($this->hasErrorMessage()) {             
                $this->redirectTo('/contact');
            } 
            
            $headers = 'From: '         . $email . "\r\n" .
                       'Reply-To: '     . $email . "\r\n" .
                       'X-Mailer: PHP/' . phpversion();
            
            $result = mail('testxamppphp@gmail.com', $subject, $message, $headers);

            if ($result) {
                $this->setMessage('success', 'The message successfully send!');
            }

            $this->deleteErrorMessage(); // To delete error messages session if exists;
            $this->redirectTo('/contact');
        }
    } 
}
