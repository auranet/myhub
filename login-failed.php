<?php
	
	session_start();
	
	//Include database connection details
	require_once('include/config.php');
	$errmess='<ul class="err">';
	if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) {
		foreach($_SESSION['ERRMSG_ARR'] as $msg) {
			$errmess .= '<li>'.$msg.'</li>';
		}
		
		$errmess .= '</ul>';
	
		unset($_SESSION['ERRMSG_ARR']);
	}
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Login Failed</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div id="root">


<div id="header"></div>


<div id="middle">


<p align="center">&nbsp;</p>
<h4 align="center" class="err">Login Failed!<br />
<? echo "$errmess"?>
<br />

  </h4>
  <center><p><a href="login.php">Back</a></p></center>

</div>
</div>

<?php
	include('footer.php');
?>

</body>
</html>

