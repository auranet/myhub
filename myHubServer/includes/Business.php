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
class Business {
    public $types=array();

    private static $instance;
    function __construct() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        foreach ($db->fetch_array("SELECT * FROM business") as $business)
                $this->types[$business['id']]=$business['business'];
    }

    public static function getType($id){
        if (!self::$instance)
            self::$instance = new Business();
        
        return self::$instance->types[$id];
    }

    public static function getTypes(){
        if (!self::$instance)
            self::$instance = new Business();

        return self::$instance->types;
    }
}
?>
