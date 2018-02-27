<?php

namespace Application\Components;

use Application\Components\Session;

class View extends AbstractView
{
    const VIEW_PATH   = ROOT . 'Application/View/';
    const LAYOUT_PATH = ROOT . 'Application/View/layout/';
    
    private $data      = [];
    private $template  = '';
    private $layout    = 'indexLayout';
    private $headTitle = '';
    
    public function __construct(array $viewData = []) 
    {
        $this->data = $viewData;
    }
    
    public function setTemplate($template)
    {
        $this->template = $template;
    }
    
    public function getTemplate()
    {
        return $this->template;
    }
    
    public function template()
    {
        foreach ($this->data as $key => $value) {
            $$key = $value;
        }
        
        $file = self::VIEW_PATH . $this->getTemplate() . '.phtml';
        if (is_file($file)) {
            include_once($file);
        }
    }
    
    public function layout()
    { 
        $file = self::LAYOUT_PATH . $this->getlayout() . '.phtml';
        
        if (is_file($file)) {
            include_once($file);
        }
    }
    
    public function setlayout($layout)
    {
        $this->layout = $layout;
    }
    
    public function getlayout()
    {
        return $this->layout;
    }
    
    public function setHeadTitle($headTitle)
    {
        $this->headTitle = $headTitle;
    }
    
    public function getHeadTitle()
    {
        return $this->headTitle;
    }
    
    public function ready()
    {
        $this->layout();
    }
    
    public function setCsrf()
    {
        $token = md5(uniqid(rand(), TRUE)); 
        Session::setSession('csrf', $token);
        return $token;
    }
    
    public function setCsrfArray($id)
    {
        $token = md5(uniqid(rand(), TRUE)); 
        Session::deleteSessionArray('csrf-array', $id);
        Session::setSessionArrayWithKey('csrf-array', $id, $token);      
        return $token;
    }
    
    function breadcrumbs($separator = ' &raquo; ', $home = 'Home') {
        // This gets the REQUEST_URI (/path/to/file.php), splits the string (using '/') into an array, and then filters out any empty values
        $path = array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
        
        // Determine protocol ( http or https )
        $httpReferer = $_SERVER['HTTP_REFERER'];

        /* After reloading the page we do not have a value of $_SERVER['HTTP_REFERER'], 
           because of it we need to set it to session in order to get it after reloading */
        if (isset($httpReferer)) {
            Session::setSession('http_referer', $httpReferer);
            $protocol = substr($httpReferer, 0, strpos($httpReferer, '/') - 1);
            $base = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/';
        } else {
            $httpRefererSession = Session::getSession('http_referer');    
            $protocol = substr($httpRefererSession, 0, strpos($httpRefererSession, '/') - 1);
            $base = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/';
        }
        
        // Initialize a temporary array with our breadcrumbs. (starting with our home page, which I'm assuming will be the base URL)
        $breadcrumbs = array("<a href=\"$base\">$home</a>");

        // To remove last element if it is an integer and last but one if it page (for pagination)
        $arrayValues = array_values($path);
        $lastElement = end($arrayValues);
        
        if (is_numeric($lastElement)) {
            array_pop($path);
            $arrKeys = array_keys($path);
            $last = end($arrKeys);
            
            if(end($path) == 'page') {
                array_pop($path);
                $arrKeys = array_keys($path);
                $last = end($arrKeys);
            }
        }
        
        if ($lastElement == 'page') {
            array_pop($path);
            $arrKeys = array_keys($path);
            $last = end($arrKeys);
        }
        
        // Find out the index for the last value in our path array
        $arrKeys = array_keys($path);
        $last = end($arrKeys);     
        
        $pathPart = '';
        
        // Build the rest of the breadcrumbs
        foreach ($path as $x => $crumb) {
            // Our "title" is the text that will be displayed (strip out .php and turn '_' into a space)
            $title = ucwords(str_replace(Array('.php', '_'), Array('', ' '), $crumb));
            
            $pathPart .= $crumb . '/';

            // If we are not on the last index, then display an <a> tag
            if ($x != $last)
                $breadcrumbs[] = "<a href=\"$base$pathPart\">$title</a>";
            // Otherwise, just display the title (minus)
            else
                $breadcrumbs[] = $title;
        }

        // Build our temporary array (pieces of bread) into one big string :)
        return implode($separator, $breadcrumbs);
    }

    /* This is callback function used in getSearchValue  */
    public function testPrint($item, $key, $userData)
    {
        $clearContent = $userData[0];
        $searchString = $this->clearStr($userData[1]);       

        if (is_int($item)) {
            $res    = substr($clearContent, $item, 100);
            $output = str_ireplace($searchString, "<span class='mark'>$searchString</span>", $res);
            
            echo '<span>'. $output . ' .... ' . '</span>';
        }
    }
    
    /* This and previous functions for search engine  */
    public function getSearchValue($searchString, $searchResult)
    {
        if (is_array($searchResult) && count($searchResult) > 0) {

            foreach ($searchResult as $value) {
                $regex = "/" . $searchString . "/i";
                
                /* Removing all entities and tags from database value, in order to put to preg_match_all clear data for matching with user search data */
                $clearTitle   = strip_tags(html_entity_decode($value->title));
                $clearContent = strip_tags(html_entity_decode($value->content));
                
                /* At first we are looking for matches in title, if we do not find, we are looking for in content */
                $matchResult = preg_match_all($regex, $clearTitle, $match, PREG_OFFSET_CAPTURE);  

                if (! $matchResult) {                 
                    $matchResult = preg_match_all($regex, $clearContent, $match, PREG_OFFSET_CAPTURE);
                }                
                
                if ($matchResult) {
                    echo '<p><a href="/page/' . $value->id . '">' . $value->title . '</a></p>';

                    foreach ($match[0] as $key =>  $item) {
                        
                        /* ($key > 4) means only five matched strings will be printed for every title */
                        if ($key > 4) {
                            break;
                        } 

                        array_walk_recursive($item, [new View, 'testPrint'], [$clearContent, $searchString]);
                    }

                    echo '<hr>';
                } 
            }
            
        } else {
            return false;
        }
    }
}
