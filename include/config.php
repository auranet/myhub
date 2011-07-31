<?php
	// Connect to the database =========================================================
	# Type="MYSQL"
	# HTTP="true"
	include('db_connect.php');

//	$adminemail = "vmaslov@breakt.co.il";
//	$hostname = "localhost";
//	$database = "myhubdb";
//	$dbusername = "meditron";
//	$dbpassword = "sem0dupe1";
	$conn = mysql_pconnect("$hostname", "$dbusername", "$dbpassword") or die(mysql_error());
	mysql_select_db($database, $conn) or die(mysql_error());
	// =================================================================================

	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
?>
