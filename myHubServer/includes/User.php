<?php

require_once 'php_mysql_class/config.inc.php';
require_once 'php_mysql_class/Database.singleton.php';
require_once 'Usertype.php';
require_once 'Business.php';

class User {
    private $id;
    private $firstName;
    private $lastName;
    private $userName;
    private $lastLogin;
    private $userTypeId;
    private $locationId;
    private $password; 
    private $active;

    private $userUpdate=array(); // assosiative array for user update commits
    private $businessUpdate=array(); // array for business update commits

    // constructor
    function __construct($param) {
        if (is_int($param))
            $this->loadUserDataFromDb($param);
        else if (is_array($param))
            $this->fillUserDataFromParam($param);
        else
            echo "<pre>bad user construct\n</pre>"; //TODO rais exception or something

        
    }


    /***
     * private functions
     ***/
    // setters
    private function setUserName($userName) {
        $this->userName = $userName;
        $this->userUpdate['username']=$userName;
    }
    private function setActive() {
        if (! $this->active) {
                $this->active = TRUE;
                $this->userUpdate['active']=1;
        }
    }
    private function setInactive() {
        $this->active = FALSE;
        $this->userUpdate['active']=0;
    }
    private function setLastLogin() {
        $this->lastLogin = date("Y-m-d H:i:s");
        $this->userUpdate['lastlogin']=$this->lastLogin;
    }
    private function setId($id) {
        $this->id=$id;
    }

    // constructor helpers
    private function fillUserDataFromParam($row) {
	
        foreach ($row as $key => $value)
				
                switch ($key) {
                    case 'id': $this->setId($value); break;
                    case 'firstname': $this->setFirstName(cleanme($value)); break;
                    case 'lastname': $this->setLastName(cleanme($value)); break;
                    case 'username': $this->setUserName(cleanme($value)); break;
                    case 'usertype_id': $this->setType(cleanme($value)); break;
                    case 'locations_id': $this->setLocation(cleanme($value)); break;
                    case 'password': $this->SetPassword(cleanme($value)); break;
                    case 'active': $value ? $this->setActive() : $this->setInactive(); break;
                }
        
        if ($this->id )
            $this->userUpdate=array(); // reset commit updates array
        else
            $this->setActive (); // handle new user
    }
    private function loadUserDataFromDb($id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $this->fillUserDataFromParam($db->query_first("SELECT * FROM users WHERE id=".$id));
    }
    // helpers
    function genmessageBusinessHelper() {
        //$needOr=FALSE;
        $return="";

        foreach (array_keys($this->getBusiness()) as $businessid) {
            //if ($needOr)
                $return=$return." OR ";
            $return=$return."genmessagebus.business_id=".$businessid;
           //$needOr=TRUE;
        }

        return $return;
    }


