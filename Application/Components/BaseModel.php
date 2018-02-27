<?php

namespace Application\Components;

abstract class BaseModel 
{
    private static $table;
    private $data = [];
    
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
    
    // Without __get method did not work (\PDO::FETCH_CLASS, $className)
    public function __get($name)
    {
        return $this->data[$name];
    }
    
    private static function getPrimaryKeyName()
    {
        $sql = "SHOW KEYS FROM " . static::$table . " WHERE Key_name = 'PRIMARY'";
        $db = DB::getConnection();
        $result = $db->query($sql);
        $row = $result->fetch();
        if ($row) {
            return $row['Column_name'];
        }
    }
    
    public static function customQuery($sql, $params = [])
    {
        $className = get_called_class();
        DB::setClassName($className);
        
        if ($params) {
            $result = DB::query($sql, $params);
        } else {
            $result = DB::query($sql);
        }
        
        return $result ?: false;
    }
    
    public static function getRowsCount($column = false, $value = false)
    {
        $primaryKeyName = self::getPrimaryKeyName();
        
        $className = get_called_class();
        DB::setClassName($className);
        
        $sql  = "SELECT COUNT(";
        $sql .= $primaryKeyName;
        $sql .= ")";
        $sql .= "AS count ";
        $sql .= "FROM ";
        $sql .= static::$table;
        if ($column && $value) {
            $sql .= " WHERE ";
            $sql .= $column . ' = :' . $column;
        }
        $sql .= " LIMIT 1";

        $result = DB::query($sql, [':' . $column => $value]);
        return $result[0]->count ?: false;
    }
    
    public static function getAll($order = false, $desc = false, $limit = false, $offset = false)
    {
        $limit = abs((int)$limit);
        $className = get_called_class();
        $primaryKeyName = self::getPrimaryKeyName();
        
        DB::setClassName($className);
        
        $sql  = "SELECT * FROM ";
        $sql .= static::$table;
        if ($order) {
            $sql .= " ORDER BY " . $order;
        }
        if ($desc) {
            $sql .= " DESC";
        }
        if ($limit) {
            $sql .= " LIMIT " . $limit;
        }
        if ($offset) {
            $sql .= " OFFSET " . $offset;
        }
        
        $result = DB::query($sql);
        return $result ?: false;
    }
    
    public static function getById($id)
    {       
        $className = get_called_class();
        $primaryKeyName = self::getPrimaryKeyName();

        DB::setClassName($className);

        $sql  = "SELECT * FROM ";
        $sql .=  static::$table;
        $sql .= " WHERE ";
        $sql .= $primaryKeyName . ' = :' . $primaryKeyName;
        
        $result = DB::query($sql, [$primaryKeyName => $id]);
        return $result[0] ?: false;
    }
    
    public static function getByColumns($dataArray, $order = false, $desc = false)
    {
        $i = 0;
        $params = [];
        
        $className = get_called_class();
        DB::setClassName($className);

        $sql  = "SELECT * FROM ";
        $sql .= static::$table;
        foreach ($dataArray as $key => $value) {
            $i++;

            if ($i == 1) {
                $sql .= " WHERE ";
                $sql .= $key . ' = :' . $key;
                $params[':' . $key] =$value;
            }

            if ($i == 2) {
                $sql .= " AND ";
                $sql .= $key . ' = :' . $key;
                $params[':' . $key] =$value;
            }
        }
        if ($order) {
            $sql .= " ORDER BY " . $order;
        }
        if ($desc) {
            $sql .= " DESC";
        }
        
        $result = DB::query($sql, $params);
        return $result[0] ?: false;
    }
    
    public static function getAllByColumns($dataArray, $order = false, $desc = false)
    {
        $i = 0;
        $params = [];
        
        $className = get_called_class();
        DB::setClassName($className);

        $sql  = "SELECT * FROM ";
        $sql .= static::$table;
        foreach ($dataArray as $key => $value) {
            $i++;

            if ($i == 1) {
                $sql .= " WHERE ";
                $sql .= $key . ' = :' . $key;
                $params[':' . $key] =$value;
            }

            if ($i == 2) {
                $sql .= " AND ";
                $sql .= $key . ' = :' . $key;
                $params[':' . $key] =$value;
            }
        }
        if ($order) {
            $sql .= " ORDER BY " . $order;
        }
        if ($desc) {
            $sql .= " DESC";
        }
        
        $result = DB::query($sql, $params);
        return $result ?: false;
    }

    public static function search($value, $column)
    {
        /* 
        Если запрос состоит из двух или более значений то может быть в базе данных эти значения взяты в html теги. 
        Этот код делит запрос на слова и добавляет регулярное выражение к началу и к концу каждого слова.
        Если в базе эти слова вжяты в теги sql запрос всё равно найдёт их.
        
        If the query consists of two or more values, it can be in the database that these values are taken in the html tags. 
        This code divides the query into words and adds a regular expression to the beginning and end of each word. 
        If these words are typed into tags in the database, the query will still find them.
        
        Start block
        */
        
        if (strpos($value, ' ') !== false) {
            $arr = explode(' ', $value);

            $search = '';
            foreach ($arr as $key => $value) {
                $search .= '.*' . $arr[$key];
            }
        } else {
            $search = '.*' . $value . '.*';
        }
        
        /* End block */

        $className = get_called_class();
        $primaryKeyName = self::getPrimaryKeyName();
        
        DB::setClassName($className);
        
        $sql  = "SELECT * FROM ";
        $sql .= static::$table;
        $sql .= " WHERE " . $column;
        $sql .= " REGEXP :search";
        
        $result = DB::query($sql, ['search' => $search]);
        return $result ?: false;
    }
    
    private function add()
    {
        $className = get_called_class();
        DB::setClassName($className);
        
        $db = DB::getConnection();
        
        $params = [];
        foreach ($this->data as $key => $value) {
            $params[':' . $key] = $value;
        }

        $sql  = "INSERT INTO ";
        $sql .= static::$table;
        $sql .= "(" . implode(', ', array_keys($this->data)) . ")";
        $sql .= " VALUES ";
        $sql .= "(" . implode(', ', array_keys($params)) . ")";
        
        $result = DB::persist($sql, $params, $db);

        if ($result) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }
    
    private function update()
    {
        $primaryKeyName = self::getPrimaryKeyName();

        $params = [];
        $newData = [];

        foreach ($this->data as $key => $value) {
            if ($key == $primaryKeyName) {
                $params[':' . $key] = $value;
            }

            $params[':' . $key] = $value;
            $newData[] = $key . ' = :' . $key;
        }

        $sql  = "UPDATE ";
        $sql .= static::$table;
        $sql .= " SET ";
        $sql .= implode(', ', $newData);
        $sql .= " WHERE ";
        $sql .= $primaryKeyName . ' = :' . $primaryKeyName;

        return DB::persist($sql, $params);
    }
    
    public function save()
    {
        if (isset($this->data['id'])) {
            return $this->update();
        } else {
            return $this->add();
        }
    }
    
    public static function delete($id)
    {
        $primaryKeyName = self::getPrimaryKeyName();

        $sql  = "DELETE FROM ";
        $sql .= static::$table;
        $sql .= " WHERE ";
        $sql .= $primaryKeyName . ' = :' . $primaryKeyName;

        return DB::persist($sql, [$primaryKeyName => $id]);
    }
}
