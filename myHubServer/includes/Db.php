<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of db
 *
 * @author thewizard
 */
class db {
    private static $instance = NULL;

    private function __construct() {
        /*** maybe set the db name here later ***/
    }

    public static function getInstance() {
        if (!self::$instance) {
            //TODO put in some configuration file
            self::$instance = mysql_connect('localhost', 'meditron', 'sem0dupe1'); //newPDO("mysql:host='localhost';dbname='myhubdb'", 'meditron', 'sem0dupe1');
            mysql_select_db ('myhubdb', self::$instance);
            //self::$instance-> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    return self::$instance;
    }

    //nobody can clone the instance
    private function __clone(){}

}