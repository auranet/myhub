<?php
	//Start session
	session_start();

	//Check whether the session variable SESS_MEMBER_ID is present or not
	if(!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == '')) {
		$error = "Access Restricted";
		$_SESSION['ERRMSG_ARR'] = $errmsg;
		session_write_close();
		header("location: login.php");
		exit();
	}


?>
