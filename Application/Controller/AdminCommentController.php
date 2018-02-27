<?php

namespace Application\Controller;

use Application\Components\View;
use Application\Components\AbstractController;
use Application\Components\AdminBase;
use Application\Model\Comment;

class AdminCommentController extends AbstractController
{
    use AdminBase;   
    
    public function indexAction()
    {
        $commentsJoinArticles = Comment::getCommentsInArticles();
       
        $view = new View([
            'commentsJoinArticles' => $commentsJoinArticles,
        ]);
        $view->setTemplate('admin-comment/index');
        $view->setLayout('adminLayout');
        $view->setHeadTitle("Comment's list");
        $view->ready();
        
        return true;
    }
    
    public function deleteAction($id)
    {
        $id = $this->clearInt($id);
        
        $this->setMessage('error', 'Can not delete the comment');
        
        if (isset($_POST['csrf'])) {
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrfArray();
                      
            if (in_array($hidden, $csrf)) {    
                $this->deleteCsrf();
                $comment = Comment::getById($id);
        
                if ($comment) {
                    if ($comment->delete($id)) {
                        $this->setMessage('success', 'The comment successfully deleted!');                      
                        $this->getMessage('error'); // This will delete 'error' message
                    }
                }
            }  
        }
        
        $this->redirectTo('/admin/manage-comments'); 
    }
}