<?php
class Application_Model_MultiDB_Finder extends Application_Model_Finder
{
    public function __construct(Application_Model_MultiDB_Object $object,$useRefer = false) {
        $this->object = $object;
        $this->table = $object->getTableName();
        if($useRefer){
            $this->refers = $object->getRefers();
        }
        
        //$this->dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $_slaves = new Zend_Config_Ini(APPLICATION_PATH."/configs/slaves.ini");
        $_slaves = $_slaves->toArray();
        $options = $_slaves[array_rand($_slaves)];
        $this->dbAdapter = Zend_Db::factory('PDO_MYSQL', $options);
        $this->select = $this->dbAdapter->select();
        $this->select->from($this->table);
        if($useRefer){
            if(count($this->refers) > 0){
                foreach($this->refers as $key => $ref){
                    $this->select->join($ref["tableName"], $this->table.".".$ref["referKey"]."=".$ref["tableName"].".".$ref["foreignKey"]);
                }
            }
        }
    }
}