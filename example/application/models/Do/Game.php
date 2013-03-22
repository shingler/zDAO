<?php
class Application_Model_Do_Game extends Application_Model_RedisObject
{
    public $gameId;
    public $players = array();        //参与者信息数组
    public $nextPlayer = 0;           //int 轮到谁(参与者数组索引)
    public $isRandom = 0;             //配对方式(是否random bool)
    public $status = 0;               //游戏局状态(已完成，进行中)
    public $type = 1;                 //游戏局类型(PVE,1V1,2V2)
    public $lastTime;                 //最后一个玩家行动的时间戳
    //public $step = 0;                 //废弃:当前进行到第N步
    
    const IS_RANDOM = 1;
    const IS_NOT_RANDOM = 0;
    const STATUS_DONE = 1;
    const STATUS_DOING = 0;
    //const TYPE_PVE = 1;
    const TYPE_1V1 = 1;
    //const TYPE_2V2 = 4;

    protected static $refers = array(
        "players" => "Application_Model_Do_Game_Player"
    );   

    public function init($gameId, $players, $isRandom, $type)
    {
        $this->players = $players;
        $this->isRandom = $isRandom;
        $this->type = $type;        
        $this->gameId = $gameId;
    }

    public function save()
    {
        $players = array();
        foreach ($this->players as $key=>$pObj) {            
            $players[$key] = Application_Model_RedisObject::obj2Array($pObj);
        }
        $this->players = $players;
        $mixed = Application_Model_RedisObject::obj2Array($this);
        parent::save($this->gameId, $mixed);
    }

    

}