<?php

namespace Application\Components;

use Application\Components\Session;
use Application\Components\FunctionsLibrary as FL;

abstract class AbstractController extends AbstractBase
{        
    public function redirectTo($location = false)
    {
        if ($location) {
            header("Location: {$location}");
            exit;
        }
    }
    
    public function getCsrf()
    {
        $csrf = Session::getSession('csrf');
        Session::deleteSession('csrf');
        return $csrf;
    }
    
    public function getCsrfArray()
    {
        $csrf = Session::getSession('csrf-array');
        return $csrf;
    }        
    
    public function deleteCsrf()
    {
        Session::deleteSession('csrf-array');
        Session::deleteSession('csrf');
        return true;
    }
    
    public function getCategoryWhichHaveNotParentCategory($categories)
    {
        $result = [];
        
        foreach ($categories as $category) {
            foreach ($categories as $item) {
                if ($category->id === $item->parent_id) {
                    continue 2;
                }
            }
            $result[] = $category; 
        }
        
        return $result;
    }
    
    public static function isLogged()
    {
        if (isset($_COOKIE['email'])) {
            $key = base64_encode('tramvai10avtobus5rubinyanc8');
            $decrypted = FL::dataDecrypt($_COOKIE['email'], $key);
            return $decrypted;
        } else {
            $admin = Session::getSession('admin');
            if (isset($admin) && is_string($admin)) {
                $email = unserialize($admin)->email;
                return $email;
            }
        }
            
        return false;  
    }
    
    protected function uploadImage($files, $pathFolder)
    {
        $output = '';
        
        if ($files["file"]["error"] != 4) {
            
            if (isset($files["file"]["error"]) && $files["file"]["error"] != 0) {
                $output = 'The uploaded file exceeds the upload_max_filesize';
            } else {
                if (is_dir(static::IMAGE_DIR)) {        
                    $tmp_name = $files["file"]["tmp_name"];
                    $imageSize = getimagesize($tmp_name);

                    $valid_types = array("image/jpg", "image/JPG", "image/jpeg", "image/bmp", "image/gif", "image/png");

                    if (in_array($imageSize['mime'], $valid_types)) {
                        // basename() may prevent filesystem traversal attacks;
                        $name = $this->clearStr(basename($files["file"]["name"]));

                        $uniqueId = uniqid();
                        $name = $uniqueId . $name;

                        if (! $this->hasErrorMessage()) {
                            if (move_uploaded_file($tmp_name, static::IMAGE_DIR . '/' . $name)) {
                                $output = '/img/' . $pathFolder . '/' . $name;
                            }
                        } 
                    } else {
                        $output = 'Uploaded file must be an image';
                    }
                }  
            }
            
        }

        return $output;
    }
    
    protected function deleteImage($obj)
    {
        $image = static::IMAGE_DIR . '/' . basename($obj->image);

        if (is_file($image)) {
            unlink($image);
            return true;
        }

        return false;
    }
    
    protected function clearFromBadWords($str)
    {
        $words        = ['Cunt', 'Motherfucker', 'Fuck', 'Wanker', 'Nigger', 'Bastard', 'Prick', 'Bollocks', 'Arsehole', 'Paki', 'Shag', 'Whore', 'Twat', 'Spastic', 'Piss off', 'Spastic', 'Slag'];
        $changedWords = ['C**t', 'Mot******ker', 'F**k', 'Wa**er', 'N****r', 'Ba***rd', 'P***k', 'Bo****ks', 'Ar****le', 'P**i', 'S**g', 'W***e', 'T**t', 'Sp***ic', 'Pi** **f', 'Sp***ic', 'S**g'];
        
        $filteredStr = str_ireplace($words, $changedWords, $str);
        
        if ($str == strtolower($str)) {
            $filteredStr = strtolower($filteredStr);
        } elseif ($str == strtoupper($str)) {
            $filteredStr = strtoupper($filteredStr);
        } elseif ($str == ucfirst($str)) {
            $filteredStr = ucfirst($filteredStr);
        }            
        
        return $filteredStr;
    }
}
