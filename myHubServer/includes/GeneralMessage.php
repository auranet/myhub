<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Message
 *
 * @author thewizard
 */
class GeneralMessage {
    //put your code here
    private $id;
    private $title;
    private $message;

    private $messageUpdate=array();
    private $locationUpdate=array();
    private $businessUpdate=array();

    private $deleted=FALSE;

    // constructor
    function __construct($param) {
        if (is_int($param))
            $this->loadMessageDataFromDb($param);
         else if (is_array($param))
            $this->fillMessageDataFromParam($param);
         else
            throw new Exception('BAD CONSTRUCTOR');
    }

    /***
     * private functions
     ***/
    // constructor helpers
    private function fillMessageDataFromParam($row) {
        foreach ($row as $key => $value)
                switch ($key) {
                    case 'id': $this->setId($value); break;
                    case 'title': $this->setTitle(cleanme($value)); break;
                    case 'message': $this->setMessage(cleanme($value)); break;
                }

        if ($this->id )
            $this->messageUpdate=array(); // reset commit updates array
        else
            $this->commit(); // handle new user
    }
    private function loadMessageDataFromDb($id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $this->fillMessageDataFromParam($db->query_first("SELECT * FROM genmessage WHERE id=".$id));
    }

    /***
     * public functions
     ***/
    // getters
	function getId() {
        return $this->id;
    }
    function getTitle() {
        if (!$this->deleted)
            return $this->title;
    }
    function getMessage() {
        if (!$this->deleted)
            return $this->message;
    }
    function getLocation() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return=array();
        foreach ($db->fetch_array("SELECT locations_id FROM genmessageloc WHERE genmessage_id='".$this->id."'") as $location)
            $return[$location['locations_id']]=Locations::getType ($location['locations_id']);
        foreach ($this->locationUpdate as $index => $active)
            if ($active)
                $return[$index]=Location::getType ($index);
            else
                unset($return[$index]);
        return $return;
    }
    function getLocationComplement() {
        return array_diff(Locations::getTypes(), $this->getLocation());
    }
    function getBusiness() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return=array();
        foreach ($db->fetch_array("SELECT business_id FROM genmessagebus WHERE genmessage_id='".$this->id."'") as $business)
            $return[$business['business_id']]=Business::getType ($business['business_id']);
        foreach ($this->businessUpdate as $index => $active)
            if ($active)
                $return[$index]=Business::getType ($index);
            else
                unset($return[$index]);
        return $return;
    }
    function getBusinessComplement() {
        return array_diff(Business::getTypes(), $this->getBusiness());
    }
    // setters
	function setId($id) {
        $this->id=$id;
    }
    function setTitle($title) {
        $this->title=stripslashes($title);
        $this->messageUpdate['title']=mysql_real_escape_string($title);
    }
    function setMessage($message) {
        $this->message=stripslashes($message);
        $this->messageUpdate['message']=mysql_real_escape_string($message);
    }
    // actions
    function addLocation($location) {
         $this->locationUpdate[$location]=TRUE;
    }
    function removeLocation($location) {
        $this->locationUpdate[$location]=FALSE;
    }
    function addBusiness($business) {
        $this->businessUpdate[$business]=TRUE;
    }
    function removeBusiness($business) {
        $this->businessUpdate[$business]=FALSE;
    }
    function updateBusiness($businesses) {
        $removeBusiness=array_keys(array_diff_key($this->getBusiness(),$businesses));
        $addBusiness=array_keys(array_diff_key($businesses,$this->getBusiness()));
        foreach ($removeBusiness as $businessId)
            $this->removeBusiness ($businessId);
        foreach ($addBusiness as $businessId)
            $this->addBusiness ($businessId);
    }
    function updateLocation($locations) {
        $removeLocation=array_keys(array_diff_key($this->getLocation(),$locations));
        $addLocation=array_keys(array_diff_key($locations,$this->getLocation()));
        foreach ($removeLocation as $locationId)
            $this->removeLocation ($locationId);
        foreach ($addLocation as $locationId)
            $this->addLocation ($locationId);
    }
    function delete() {
        $this->deleted=TRUE;
    }
    function commit() {
        if ($this->deleted && $this->id) {
                $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
                $db->query("DELETE FROM genmessage WHERE id=".$this->id);
                $db->query("DELETE FROM genmessageloc WHERE genmessage_id=".$this->id);
                $db->query("DELETE FROM genmessagebus WHERE genmessage_id=".$this->id);
        } else {
            if ($this->messageUpdate) {
                $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);

                if ($this->id)
                    $db->update('genmessage', $this->messageUpdate, "id='".$this->id."'");
                else
                    $this->id=$db->insert('genmessage', $this->messageUpdate);

                $this->messageUpdate=array();
            }
            if ($this->businessUpdate) {
                $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);

                foreach ($this->businessUpdate as $index => $active)
                    if ($active)
                        $db->insert('genmessagebus', array('business_id'=>$index, 'genmessage_id'=> $this->id));
                    else
                        $db->query("DELETE FROM genmessagebus WHERE genmessage_id=".$this->id." AND business_id=".$index);

                $this->businessUpdate=array();
            }
            if ($this->locationUpdate) {
                $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);

                foreach ($this->locationUpdate as $index => $active)
                    if ($active)
                        $db->insert('genmessageloc', array('locations_id'=>$index, 'genmessage_id'=> $this->id));
                    else
                        $db->query("DELETE FROM genmessageloc WHERE genmessage_id=".$this->id." AND locations_id=".$index);

                $this->locationUpdate=array();
            }
        }
    }
	
	
	
	public static function getManagerMessages($query){	
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$result=$db->fetch_array($query);		
		return $result;
	}
	
	public static function getCountMessages(){
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$result=$db->fetch_array("SELECT * FROM genmessage");		
		return count($result);
	}
        public static function isMessagesExisted($id){
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$result=$db->query("SELECT * FROM genmessage WHERE id=".$id);
		return count($result);
	}
}
?>
