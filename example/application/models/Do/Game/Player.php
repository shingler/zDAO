<?php
class Application_Model_Do_Game_Player extends Application_Model_RedisObject
{
    public $index;                          //索引
    public $userId;                         //数字用户id
    public $status = 1;                     //0 托管； 1 正常游戏 
    public $isWinner = null;                //是否是胜利的一方
    public $ifGetReward = 0;                //是否已经领取一局游戏战斗奖励
    public $getRequestRewardTime;           //领取催促奖励时间戳
    //public $stepRewards = array();          //废弃:玩家领过步数奖励的数组

    const ISWINNER_WIN = 1;
    const ISWINNER_LOSE = 0;

    public function init($userId, $index)
    {
        $this->userId = $userId;
        $this->index = $index;        
    }
}