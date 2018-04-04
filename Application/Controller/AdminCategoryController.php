<?php

namespace Application\Controller;

use Application\Components\View;
use Application\Components\AbstractController;
use Application\Components\Session;
use Application\Model\Category;
use Application\Model\Article;
use Application\Components\AdminBase;

class AdminCategoryController extends AbstractController
{
    use AdminBase;
    
    const IMAGE_DIR = ROOT . 'img/article';
    
    public function indexAction()
    {  
        $categories = Category::getAll('category_order');
        
        $view = new View([
            'categories' => $categories,               
            'cnt'        => 0,               
        ]);
        $view->setTemplate('admin-category/index');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Manage categories');
        $view->ready();
        
        return true;
    }
    
    public function addAction()
    {  
        $categories = Category::getAll();
        
        if (isset($_POST['add_category'])) {
            
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrf();
            $this->deleteCsrf();
            
            if ($hidden !== $csrf) {            
                $this->setErrorMessage('csrfError', 'Please try again');             
            } 
            
            $name = $this->clearStr($_POST['name']);
                       
            $res = Category::getByColumns(['name' => $name]);
            if ($res) {
                $this->setErrorMessage('nameError', 'Category with the name "' . $name . '" exists already');
            }
            
            $parentId = $this->clearInt($_POST['category_id']);
            if ($parentId === 0) {
                $parentId = null;
            }
            
            $isPublic = $_POST['is_public'] ?? 0;
            $isPublic = $this->clearInt($isPublic);
            
            $categoryOrder = $this->clearInt($_POST['order']);
            if ($categoryOrder > 127) {
                $this->setErrorMessage('orderError', 'The maximum number is 127');
            }
            
            if ($this->hasErrorMessage()) {             
                $this->redirectTo('/admin/manage-categories/add');
            }
            
            $category = new Category();
           
            $category->name           = $name;
            $category->parent_id      = $parentId;
            $category->is_visible     = $isPublic;          
            $category->category_order = $categoryOrder;          

            $category->save();
            
            $this->setMessage('success', 'The category successfully added!');
            $this->deleteErrorMessage(); // To delete error messages session if exists;
            $this->redirectTo('/admin/manage-categories'); 
        } 
        
        $view = new View([
             'categories' => $categories,              
        ]);
        $view->setTemplate('admin-category/add');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Add category');
        $view->ready();
        
        return true;
    }
    
    public function editAction($id)
    {  
        $id = $this->clearInt($id);
        $category = Category::getById($id);
        
        $categories = Category::getAll();
        
        if (isset($_POST['edit_category'])) {
            
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrf();
            $this->deleteCsrf();
            
            if ($hidden !== $csrf) {            
                $this->setErrorMessage('csrfError', 'Please try again');             
            } 
                       
            $oldName = $this->clearStr($category->name);
            $newName = $this->clearStr($_POST['name']);
                      
            if (Category::getByColumns(['name' => $newName]) && $newName !== $oldName) {
                $this->setErrorMessage('nameError', 'The name "' . $newName . '" exists already');
            }
            
            $parentId = $this->clearInt($_POST['category_id']);
            if ($parentId === 0) {
                $parentId = null;
            }
            
            $isPublic = $_POST['is_public'] ?? 0;
            $isPublic = $this->clearInt($isPublic);
            
            $categoryOrder = $this->clearInt($_POST['order']);
            if ($categoryOrder > 127) {
                $this->setErrorMessage('orderError', 'The maximum number is 127');
            }
            
            if ($this->hasErrorMessage()) {             
                $this->redirectTo('/admin/manage-categories/edit/' . $id);
            }
           
            $category->name           = $newName;
            $category->parent_id      = $parentId;
            $category->is_visible     = $isPublic;   
            $category->category_order = $categoryOrder;

            $category->save();
            
            $this->setMessage('success', 'The category successfully edited!');
            $this->deleteErrorMessage(); // To delete error messages session if exists;
            $this->redirectTo('/admin/manage-categories');  
        }
        
        $view = new View([
            'category'   => $category,                   
            'categories' => $categories,                   
        ]);
        $view->setTemplate('admin-category/edit');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Edit category');
        $view->ready();
        
        return true;
    }
    
    public function deleteAction($id)
    {
        $this->setMessage('error', 'Can not delete the category');
        
        if (isset($_POST['csrf'])) {
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrfArray();
                      
            if (in_array($hidden, $csrf)) {    
                $this->deleteCsrf();
                
                $category = Category::getById($this->clearInt($id));
                
                if ($category) {
                    $category->deleteArticlesImages();
                    
                    if ($category->delete((int)$id)) {
                        $this->setMessage('success', 'The category successfully deleted!');                      
                        $this->getMessage('error'); // This will delete 'error' message
                    }
                }
            }  
        }
        
        $this->redirectTo('/admin/manage-categories');
    }
}
