<?php
class Application_Model_Do_PvpRank extends Application_Model_Entity
{
    protected static $_name = "pvpRank";
    protected static $_primary = "rankId";
    
    public $rankId;
    public $userId;    
    public $pvpScore;
    public $rank;

    protected static $refers = array(        
        "user" => array(
            "tableName" =>"user", 
            "referKey" => "userId",
            "foreignKey" => "userId"
        )
    );
    
    public function save() {
    	$db = Zend_Db_Table_Abstract::getDefaultAdapter();
    	if ($this->rankId == null) {
    		$result = $this->findBy('userId', $this->userId);
    		if ($result != null) {
    			$db->update("pvpRank",static::obj2Array($this),"rankId=".$result->rankId);
    		} else {
    			$db->insert("pvpRank",static::obj2Array($this));
	            $this->rankId = $db->lastInsertId();
    		}
    	} else {            
            $db->update(static::$_name,static::obj2Array($this),static::$_primary."=".$this->rankId);
        }
    }
}
?>
