<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Object
 *
 * @author singler
 */
abstract class Application_Model_Entity extends Application_Model_Object{
    protected static $_name;
    protected static $_primary;
    protected $_data;
    protected $reflectClass;
    /**
     * 引用关系数组，元素是"tableName","referKey","foreignKey"
     * @var mixed
     */
    protected static $refers = array();


    public function __construct($data = null) {
        $className = get_called_class();
        $this->reflectClass = new ReflectionClass($className);
        $this->setup();
        if($data != null){
            $this->load($data);
        }        
    }
    
    public static function getTableName()
    {
        return static::$_name;
    }
    public static function getRefers()
    {
        return static::$refers;
    }
    public function getData()
    {
        return $this->_data;
    }
    public function getPrimary()
    {
        return static::$_primary;
    }
    public static function getObject($array){
        $object = new self;
        $object->load($array);
        return $object;
    }
    
    public function getPrimaryValue(){
        $primary = static::getPrimary();
        return $this->$primary;
    }
    public function setPrimaryValue($value){
        $primary = static::getPrimary();
        return $this->$primary = $value;
    }
    public function load($array = null)
    {
        $className = get_called_class();
        $reflectClass = new ReflectionClass($className);
       
        //为类的属性加载数据
        foreach($array as $key=>$val){
            if($reflectClass->hasProperty($key)){
                $property = $reflectClass->getProperty($key);
                if($property->isPublic() && !$property->isStatic()){
                    $key = $property->getName();
                    $this->__set($key,$val);
                }
            }else{
                //为类的引用类加载数据
                $refers = $this->getRefers();
                foreach($refers as $refCol => $ref){
                    $refectSub = new ReflectionClass(get_class($this->_data[$refCol]));
                    if($refectSub->hasProperty($key)){
                        $this->_data[$refCol]->__set($key,$val);
                    }
                }
            }
        }
    }
    /**
     * 将类的属性放入data数组
     */
    public function setup(){
        $className = get_called_class();
        $reflectClass = new ReflectionClass($className);
        //加载类的属性到data数组中去
        $properties = $reflectClass->getProperties(ReflectionProperty::IS_PUBLIC);        
        foreach($properties as $prop){
            if(!$prop->isStatic()){
                $propName = $prop->getName();
                $this->_data[$propName] = NULL;
            }
        }
        //根据引用关系，将对应的类的实例加入data数组
        $refers = $this->getRefers();
        foreach($refers as $column => $ref){
            $subClass = static::tableName2ClassName($ref["tableName"]);
            $this->_data[$column] = new $subClass;
        }
    }
    public static function obj2Array($model_object)
    {
        $reflectClass = new ReflectionClass($model_object);
        $data_arr = array();
        $properties = $reflectClass->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach($properties as $prop){
            if(!$prop->isStatic() && !is_object($prop->getValue($model_object))){
                $data_arr[$prop->getName()] = $prop->getValue($model_object);
            }
        }
        return $data_arr;        
    }   
    public function save(){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        if($this->getPrimaryValue() == null){
            $db->insert(static::$_name,static::obj2Array($this));
            $primaryValue = $db->lastInsertId();
            $this->setPrimaryValue($primaryValue );
        }  else {            
            $db->update(static::$_name,static::obj2Array($this),static::$_primary."=".$this->getPrimaryValue());
        }
    }
    /**
     * 根据主键的值查找对象及相关对象
     * @param string pk 
     * @return Application_Model_Object
     */
    public static function find($primary){        
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = $db->select();
        $select->from(static::getTableName());
        if(count(self::getRefers()) > 0){
            foreach(self::getRefers() as $ref){
                $select->join($ref["tableName"], static::getTableName().".".$ref["referKey"]."=".$ref["tableName"].".".$ref["foreignKey"]);
            }
        }
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
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = $db->select();
        $select->from(static::getTableName());
        if(count(self::getRefers()) > 0){
            foreach(self::getRefers() as $ref){
                $select->join($ref["tableName"], static::getTableName().".".$ref["referKey"]."=".$ref["tableName"].".".$ref["foreignKey"]);
            }
        }
        $select->where($column."=?", $value);
        $row = $db->fetchRow($select);
        if($row != null){
            $class = get_called_class();
            return new $class($row);
        }else{
            return null;
        }
    }
    public function __get($name) {
        return $this->_data[$name];
    }

    public function __set($name, $value) {        
        $this->_data[$name] = $value;
        $this->$name = $value;
    }
    
    public static function tableName2ClassName($tableName){
        $words = explode('_', $tableName);
        $className = "Application_Model_Do_";
        foreach($words as $w){
            $className .= ucfirst($w);
        }
        return $className;
    }
    /**
     *
     * @return \Application_Model_Finder 
     */
    public static function finder(){
        $className = get_called_class();
        $finder = new Application_Model_Finder(new $className,true);
        return $finder;
    }
    
    public function toArray()
    {
        return $this->_data;  
    }   
    /**
     *
     * @return Zend_Db_Adapter_Abstract 
     */
    public function getDBAdapter(){
        return Zend_Db_Table_Abstract::getDefaultAdapter();
    }

}

?>
