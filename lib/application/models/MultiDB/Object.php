<?php

/*
 * ORM的简单实现，只做了数据库单表对对象的映射和封装
 * 该子类继承自Application_Model_Object，实现了数据库的一主多从结构，可以做到读写分离
 */

/**
 * Description of Object
 *
 * @author singler
 */
abstract class Application_Model_MultiDB_Object extends Application_Model_Object
{    
    /**
     * 根据主键的值查找对象
     * @param string pk 
     * @return Application_Model_Object
     */
    public static function find($primary){        
        //$db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $_slaves = new Zend_Config_Ini(APPLICATION_PATH."/configs/slaves.ini");
        $_slaves = $_slaves->toArray();
        $options = $_slaves[array_rand($_slaves)];
        $db = Zend_Db::factory('PDO_MYSQL', $options);
        $select = $db->select();
        $select->from(static::getTableName());        
        $select->where(static::$_primary."=?", $primary);
        $row = $db->fetchRow($select);
        if($row != null){
            $class = get_called_class();
            return new $class($row);
        }else{
            return null;
        }
    }    
    /**
     * 根据键的值查找对象
     * @param string column
     * @param string value
     * @return Application_Model_Object
     */
    public static function findBy($column,$value){        
        //$db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $_slaves = new Zend_Config_Ini(APPLICATION_PATH."/configs/slaves.ini");
        $_slaves = $_slaves->toArray();
        $options = $_slaves[array_rand($_slaves)];
        $db = Zend_Db::factory('PDO_MYSQL', $options);
        $select = $db->select();
        $select->from(static::getTableName());        
        $select->where($column."=?", $value);
        $row = $db->fetchRow($select);
        if($row != null){
            $class = get_called_class();
            return new $class($row);
        }else{
            return null;
        }
    }
    
    /**
     *
     * @return \Application_Model_Finder 
     */
    public static function finder(){
        $className = get_called_class();
        $finder = new Application_Model_MultiDB_Finder(new $className,false);
        return $finder;
    }
}

?>
