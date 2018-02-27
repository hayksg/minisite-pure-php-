<?php

return [
    'search?search=(.*)' => 'search/index', // indexAction in SearchController

    'admin/manage-articles/page/([0-9]+)'   => 'adminArticle/index/$1',     // indexAction  in AdminArticleController
    'admin/manage-articles'                 => 'adminArticle/index',     // indexAction  in AdminArticleController
    'admin/manage-articles/add'             => 'adminArticle/add',       // addAction    in AdminArticleController
    'admin/manage-articles/edit/([0-9]+)'   => 'adminArticle/edit/$1',   // editAction   in AdminArticleController
    'admin/manage-articles/delete/([0-9]+)' => 'adminArticle/delete/$1', // deleteAction in AdminArticleController
    
    'admin/manage-categories'                 => 'adminCategory/index',     // indexAction  in AdminCategoryController
    'admin/manage-categories/add'             => 'adminCategory/add',       // addAction    in AdminCategoryController
    'admin/manage-categories/edit/([0-9]+)'   => 'adminCategory/edit/$1',   // editAction   in AdminCategoryController
    'admin/manage-categories/delete/([0-9]+)' => 'adminCategory/delete/$1', // deleteAction in AdminCategoryController
    
    'admin/manage-admins'                 => 'manageAdmin/index',     // indexAction  in ManageAdminController
    'admin/manage-admins/add'             => 'manageAdmin/add',       // addAction    in ManageAdminController
    'admin/manage-admins/edit/([0-9]+)'   => 'manageAdmin/edit/$1',   // editAction   in ManageAdminController
    'admin/manage-admins/delete/([0-9]+)' => 'manageAdmin/delete/$1', // deleteAction in ManageAdminController
    
    'admin/manage-slider'                 => 'adminSlider/index',     // indexAction  in AdminSliderController
    'admin/manage-slider/add'             => 'adminSlider/add',       // addAction    in AdminSliderController
    'admin/manage-slider/edit/([0-9]+)'   => 'adminSlider/edit/$1',   // editAction   in AdminSliderController
    'admin/manage-slider/delete/([0-9]+)' => 'adminSlider/delete/$1', // deleteAction in AdminSliderController
    
    'admin-area' => 'authentication/index',  // indexAction  in AuthenticationController
    'login'      => 'authentication/login',  // loginAction  in AuthenticationController
    'logout'     => 'authentication/logout', // logoutAction in AuthenticationController   
    
    'admin/manage-footer/editIcons'      => 'adminFooter/editIcons',      // editIconsAction       in AdminFooterController
    'admin/manage-footer/editFooterText' => 'adminFooter/editFooterText', // editFooterTextAction  in AdminFooterController
    'admin/manage-footer'                => 'adminFooter/index',          // indexAction           in AdminFooterController
    
    'admin/manage-comments'                 => 'adminComment/index', // indexAction in AdminCommentController
    'admin/manage-comments/delete/([0-9]+)' => 'adminComment/delete/$1', // deleteAction in AdminCommentController
    
    'admin' => 'admin/index', // indexAction in AdminController
    
    'about-us' => 'aboutUs/index', // indexAction in AboutUsController
    
    'contact/mail' => 'contact/mail',  // mailAction in ContactController
    'contact'      => 'contact/index', // indexAction in ContactController
    
    'portfolio' => 'portfolio/index', // indexAction in PortfolioController
    
    'comment/add' => 'comment/add', // addAction in CommentController
    
    'raiting/add' => 'raiting/add', // addAction in RaitingController
    
    'error' => 'error/index', // indexAction in ErrorController
    
    'category/([0-9]+)' => 'category/index/$1', // indexAction in CategoryController
    'page/([0-9]+)'     => 'category/page/$1',  // pageAction in CategoryController
    
    '' => 'index/index', // indexAction in IndexController
];
