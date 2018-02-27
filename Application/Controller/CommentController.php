<?php

namespace Application\Controller;

use Application\Components\AbstractController;
use Application\Components\View;
use Application\Model\Comment;

class CommentController extends AbstractController
{
    public function addAction()
    {
        $output = '';
        $errors = [];
              
        if (isset($_POST['article-id'], $_POST['csrf'], $_POST['nickname'], $_POST['email'], $_POST['content'])) {
            
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf   = $this->getCsrf();
            $this->deleteCsrf();
            
            if ($hidden !== $csrf) {                        
                $errors['csrfError'] = 'Please reload the page and try again';          
            }

            $articleId = $this->clearInt($_POST['article-id']);
            
            $nickname = $this->clearStr($_POST['nickname']);
             
            $email = $this->clearStr($_POST['email']);
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);   
            
            if ( ! $email ) {
                $errors['emailError'] = 'Wrong email type. Please reload the page and try again';  
            }          

            $content = $this->clearStr($_POST['content']);             
            $content = $this->clearFromBadWords($content); 
            
            if (count($errors) == 0) {
                $comment = new Comment();

                $comment->article_id    = $articleId;
                $comment->user_nickname = $nickname;
                $comment->user_email    = $email;
                $comment->content       = $content;

                $lastInsertId = $comment->save();

                $lastComment = Comment::getById($lastInsertId);

                $responseArr = [];

                $responseArr['user_nickname'] = $lastComment->user_nickname;
                $responseArr['content'] = $lastComment->content;
                $responseArr['entry_date'] = $this->getDateTime($lastComment->entry_date);

                $output = $responseArr;
            }
        }       
        
        echo json_encode(['output' => $output, 'errors' => $errors]);
        return true;
    }     
}
