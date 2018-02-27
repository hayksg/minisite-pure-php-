<?php
namespace Application\Controller;
use Application\Components\AbstractController;
use Application\Components\View;
use Application\Model\Category;
use Application\Model\Article;
use Application\Model\Comment;
use Application\Model\Raiting;

class CategoryController extends AbstractController
{
    public function indexAction($id)
    {
        $id = $this->clearInt($id);
        
        $categories = Category::getCategories();
        $articles = Article::getAllByColumns(['category_id' => $id, 'is_public' => 1], 'id', true);
        
        $view = new View([         
            'categories' => $categories,
            'articles'   => $articles,
        ]);
        
        $view->setTemplate('category/index');
        $view->setHeadTitle('Categories');
        $view->ready();
        
        return true;
    }
    
    public function pageAction($id)
    {
        $id = $this->clearInt($id);
        
        $avgRaiting    = Raiting::getAVGRaiting($id);
        $raitingsCount = Raiting::getRowsCount('article_id', $id);

        $article = Article::getById($id);
        $categories = Category::getCategories();
        $comments = Comment::getAllByColumns(['article_id' => $id]);

        $view = new View([         
            'categories'    => $categories,
            'article'       => $article,
            'comments'      => $comments,
            'avgRaiting'    => $avgRaiting,
            'raitingsCount' => $raitingsCount,
        ]);
        
        $view->setTemplate('category/page');
        $view->setHeadTitle($article->title);
        $view->ready();
        
        return true;
    } 
}
