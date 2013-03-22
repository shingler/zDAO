<?php
class Application_Model_RedisObject
{
    protected $_redis;
    protected $_data;
    protected $reflectClass;
    /**
     * 引用关系数组，元素是"referKey"=>"className"
     * @var mixed
     */
    protected static $refers = array();

    public function __construct($data = null)
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH."/configs/redis.ini", "general");
        $this->_redis = new Redis();
        $this->_redis->pconnect($config->host, $config->port);
        $this->_redis->select($config->db->game);

        $className = get_called_class();
        $this->reflectClass = new ReflectionClass($className);
        $this->setup();
        if($data != null){
            $this->load($data);
        }  
    }
    public static function getRefers()
    {
        return static::$refers;
    }
    public function __get($name) {
        return $this->_data[$name];
    }

    public function __set($name, $value) {        
        $this->_data[$name] = $value;
        $this->$name = $value;
    }

    public function save($key, $mixed)
    {        
        $mixed = serialize($mixed);        
        $this->_redis->set($key, $mixed);
    }

    public static function get($key)
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH."/configs/redis.ini", "general");
        $_redis = new Redis();
        $_redis->connect($config->host, $config->port);
        $_redis->select($config->db->game);
        $mixed = $_redis->get($key);
        if ($mixed) {
            $mixed = unserialize($mixed);
            //var_dump($mixed);exit;
            $class = get_called_class();
            return new $class($mixed);
        } else {
            return false;
        }
    }

    public function delete($key)
    {
        $this->_redis->del($key);
    }

    public function load($array = null)
    {
        $className = get_called_class();
        $reflectClass = new ReflectionClass($className);
       
        //为类的引用类加载数据
        $refers = $this->getRefers();
        //为类的属性加载数据
        foreach($array as $key=>$val){
            if (isset($refers[$key]) && is_array($val)) {
                $subClass = $refers[$key];
                $referValue = array();
                foreach ($val as $valItem) {
                    $referValue[] = new $subClass($valItem);
                }
                $this->__set($key, $referValue);
            } else if($reflectClass->hasProperty($key)){
                $property = $reflectClass->getProperty($key);
                if($property->isPublic() && !$property->isStatic()){
                    $key = $property->getName();
                    $this->__set($key,$val);
                }
            }else{                
                foreach($refers as $refCol => $refClass){
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
        foreach($refers as $column => $subClass){            
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
}