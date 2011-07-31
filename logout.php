<?php
	//Start session
	session_start();


	//Unset the variables stored in session
	unset($_SESSION['SESS_MEMBER_ID']);

	session_write_close();
	header("location: login.php");
	exit();
?>

