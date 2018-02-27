<?php

namespace Application\Controller;

use Application\Components\View;
use Application\Components\AbstractController;
use Application\Components\Session;
use Application\Components\Pagination;
use Application\Model\Article;
use Application\Model\Category;
use Application\Components\AdminBase;

class AdminArticleController extends AbstractController
{
    use AdminBase;
    
    const IMAGE_DIR = ROOT . 'img/article';
    const SHOW_BY_DEFAULT = 10;
    
    public function indexAction($page = 1)
    {
        /* Pagination block */
        $articleRowsCount = Article::getRowsCount();
        $offset = ($page - 1) * self::SHOW_BY_DEFAULT;
        $offset = $this->clearInt($offset);

        $pagination = new Pagination($articleRowsCount, $page, self::SHOW_BY_DEFAULT, 'page/');
        /* End block */
        
        $articles = Article::getAll(false, false, self::SHOW_BY_DEFAULT, $offset);
        
        $view = new View([
            'articles'      => $articles,            
            'category'      => new Category(),         
            'cnt'           => $offset,      
            'pagination'    => $pagination,         
            'articlesCount' => $articleRowsCount,         
            'showByDefault' => self::SHOW_BY_DEFAULT,         
        ]);
        $view->setTemplate('admin-article/index');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Manage articles');
        $view->ready();
        
        return true;
    }
    
    public function addAction()
    { 
        $categories = Category::getAll();
        $categories = $this->getCategoryWhichHaveNotParentCategory($categories);

        if (isset($_POST['add_article'])) {
            
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrf();
            $this->deleteCsrf();
            
            if ($hidden !== $csrf) {            
                $this->setErrorMessage('csrfError', 'Please try again');             
            }                       
            
            $title = $this->clearStr($_POST['title']);
            
            // This block must be before the image handle, in order do not allow upload image if error exists on form fields
            $res = Article::getByColumns(['title' => $title]);
            if ($res) {
                $this->setErrorMessage('titleError', 'Article with the title "' . $title . '" exists already');
            }
            
            $categoryId  = $this->clearInt($_POST['category_id']);
            $description = $this->clearStr($_POST['description']);
            $content     = $this->clearStr($_POST['content']);           
            $isPublic    = $_POST['is_public'] ?? 0;
            $isPublic    = $this->clearInt($isPublic);
            
            $image       = $this->clearStr($this->uploadImage($_FILES, 'article'));
            
            /* 
            // If we wont default image
            
            if (! $image) {
                $image = '/img/home/no-image.jpg';
            }
            */
            
            if ($image === 'Uploaded file must be an image' || $image === 'The uploaded file exceeds the upload_max_filesize') {
                $this->setErrorMessage('imageError', $image);
            }
            
            if ($this->hasErrorMessage()) {             
                $this->redirectTo('/admin/manage-articles/add');
            }  
            
            $article = new Article();
           
            $article->title       = $title;
            $article->category_id = $categoryId;
            $article->description = $description;
            $article->content     = $content;
            $article->is_public   = $isPublic;
            $article->image       = $image;
            
            $article->save();
            
            $this->setMessage('success', 'The article successfully added!');
            $this->deleteErrorMessage(); // To delete error messages session if exists;
            $this->redirectTo('/admin/manage-articles');
        }
        
        $view = new View([
            'categories' => $categories,
        ]);
        $view->setTemplate('admin-article/add');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Add article');
        $view->ready();
        
        return true;
    }
    
    public function editAction($id)
    {
        $id = $this->clearInt($id);
        $article = Article::getById($id);
        
        $categories = Category::getAll();
        $categories = $this->getCategoryWhichHaveNotParentCategory($categories);
        
        if (isset($_POST['edit_article'])) {
            
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrf();
            $this->deleteCsrf();
            
            if ($hidden !== $csrf) {            
                $this->setErrorMessage('csrfError', 'Please try again');             
            } 
            
            $oldTitle = $this->clearStr($article->title);
            $newTitle = $this->clearStr($_POST['title']);
            
            /* The use of 'strcasecmp' function makes it possible to replace lowercase letters with uppercase letters in title */
            if (Article::getByColumns(['title' => $newTitle]) && strcasecmp($newTitle, $oldTitle) !== 0) {
                $this->setErrorMessage('titleError', 'The title "' . $newTitle . '" exists already');
            }
            
            $categoryId  = $this->clearInt($_POST['category_id']);
            $description = $this->clearStr($_POST['description']);
            $content     = $this->clearStr($_POST['content']);
            
            $isPublic    = $_POST['is_public'] ?? 0;
            $isPublic    = $this->clearInt($isPublic);
            
            $image       = $this->clearStr($this->uploadImage($_FILES, 'article'));                      
            
            if ($image === 'Uploaded file must be an image' || $image === 'The uploaded file exceeds the upload_max_filesize') {
                $this->setErrorMessage('imageError', $image);
            }
            
            /* This block for deleting the old image if we change image to new */
            if ((! empty($image)) && ($image !== $article->image)) {
                $this->deleteImage($article);
            } else {
                $image = $article->image;
            }
            /* End block */
            
            if ($this->hasErrorMessage()) {             
                $this->redirectTo('/admin/manage-articles/edit/' . $id);
            } 
            
            $article->title       = $newTitle;
            $article->category_id = $categoryId;
            $article->description = $description;
            $article->content     = $content;
            $article->is_public   = $isPublic;
            $article->image       = $image;
            
            $article->save();
            
            $this->setMessage('success', 'The article successfully edited!');
            $this->deleteErrorMessage(); // To delete error messages session if exists;
            $this->redirectTo('/admin/manage-articles');
        }
        
        $view = new View([
            'article'    => $article,
            'categories' => $categories,
        ]);
        $view->setTemplate('admin-article/edit');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Edit article');
        $view->ready();
        
        return true;
    }
    
    public function deleteAction($id)
    {  
        $id = $this->clearInt($id);
        $this->setMessage('error', 'Can not delete the article');
        
        if (isset($_POST['csrf'])) {
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrfArray();
                      
            if (in_array($hidden, $csrf)) {    
                $this->deleteCsrf();
                $article = Article::getById($id);
        
                if ($article) {
                    $this->deleteImage($article);

                    if ($article->delete($id)) {
                        $this->setMessage('success', 'The article successfully deleted!');                      
                        $this->getMessage('error'); // This will delete 'error' message
                    }
                }
            }  
        }
        
        $this->redirectTo('/admin/manage-articles');   
    }
}
