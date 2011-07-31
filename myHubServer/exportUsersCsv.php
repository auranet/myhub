<?php
require_once '../auth.php';
require_once 'includes/User.php';
require_once 'includes/Business.php';
require_once 'includes/Locations.php';
 


$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();
 
require 'exportcsv.inc.php';
 
 
if (isset($_SESSION['TABLES'])) {
    $tables = $_SESSION['TABLES'];
}else $tables = 1;

$table="users"; // this is the tablename that you want to export to csv from mysql.
$today = getdate();
$filename = 'export_'.$today['mday'].'-'.$today['mon'].'-'.$today['year'].'.csv';	
exportMysqlToCsv($table,$filename);

 
?>