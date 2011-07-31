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
class UserLanguages {
    private $id;
    private $languages_id;

    private $userUpdate=array(); // assosiative array for update commits

    // constructor
    function __construct($param) {
	echo "__construct: param = ". $param;
        if (is_numeric ($param)){
		//if ($param == 1){			
            $this->loadUserDataFromDb($param);
			}
        else if (is_array($param))
            $this->fillUserDataFromParam($param);
        else{			
            echo "<pre>bad UserLanguages construct\n</pre>"; //TODO rais exception or something       
			}
        $this->commit();
    }


    /***
     * private functions
     ***/
    // setters
	
    function setUserLang($languages_id) {
        $this->languages_id = $languages_id;
        $this->userUpdate['languages_id']=$this->languages_id;
    }	
	
    private function setId($id) {
        $this->id=$id;
    }

    // constructor helpers
    private function fillUserDataFromParam($row) {
        foreach ($row as $key => $value)
                switch ($key) {
                    case 'id': $this->setId($value); break;
                    case 'languages_id': $this->setLanguage($value); break;
                }
        
        if ($this->id )
            $this->userUpdate=array(); // reset commit updates array
 
    }
	
    private function loadUserDataFromDb($id) {
		
		
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		
		//$this->fillUserDataFromParam($db->query_first("SELECT * FROM users WHERE id='".$id."'"));
        $this->fillUserDataFromParam($db->fetch_array("SELECT languages_id FROM userlang WHERE users_id='".$id."'"));
		//$this->fillUserDataFromParam($db->query_first("SELECT * FROM userlang WHERE users_id='".$id."'"));
		//$this->fillUserDataFromParam($db->fetch_array("SELECT * FROM userlang, languages WHERE userlang.language=languages.id"));
		//"SELECT * FROM userlang,languages WHERE userlang.t1_ID=table2.t2_ID AND table2.t2_ID=table3.t3_ID"
		//"SELECT * FROM table1,table2 WHERE table1.t1_ID=table2.t2_ID"
    }


    /***
	* public functions
           ***/
    // getters
    function getId() {
        return $this->id;
    }
    function getUserLang() {
        return $this->languages_id;
    }    
  
    // actions
    function delete() {
        $this->setInactive();
    }
	
    function commit() {
        if ($this->userUpdate) {
            $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
            if ($this->id)
                $db->update('languages_id', $this->userUpdate, "id='".$this->id."'");
            else
                $this->id=$db->insert('languages_id', $this->userUpdate);
				
			//$this->$userUpdate=array();
        }
		
    }   
}
?>
