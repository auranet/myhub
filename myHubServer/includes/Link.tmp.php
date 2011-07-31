<?php

require_once 'php_mysql_class/config.inc.php';
require_once 'php_mysql_class/Database.singleton.php';
require_once 'debug.php';

/**
 * Usage:
 * $baba = new Link($id);
 * <$id> is db link's id
 * or:
 * $booba = new Link($array);
 * <$array> is an assosiative array of either the whole row from the database (i.e. from links),
 *          or for a new link, at least: ('url'=>'http://www.mosheufnik.org', 'imgpath'=>'moshe.gif', 'description'=>'handsome', 'name'=>'Moshe Ufnik')
 */
class Link {
    private $id;
    private $url;
    private $imgpath;
    private $imgbutton;
    private $description;
    private $name;
    private $active;

    private $linkUpdate=array(); // assosiative array for update commits
    private $messageUpdate=array(); // array for messages update commits
    private $businessUpdate=array();
    private $locationUpdate=array();

    // constructor
    function __construct($param) {
        $this->messageUpdate = array();
        if (is_int($param)) {
                $this->loadLinkDataFromDb($param);
                $this->messageUpdate = $this->getMessages();
        }
        else if (is_array($param))
            $this->fillLinkDataFromParam($param);
        else
            echo "<pre>bad link construct\n</pre>"; //TODO rais exception or something
        
        $this->commit();
    }


    /***
     * private functions
     ***/
    // setters
    private function setActive() {
        if (! $this->active) {
                $this->active = TRUE;
                $this->linkUpdate['active']=1;
        }
    }
    private function setInactive() {
        $this->active = FALSE;
        $this->linkUpdate['active']=0;
    }
    private function setId($id) {
        $this->id=$id;
    }

    // constructor helpers
    private function fillLinkDataFromParam($row) {
	if (is_array($row)){
          foreach ($row as $key => $value)
                switch ($key) {
                    case 'id': $this->setId($value); break;
                    case 'url': $this->setUrl($value); break;
                    case 'imgpath': $this->setImgpath($value); break;
		    case 'imgbutton': $this->setImgbutton($value); break;
                    case 'description': $this->setDescription($value); break;
                    case 'name': $this->setName($value); break;
                    case 'active': $value ? $this->setActive() : $this->setInactive(); break;
                }
        
          if ($this->id )
              $this->linkUpdate=array(); // reset commit updates array
          else
              $this->setActive (); // handle new link
	} else {
	echo "an entry with the passed link id does not exist in table links";
	}	
    }
    private function loadLinkDataFromDb($id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $this->fillLinkDataFromParam($db->query_first("SELECT * FROM links WHERE id='".$id."'"));
    }


