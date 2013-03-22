<?php
class Application_Model_Do_User extends Application_Model_MultiDB_Object
{
    protected static $_name = "user";
    protected static $_primary = "userId";

    public $userId;
    public $nick;
    public $avatar;
    public $coins = 0;
    public $cash = 0;
    public $wins = 0;
    public $loses = 0;
    public $draws = 0;
    public $pvppoint = 0;
    public $level = 0;
    public $pvpScore = 100;
    public $pvpStep = 1;
    public $pvpBonus = 0;
    public $tentLevel = 1;
    public $tickets = "";
    /**
     * 返回当前登录用户。根据参数是否重新加载
     * @param boolean $reload
     * @return Application_Model_Do_User|boolean
     */
    public static function getCurrentUser($reload = false){
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity() && $reload == false){
            return $auth->getIdentity();
        }else if($auth->hasIdentity() && $reload == true){
            $userId = $auth->getIdentity()->userId;
            return self::find($userId);
        }else{
            return false;
        }
    }
}
?>
