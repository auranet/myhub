<?php

require_once 'includes/php_mysql_class/config.inc.php';
require_once 'includes/php_mysql_class/Database.singleton.php';

/**
 * Usage:
 * $baba = new User($id);
 * <$id> is db user's id
 * or:
 * $booba = new User($array);
 * <$array> is an assosiative array of either the whole row from the database (i.e. from Users),
 *          or for a new user, at least: 'firtname'=>'moshe', 'location'=>'ufnik', 'business'=>'mufnik', 'usertype'=>'orange beast', 'password'=>'seamonkey'
 */
class UserAttributes {
    private $id;
    private $language;
    private $location;
    private $business;
    private $active;

    private $userUpdate=array(); // assosiative array for update commits

    // constructor
    function __construct($param) {
        if (is_int($param))
            $this->loadUserDataFromDb($param);
        else if (is_array($param))
            $this->fillUserDataFromParam($param);
        else
            echo "<pre>bad user construct\n</pre>"; //TODO rais exception or something

        
        $this->commit();
    }


    /***
     * private functions
     ***/
    // setters
	
    function setLanguage($language) {
        $this->language = $language;
        $this->userUpdate['language']=$this->language;
    }
	
    function setLocation($location) {
        $this->location = $location;
        $this->userUpdate['location']=$this->location;
    }
	
    private function setBusiness($business) {
        $this->business = $business;
        $this->userUpdate['business']=$this->business;
    }
	
    private function setId($id) {
        $this->id=$id;
    }

    // constructor helpers
    private function fillUserDataFromParam($row) {
        foreach ($row as $key => $value)
                switch ($key) {
                    case 'id': $this->setId($value); break;
                    case 'language': $this->setLanguage($value); break;
                    case 'location': $this->setLocation($value); break;
                    case 'business': $this->setBusiness($value); break;
                }
        
        if ($this->id )
            $this->userUpdate=array(); // reset commit updates array
 
    }
    private function loadUserDataFromDb($id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $this->fillUserDataFromParam($db->query_first("SELECT * FROM users WHERE id='".$id."'"));
    }


    /***
     * public functions
     ***/
    // getters
    function getId() {
        return $this->id;
    }
    function getLanguage() {
        return $this->language;
    }
    function getLocation() {
        return $this->location;
    }
    function getBusiness() {
        return $this->business;
    }

    
    // actions
    function delete() {
        $this->setInactive();
    }
    function commit() {
        if ($this->userUpdate) {
            $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
            if ($this->id)
                $db->update('users', $this->userUpdate, "id='".$this->id."'");
            else
                $this->id=$db->insert('users', $this->userUpdate);
        }
		this->$userUpdate=array();
    }   
}
?>