    /***
     * public functions
     ***/
    // getters
    function getId() {
        return $this->id;
    }
    function getUrl() {
        return $this->url;
    }
    function getImgpath() {
        return $this->imgpath;
    }
    function getImgbutton() {
	return $this->imgbutton;
    }
    function getDescription() {
        return $this->description;
    }
    function getName() {
        return $this->name;
    }
    function getMessages() {
        if(!isset($this->id)) {
            $return = array();
            return $return;
        }
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        $businesses = array();
        $locations = array();
        foreach ($db->fetch_array("SELECT messages.id, messages.message 
	            FROM messages, linkmessages 
	            WHERE messages.id=linkmessages.messages_id
	                AND linkmessages.links_id=".$this->id) as $message){

            $businesses = $this->getMsgBusinesses($message['id']);
            $locations  = $this->getMsgLocations($message['id']);

            $return[$message['id']]=array ('message' => $message['message'], 'messages_id' => $message['id'],
                                              'businesses' => $businesses, 'locations' => $locations);  
        }
        return $return;
    }
    function getMsgBusinesses($msg_id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT business.id, business.business FROM business, linkmessbus
                WHERE linkmessbus.business_id=business.id AND linkmessbus.messages_id=".$msg_id) as $value) {
            $return[$value['id']] = $value['business'];
        }
        return $return;
    }
    function getMsgLocations($msg_id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT locations.id, locations.location FROM locations, linkmessloc
                    WHERE linkmessloc.locations_id=locations.id AND linkmessloc.messages_id=".$msg_id) as $value)
            $return[$value['id']] = $value['location'];
        return $return;
    }
    function getBusinesses(){
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$return = array();
	foreach ($db->fetch_array("SELECT business.id, business.business 
	FROM business, linkbusiness
	WHERE business.id=linkbusiness.business_id
	AND linkbusiness.links_id='".$this->id."'") as $business){
		$return[$business['id']]=$business['business'];
	}
	return $return;
    }
    function getLocations(){
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$return = array();
	foreach ($db->fetch_array("SELECT locations.id, locations.location 
	FROM locations, linklocation 
	WHERE locations.id=linklocation.locations_id 
	AND linklocation.links_id='".$this->id."'") as $location){
		$return[$location['id']]=$location['location'];
	}
	return $return;
    }
    function getAllBusinesses(){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT id, business FROM business") as $item){
                $return[$item['id']]=$item['business'];
        }
        return $return;
    }
    function getAllMessages(){
    $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT id, message FROM messages") as $item){
                $return[$item['id']]=$item['message'];
        }
        return $return;
    }
    function getAllLocations(){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT id, location FROM locations") as $item){
                $return[$item['id']]=$item['location'];
        }
        return $return;
    }
    /* Usage getters */
    // Q: can php use max on dates? supposed to use lexicographical order which should work
    
	function getUsageInfo(){
		$usage_array = array ('name' => $this->getName(), 
							  'last_click' => $this->getLastClick(), 
							  'email' => $this->getEmail(), 
							  'total_clicks' => $this->getTotalClicks(), 
							  'last_month_clicks' => $this->getLastMonthClicks());
		return $usage_array;
	}
	function getLastClick() {
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		foreach ($db->fetch_array("SELECT users_id, timestamp FROM usagelinks 
		WHERE links_id='".$this->id."'") as $timestamp){
			$timestamps[] = $timestamp['timestamp'];
		}
		if (isset($timestamps))
			return max($timestamps);
		else return '0000-00-00 00:00:00';
	}
	function getEmail(){
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$last_click = $this->getLastClick();
		//echo $last_click;
		//$last_clicker_id = $db->fetch("SELECT users_id FROM usagelinks WHERE timestamp='".$last_click."'");
		$last_clicker_id_arr = $db->fetch_array("SELECT users_id FROM usagelinks WHERE timestamp='".$last_click."'");
		//print_r($last_clicker_id_arr);
		$last_clicker_email = "";
		if (isset($last_clicker_id_arr[0])){
			$last_clicker_id = $last_clicker_id_arr[0]['users_id'];
			//echo "<br />".$last_clicker_id;
			$last_clicker_email_arr = $db->fetch_array("SELECT username FROM users WHERE id='".$last_clicker_id."'");
			//print_r($last_clicker_email_arr);
			$last_clicker_email = $last_clicker_email_arr[0]['username'];
		}
		return $last_clicker_email;
	}
    function getTotalClicks() {
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		foreach ($db->fetch_array("SELECT id FROM usagelinks WHERE links_id='".$this->id."'") as $hit){
			$count[] = $hit;
		}
		if (isset($count))
			return count($count);
		else
			return 0;
	}
	function getlastMonthClicks() {
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$thirty_days_ago = date("Y-m-d H:i:s", strtotime("-30 days"));
		foreach ($db->fetch_array("SELECT id FROM usagelinks WHERE links_id='".$this->id."'
		AND timestamp <= NOW()
		AND timestamp >='".$thirty_days_ago."'") as $hit){
			$count[] = $hit;
		}
		if (isset($count))
			return count($count);
		else
			return 0;
	}
	
    // setters
    function setUrl($url) {
        $this->url = clean($url);
        $this->linkUpdate['url']=$this->url;
    }
    function setImgpath($imgpath) {
        $this->imgpath = clean($imgpath);
        $this->linkUpdate['imgpath']=$this->imgpath;
    }
    function setImgbutton($imgbutton) {
        $this->imgbutton = clean($imgbutton);
        $this->linkUpdate['imgbutton']=$this->imgbutton;
    }
    function setName($name) {
        $this->name = clean($name);
        $this->linkUpdate['name']=$this->name;
    }
    function setDescription($description) {
        $this->description = clean($description);
        $this->linkUpdate['description']=$this->description;
    }

    // actions
    function delete() {
        $this->setInactive();
    }
    function updateMessages ($messages){
        $this->messageUpdate = $messages;
    }
    function addLocation ($location){
	$this->locationUpdate[$location]=TRUE;
    }
    function removeLocation ($location){
	$this->locationUpdate[$location]=FALSE;
    }
    function updateLocations($locations){		   
		foreach ($this->getAllLocations() as $id => $name){		
			if (in_array($name, $locations)){				
				$this->addLocation($id);
			} else {				
				$this->removeLocation($id);
			}
		}
    }
    function addBusiness($business) {
        $this->businessUpdate[$business]=TRUE;
    }
    function removeBusiness($business) {
        $this->businessUpdate[$business]=FALSE;
    }
    function updateBusinesses($businesses){
    // sets elements in businessUpdate array to true iff their name is found in argument				  
		foreach ($this->getAllBusinesses() as $id => $name){
			if (in_array($name, $businesses)){
				$this->addBusiness($id);
			} else {
				$this->removeBusiness($id);
			}
		}
    }
    function logUsage($user_id){
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$row = array('users_id' => $user_id, 'links_id' => $this->id, 'timestamp' => date('Y-m-d H:i:s.u'));
	$db->insert("usagelinks", $row);
    }
    static function getMaxMessagesIndex() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $res=$db->query_first("SELECT MAX(id) FROM messages");
        return $res['MAX(id)'];
    }
    function debug_print() {
        bt_debug2("<br>--------<br>Link id   = ".$this->getId()."<br>");
        bt_debug2("Link name = ".$this->getName()."<br>");
        bt_debug2("Link url  = ".$this->getUrl()."<br>");
        bt_debug2("Link businesses = ");            bt_debug2($this->getBusinesses());
        bt_debug2("<br>Link locations = ");         bt_debug2($this->getLocations());
        bt_debug2("<br>Link messages = ");          bt_debug2($this->getMessages());
        bt_debug2("<br>Link messageUpdate = ");     bt_debug2($this->messageUpdate);
        bt_debug2("<br>--------<br>");
    }
    function commit() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        if ($this->linkUpdate) {
            if (isset($this->id))
                $db->update('links', $this->linkUpdate, "id='".$this->id."'");
            else
                $this->id=$db->insert('links', $this->linkUpdate);

	    $this->linkUpdate = array();
        }
       $db->query("DELETE FROM linkmessages WHERE links_id=".$this->id);
        // FIXME $db->query("DELETE FROM linkmessbus  WHERE messages_id=".$msg_id);
        // FIXME $db->query("DELETE FROM linkmessloc  WHERE links_id=".$this->id);
        if ($this->messageUpdate) { // FIXME: message businesses, locations
            foreach ($this->messageUpdate as $index => $value) {
		$value['message'] = clean($value['message']);
                $msg_id=$value['messages_id'];
                if($msg_id > Link::getMaxMessagesIndex()) {
					
                    $msg_id=$db->insert('messages', array('message'=>$value['message']));
                }
                else {
                    $db->query("UPDATE messages SET message='".$value['message']."' WHERE id='".$msg_id."'");
                }
                $linkmsg_id1=$db->insert('linkmessages', array('messages_id'=>$msg_id, 'links_id'=>$this->id));
                $db->query("DELETE FROM linkmessbus  WHERE messages_id=".$msg_id);
                $db->query("DELETE FROM linkmessloc  WHERE messages_id=".$msg_id);
                foreach($value['businesses'] as $ind2 => $val2) {
                        $db->insert('linkmessbus', array('business_id' => $ind2, 'messages_id' => $msg_id));
                }
                foreach($value['locations'] as $ind2 => $val2)
                    $db->insert('linkmessloc', array('locations_id' => $ind2, 'messages_id' => $msg_id));
            }
            $this->messageUpdate=array();
        }
        if ($this->businessUpdate) {
			$db->query("DELETE FROM linkbusiness WHERE links_id=".$this->id."");
            foreach ($this->businessUpdate as $index => $active){
                if ($active){
					
                    $db->insert('linkbusiness', array('business_id'=>$index, 'links_id'=>  $this->id));
					}               
            }
            $this->businessUpdate=array();
        }
        if ($this->locationUpdate) {
            $db->query("DELETE FROM linklocation WHERE links_id=".$this->id."");					            
            foreach ($this->locationUpdate as $index => $active){
                if ($active) {					
                    $db->insert('linklocation', array('locations_id'=>$index, 'links_id'=>  $this->id));
					}
            }
            $this->locationUpdate=array();
        }

    }
	
	public static function getActiveLinks(){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$result=$db->fetch_array("SELECT * FROM links WHERE active=true");
		
		return $result;
	}	
	
	public static function getAllLinks(){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$result=$db->fetch_array("SELECT * FROM links ");
		
		return $result;
	}	
	
	
	public static function getManagerLinks($query){	
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$result=$db->fetch_array($query);		
		return $result;
	}	
	
	
}
?>
