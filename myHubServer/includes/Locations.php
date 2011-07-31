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
class Locations {
    private $types=array();

    private static $instance;
    private function __construct() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        foreach ($db->fetch_array("SELECT * FROM locations") as $locations)
                $this->types[$locations['id']]=$locations['location'];
    }

    public static function getType($id){
        if (!self::$instance)
            self::$instance = new Locations();
        
        return self::$instance->types[$id];
    }

    public static function getTypes(){
        if (!self::$instance)
            self::$instance = new Locations();

        return self::$instance->types;
    }
}
?>
