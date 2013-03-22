<?php
class Application_Model_Finder
{
    protected $object;
    protected $table;    
    protected $select;
    protected $dbAdapter;
    protected $refers;

    public function __construct(Application_Model_Object $object,$useRefer = false) {
        $this->object = $object;
        $this->table = $object->getTableName();
        if($useRefer){
            $this->refers = $object->getRefers();
        }
        
        $this->dbAdapter = Zend_Db_Table::getDefaultAdapter();
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
    
    public function where($column,$value=null) {
        if($value != null){
            $this->select->where($column, $value);
        }else{
            $this->select->where($column);
        }
        
        return $this;
    }
    
    public function order($spec){
        $this->select->order($spec);
        return $this;
    }
    
    public function limit($page,$limit){
        $this->select->limitPage($page, $limit);
        return $this;
    }
    
    public function fetch(){
        $results = $this->dbAdapter->fetchAll($this->select);
        $list = array();
        foreach($results as $row){
            $className = Application_Model_Object::tableName2ClassName($this->table);
            $entry = new $className;
            $entry->load($row);
            $list[] = $entry;
        }
        return $list;
    }
    
    public function getCount(){
        $select = clone $this->select;
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns("COUNT(1)");
        return $this->dbAdapter->fetchOne($select);
    }
}