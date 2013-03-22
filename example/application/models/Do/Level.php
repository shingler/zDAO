<?php
class Application_Model_Do_Level extends Application_Model_MultiDB_Object
{
    protected static $_name = "level";
    protected static $_primary = "levelId";
    
    public $levelId;
    public $userStar;
    public $aiStar;
    public $allys;
    public $allyCountLimit;
    public $aiCountLimit;
    public $bonus;
    public $chest; //宝箱数量
    
    const LEVEL_WIN = 1;
    const LEVEL_LOST = 0;    
}