    /***
     * public functions
     ***/
    // getters
    function getId() {
        return $this->id;
    }
    function getFirstName() {
        return $this->firstName;
    }
    function getLastName() {
        return $this->lastName;
    }
    function getUserName() {
        return $this->userName;
    }
    function getType() {
        return Usertype::getType($this->userTypeId);
    }
    function getLastLogin() {
        return $this->lastLogin;
    }
    function getLocation() {
        return $this->locationId;
    }
    function getBusiness() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$return=array();
        foreach ($db->fetch_array("SELECT business_id FROM userbusiness WHERE users_id='".$this->id."'") as $business)
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
    function getBusinessIds() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$return=array();
        foreach ($db->fetch_array("SELECT business_id FROM userbusiness WHERE users_id='".$this->id."'") as $business)
            $return[] = $business['business_id'];
	return $return;
    }
    function getGeneralMessage() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$return = array();
        foreach ($db->fetch_array("SELECT DISTINCT genmessage.id, genmessage.title, genmessage.message FROM genmessage
                LEFT JOIN genmessagebus ON genmessagebus.genmessage_id=genmessage.id
                LEFT JOIN genmessageloc ON genmessageloc.genmessage_id=genmessage.id
                WHERE genmessageloc.locations_id='".$this->locationId."' ".$this->genmessageBusinessHelper()) as $genmessage)
            $return[$genmessage['id']]=array ('title' => $genmessage['title'], 'message' => $genmessage['message']);
        return $return;
    }
    function getLinks(){
		$location_links=$this->getLocationLinks();
		$business_links=$this->getBusinessLinks();
		return $business_links + $location_links;
	}
	function getLocationLinks(){
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		foreach ($db->fetch_array("SELECT links.id, links.name FROM users 
		JOIN linklocation ON users.locations_id=linklocation.locations_id 
		JOIN links ON linklocation.links_id=links.id
		WHERE users.id='".$this->id."' AND links.active='1'") as $loclink){
			$return[$loclink['id']] = $loclink['name'];
		}
		if (isset($return))
			return $return;
		else	
			return array();
	}
	function getBusinessLinks(){
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		foreach ($db->fetch_array("SELECT links.id, links.name FROM users 
		JOIN userbusiness ON users.id=userbusiness.users_id 
		JOIN linkbusiness ON userbusiness.business_id=linkbusiness.business_id 
		JOIN links ON linkbusiness.links_id=links.id
		WHERE users.id='".$this->id."' AND links.active='1'") as $buslink){
			$return[$buslink['id']] = $buslink['name'];
		}
		if (isset($return))
			return $return;
		else
			return array();
	}
    // setters
    function setFirstName($firstName) {
        $this->firstName = $firstName;
        $this->userUpdate['firstname']=mysql_real_escape_string($firstName);
    }
    function setLastName($lastName) {
        $this->lastName = $lastName;
        $this->userUpdate['lastname']=mysql_real_escape_string($lastName);
    }
    function setType($type) {
        $this->userTypeId=$type;
        $this->userUpdate['usertype_id']=mysql_real_escape_string($type);
    }
    function setPassword($password) {
        $this->password = $password;
        $this->userUpdate['password']=mysql_real_escape_string($password);
    }
    function setLocation($location) {
        $this->locationId=$location;
        $this->userUpdate['locations_id']=mysql_real_escape_string($location);
    }

    // actions
    function delete() {
        $this->setInactive();
    }
    function addBusiness($business) {
        $this->businessUpdate[$business]=TRUE;
    }
    function removeBusiness($business) {
        $this->businessUpdate[$business]=FALSE;
    }

    function commit() {
	
        if ($this->userUpdate) {
            $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);

            if ($this->id)
                $db->update('users', $this->userUpdate, "id='".$this->id."'");
            else
                $this->id=$db->insert('users', $this->userUpdate);

            $this->userUpdate=array();
        }

        if ($this->businessUpdate) {
            $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
            
            foreach ($this->businessUpdate as $index => $active)
                if ($active)
                    $db->insert('userbusiness', array('business_id'=>$index, 'users_id'=>  $this->id));
                else
                    $db->query("DELETE FROM userbusiness WHERE users_id=".$this->id." AND business_id=".$index);
            
            $this->businessUpdate=array();
        }
    }
	
	
	function updateBusiness($business) {
       $removeBusiness=array_keys($this->getBusiness());
       $addBusiness=array_keys($business);
       foreach ($removeBusiness as $businessId)
           $this->removeBusiness ($businessId);
       foreach ($addBusiness as $businessId)
           $this->addBusiness ($businessId);
   }
   
   
	//IR added functions:
	public static function getAllUsers(){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$result=$db->fetch_array("SELECT * FROM users");
		
		return count($result);
	}
	
	public static function getActiveUsers(){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$result=$db->fetch_array("SELECT * FROM users WHERE active=true");
		
		return count($result);
	}
	
	public static function getAllLinks(){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$result=$db->fetch_array("SELECT * FROM links ");
		
		return $result;
	}
	
	
	function getPassword() {
        return $this->password;
    }
	
	public static function getManagerUsers($query){	
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$result=$db->fetch_array($query);		
		return $result;
	}
	
	public static function isUserExists($userName){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$result=$db->query("SELECT username FROM users WHERE username='".$userName."'");
		
		return mysql_num_rows($result);
	}
	public static function getUserIdByUserName($userName){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);	
	$result=$db->query("SELECT id FROM users WHERE username='".$userName."'");
	$row = mysql_fetch_array($result);
	return $row['id'];;
	}	
	
	public static function getUserNameById($userId){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);	
	$result=$db->query("SELECT username FROM users WHERE id='".$userId."'");
	$row = mysql_fetch_array($result);
	return $row['username'];;
	}		
}
?>
