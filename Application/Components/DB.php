<?php

namespace Application\Components;

class DB 
{
    const DB_CONFIG_PATH = ROOT . 'config/db_params.php';
    public static $className;
    
    public static function setClassName($className)
    {
        self::$className = $className;
    }
    
    public static function getConnection()
    {
        $config = include(self::DB_CONFIG_PATH);
        $dsn = "mysql:host={$config['host']};dbname={$config['name']}";
        
        try {
            $db = new \PDO($dsn, $config['user'], $config['pass']);
            $db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            return $db ?: false;
        } catch(\PDOException $e) {
            // This exception will catch the ErrorController and write it into log file
            throw new \Exception('Database connection error');
        }
    }
    
    public static function query($sql, $params = [])
    {
        $className = self::$className;
        $db = DB::getConnection();
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        $res = $stmt->fetchAll(\PDO::FETCH_CLASS, $className);
        
        return $res ?: false;
    }
    
    public static function persist($sql, $params, $db = false)
    {
        if (! $db) {
            $db = DB::getConnection();
        }        

        $stmt = $db->prepare($sql);
        return $stmt->execute($params) ?: false;
    }
}
