<?php
require_once '../auth.php';
require_once 'includes/User.php';

$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();
 
require 'exportsurveycsv.inc.php';
 
 
if (isset($_SESSION['TABLES'])) {
    $tables = $_SESSION['TABLES'];
}else $tables = 1;

$table="surveys_report"; // this is the tablename that you want to export to csv from mysql.
$today = getdate();
$filename = 'export_surveys_'.$today['mday'].'-'.$today['mon'].'-'.$today['year'].'.csv';

exportMysqlToCsv($table,$filename);

 
?>