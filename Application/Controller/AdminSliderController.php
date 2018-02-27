<?php

namespace Application\Controller;

use Application\Components\View;
use Application\Components\AbstractController;
use Application\Components\AdminBase;
use Application\Model\Slider;

class AdminSliderController extends AbstractController
{
    use AdminBase;
    
    const IMAGE_DIR = ROOT . 'img/slider';
    
    public function indexAction()
    {  
        $slider = Slider::getAll();
        
        $view = new View([
            'slider' => $slider,                    
            'cnt'     => 0,         
        ]);
        $view->setTemplate('admin-slider/index');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Manage slider');
        $view->ready();
        
        return true;
    }
    
    public function addAction()
    {
        if (isset($_POST['add_slider_image'])) {
            
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrf();
            $this->deleteCsrf();
            
            if ($hidden !== $csrf) {            
                $this->setErrorMessage('csrfError', 'Please try again');             
            }
            
            $isVisible = $_POST['is_visible'] ?? 0;
            
            $image = $this->clearStr($this->uploadImage($_FILES, 'slider'));          
            
            if ($image === 'Uploaded file must be an image' || $image === 'The uploaded file exceeds the upload_max_filesize') {
                $this->setErrorMessage('imageError', $image);
            }

            if ($this->hasErrorMessage()) {             
                $this->redirectTo('/admin/manage-articles/add');
            }  
            
            $slider = new Slider();

            $slider->is_visible = $isVisible;
            $slider->image      = $image;
            
            $slider->save();
            
            $this->setMessage('success', 'The slider image successfully added!');
            $this->deleteErrorMessage(); // To delete error messages session if exists;
            $this->redirectTo('/admin/manage-slider');
        }

        $view = new View();
        $view->setTemplate('admin-slider/add');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Add image');
        $view->ready();
        
        return true;
    }
    
    public function editAction($id)
    {
        $id = $this->clearInt($id);
        $slider = Slider::getById($id);
        
        if (isset($_POST['edit_slider_image'])) {
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrf();
            $this->deleteCsrf();
            
            if ($hidden !== $csrf) {            
                $this->setErrorMessage('csrfError', 'Please try again');             
            }
            
            $isVisible = $_POST['is_visible'] ?? 0;
            
            $image       = $this->clearStr($this->uploadImage($_FILES, 'slider'));                      
            
            if ($image === 'Uploaded file must be an image' || $image === 'The uploaded file exceeds the upload_max_filesize') {
                $this->setErrorMessage('imageError', $image);
            }
            
            /* This block for deleting the old image if we change image to new */
            if ((! empty($image)) && ($image !== $slider->image)) {
                $this->deleteImage($slider);
            } else {
                $image = $slider->image;
            }
            /* End block */
            
            if ($this->hasErrorMessage()) {             
                $this->redirectTo('/admin/manage-slider/edit/' . $id);
            } 
            
            $slider->is_visible = $isVisible;
            $slider->image      = $image;
            
            $slider->save();
            
            $this->setMessage('success', 'The slider successfully edited!');
            $this->deleteErrorMessage(); // To delete error messages session if exists;
            $this->redirectTo('/admin/manage-slider');
        }
        
        $view = new View([
            'slider' => $slider,                          
        ]);
        $view->setTemplate('admin-slider/edit');
        $view->setLayout('adminLayout');
        $view->setHeadTitle('Edit image');
        $view->ready();
        
        return true;
    }
    
    public function deleteAction($id)
    {
        $id = $this->clearInt($id);

        $this->setMessage('error', 'Can not delete the slider image');
        
        if (isset($_POST['csrf'])) {
            $hidden = $this->clearStr($_POST['csrf']);
            $csrf = $this->getCsrfArray();
                      
            if (in_array($hidden, $csrf)) {    
                $this->deleteCsrf();
                $slider = Slider::getById($id);
        
                if ($slider) {
                    $this->deleteImage($slider);

                    if ($slider->delete($id)) {
                        $this->setMessage('success', 'The slider image successfully deleted!');                      
                        $this->getMessage('error'); // This will delete 'error' message
                    }
                }
            }  
        }
        
        $this->redirectTo('/admin/manage-slider'); 
    }
}
