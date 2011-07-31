<?php

include ('../include/db_connect.php');

define('DB_SERVER', $hostname);
define('DB_USER', $dbusername);
define('DB_PASS', $dbpassword);
define('DB_DATABASE', $database);
define('BUTTONPATH', "../webdev/Images/Menu/Buttons/");
define('THUMBNAILPATH', "thumbnails/");
define('IMPORTCSV', "csv/importUsers.csv");

function clean($str) {
	$str = @trim($str);
	if(get_magic_quotes_gpc()) {
		$str = stripslashes($str);
	}
	return mysql_real_escape_string($str);
}

// replace all occurrences of " by ''
function double_to_single($str) {
	$chars = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);
        $out_arr = array();
	foreach ($chars as $index => $char) {
		if (ord($char) == 34) {
			$out_arr[] = "'";
			$out_arr[] = "'";
		} else {
			$out_arr[] = $char;
		}
	}
	$out_string = implode("", $out_arr);
	return $out_string;
}

function cleanme ($string) {
	return double_to_single(stripslashes($string));
}

?>
