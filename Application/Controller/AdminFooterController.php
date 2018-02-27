<?php

namespace Application\Controller;

use Application\Components\View;
use Application\Components\AbstractController;
use Application\Components\AdminBase;

class AdminFooterController extends AbstractController
{
    use AdminBase;   
    
    public function indexAction()
    {    
        $view = new View();
        $view->setTemplate('admin-footer/index');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Edit footer');
        $view->ready();
        
        return true;
    }
    
    public function editIconsAction()
    {
        if (isset($_POST['edit-social-icons'])) {
            
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrfArray();
            $this->deleteCsrf();
                      
            if (! in_array($hidden, $csrf)) {    
                $this->setErrorMessage('csrfError', 'Please try again');  
            }
            
            $twitter  = $this->clearStr($_POST['twitter']);
            $facebook = $this->clearStr($_POST['facebook']);
            $youtube  = $this->clearStr($_POST['youtube']);           
            
            $twitterResult  = $this->filePutContent('soc-icons/twitter.txt',  $twitter);
            $facebookResult = $this->filePutContent('soc-icons/facebook.txt', $facebook);
            $youtubeResult  = $this->filePutContent('soc-icons/youtube.txt',  $youtube);
            
            if (! $twitterResult && $twitter) {
                $this->setErrorMessage('twitterError', 'Can not add text to twitter link'); 
            }
            
            if (! $facebookResult && $facebook) {
                $this->setErrorMessage('facebookError', 'Can not add text to facebook link'); 
            }
            
            if (! $youtubeResult && $youtube) {
                $this->setErrorMessage('youtubeError', 'Can not add text to youtube link'); 
            }
            
            if ($this->hasErrorMessage()) {             
                $this->redirectTo('/admin/manage-footer');
            } 
            
            $this->setMessage('success', 'The social icons successfully edited!');
            $this->deleteErrorMessage(); // To delete error messages session if exists;
            $this->redirectTo('/admin/manage-footer');
        }  
    }
    
    public function editFooterTextAction()
    {
        if (isset($_POST['edit_footer'])) {
            
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrfArray();
            $this->deleteCsrf();
                      
            if (! in_array($hidden, $csrf)) {   
                $this->setErrorMessage('csrfError', 'Please try again');    
            }
            
            $content = $this->clearStr($_POST['footer-content']);
            
            $result = $this->filePutContent('footer/footer-text.txt', $content);

            if (! $result && $content) {
                $this->setErrorMessage('contentError', 'Can not add text to footer'); 
            }

            if ($this->hasErrorMessage()) {             
                $this->redirectTo('/admin/manage-footer');
            } 
            
            $this->setMessage('success', 'The footer text successfully edited!');
            $this->deleteErrorMessage(); // To delete error messages session if exists;
            $this->redirectTo('/admin/manage-footer');
        } 
    }
}
