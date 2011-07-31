<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Usertype
 *
 * @author thewizard
 */
class Usertype {
    private $types=array();

    private static $instance;
    private function __construct() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        foreach ($db->fetch_array("SELECT * FROM usertype") as $usertype)
                $this->types[$usertype['id']]=$usertype['description'];
    }

    public static function getType($id){
        if (!self::$instance)
            self::$instance = new Usertype();
        
        return self::$instance->types[$id];
    }

    public static function getTypes(){
        if (!self::$instance)
            self::$instance = new Usertype();

        return self::$instance->types;
    }
}
?>
