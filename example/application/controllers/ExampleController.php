<?php
class ExampleController
{
    public function init()
    {        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);        
    }

    public function selectAction()
    {
        $uid = $this->_request->getParam("uid", "");
        if (strlen($tid) > 0) {
            //find a data object with primary key directly.
            $ticket = Application_Model_Do_User::find($uid);
            var_dump($ticket);
        } else {
            //find some object that uid cannot be 1, and order by uid
            $tickets = Application_Model_Do_User::finder()->where("userId!=?",1)->order("uid desc")->fetch();
            var_dump($tickets);
            //find a data object by specify column
            $ticket = Application_Model_Do_User::findBy("nick", "someone");
            var_dump($ticket);
        }
    }
    public function deleteAction()
    {
        $uid = $this->_request->getParam("uid", "");
        $ticket = Application_Model_Do_User::find($uid);
        //delete this object
        $ticket->delete();
    }
    public function saveAction()
    {
        $uid = $this->_request->getParam("uid", "");
        if (strlen($uid) == 0) {
            $ticket = new Application_Model_Do_User();
            //new a object, set property and save.
            $ticket->nick = "someone";
            $ticket->save();
        } else {
            $ticket = Application_Model_Do_User::find($uid);
            $ticket->nick = "awesome";
            //this object has primary value or not can make a insert or update.
            $ticket->save();
        }
    }
}