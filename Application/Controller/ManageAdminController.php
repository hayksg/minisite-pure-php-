<?php

namespace Application\Controller;

use Application\Components\AbstractController;
use Application\Components\View;
use Application\Model\Authentication;
use Application\Components\AdminBase;

class ManageAdminController extends AbstractController
{
    use AdminBase;
    
    public function indexAction()
    {  
        $admins = Authentication::getAll();
        
        $view = new View([
            'admins' => $admins,
            'cnt'    => 0,
        ]);
        $view->setTemplate('manage-admins/index');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Manage admins');
        $view->ready();
        
        return true;
    }
    
    private function responseFromGoogle($postRecaptchaValue)
    {        
        $secret = '6Le4j0UUAAAAANdPFctAxxYT-rxwBQbhKghH9g5c';
        $url    = "https://www.google.com/recaptcha/api/siteverify";

        $data = [
            'secret'   => $secret,
            'response' => $postRecaptchaValue,
            'remoteip' => $_SERVER['REMOTE_ADDR'],
        ];

        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, $url);
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);

        return $response;
    }
    
    public function addAction()
    {  
        if (isset($_POST['register'])) {

            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrf();
            
            if ($hidden !== $csrf) {            
                $this->setErrorMessage('csrfError', 'Please try again');             
            } 
            
            $name = $this->clearStr($_POST['name']);
            
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            
            if (! $email) {
                $this->setErrorMessage('emailError', 'Wrong email type');
            }

            $result = Authentication::getByColumns(['email' => $email]);
            if ($result) {
                $this->setErrorMessage('emailExistsError', 'Email address "' . $email . '" exists already');
            }

            $password = $this->clearStr($_POST['password']);
            $confirm_password = $this->clearStr($_POST['confirm_password']);
            
            if ($password !== $confirm_password) {
                $this->setErrorMessage('passwordConfirmError', 'Please confirm the password');
            }
            
            $postRecaptchaValue = $_POST['g-recaptcha-response'];
            
            $response = $this->responseFromGoogle($postRecaptchaValue);
	        $response = json_decode($response);
            
            if (! $response->success) {
                $this->setErrorMessage('recaptchaError', 'Please verify recaptcha');
            }           
            
            if ($this->hasErrorMessage()) {             
                $this->redirectTo('/admin/manage-admins/add');
            }
            
            $auth = new Authentication();
           
            $auth->name     = $name;
            $auth->email    = $email;
            $auth->password = password_hash($password, PASSWORD_DEFAULT);           
            
            $auth->save();
            
            $this->setMessage('success', 'The admin successfully added!');
            $this->deleteErrorMessage(); // To delete error messages session if exists;
            $this->redirectTo('/admin/manage-admins');
        }
        
        $view = new View();
        $view->setTemplate('manage-admins/add');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Add admin');
        $view->ready();
        
        return true;
    }
    
    public function editAction($id)
    {
        $id = $this->clearInt($id);
        $role = 'admin';
        $admin = Authentication::getById($id);
        
        if (isset($_POST['edit_admin'])) {

            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrfArray();
                      
            if (! in_array($hidden, $csrf)) { 
                $this->setErrorMessage('csrfError', 'Please try again'); 
            }

            if (in_array($_POST['role'], ['admin', 'super_admin'])) {
                $role = $this->clearStr($_POST['role']);
            } else {
                $this->setErrorMessage('roleError', 'Wrong value'); 
            }
            
            if ($this->hasErrorMessage()) {             
                $this->redirectTo('/admin/manage-admins/edit/' . $id);
            }

            $admin->role = $role;
            $admin->save();
            
            $this->setMessage('success', 'The admin successfully edited!');
            $this->deleteErrorMessage(); // To delete error messages session if exists;
            $this->redirectTo('/admin/manage-admins');
        }
        
        $view = new View([
            'admin' => $admin,
        ]);
        $view->setTemplate('manage-admins/edit');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Edit admin');
        $view->ready();
        
        return true;
    }
    
    public function deleteAction($id) {
        $id = $this->clearInt($id);
        $this->setMessage('error', 'An error occurred. Can not delete the admin');
        
        if (isset($_POST['csrf'])) {
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrfArray();
                      
            if (in_array($hidden, $csrf)) {    
                $admin = Authentication::getById($id);
                
                if ($admin) {                   
                    $this->deleteCsrf();
                    
                    if ($admin->delete($id)) {                       
                        $this->setMessage('success', 'The admin successfully deleted!');                      
                        $this->getMessage('error'); // This will delete 'error' message
                    }
                }
            }  
        }
        
        $this->redirectTo('/admin/manage-admins');  
    }
}
