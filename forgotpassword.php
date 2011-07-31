<?php
	
	session_start();
	
	//Include database connection details
	require_once('include/config.php');
	
	$formess='';
	if( isset($_SESSION['FORMSG_ARR']) && is_array($_SESSION['FORMSG_ARR']) && count($_SESSION['FORMSG_ARR']) >0 ) {
		foreach($_SESSION['FORMSG_ARR'] as $msg) {
		
			$formess .= $msg.' <br />';

		}
		
		unset($_SESSION['FORMSG_ARR']);

	}
		

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Forgot Password</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />

</head>

<body>
<div id="root">
	

	<div id="header">
	</div>
	
	<div id="middle" >
	
	<div id="pagediv">

    	<span class="style4">Welcome to MyHub</span><br/>
	
    	<span class="style5">password claim</span>
	
	</div>

	
        <div id="logdiv">

	<div class="style1">
	<br /><br />
	This application is for Medtronic authorized personnel only.<br />
	Please provide your Medtronic E-mail address. &nbsp;<br />
	New MyHub password will be sent to your E-mail.<br/> <br />
	</div>	<br />
	 <div id="fieldset">

	<form id="passwordreset" name="passwordreset" method="post" action="forgotpassword-exec.php">
    	<span class="style1">Email: <input name="email" type="text" class="textfield" id="login" /></span>
    	<br />
    	
	<div id="separator"><br /></div>
	
   	<input type="submit" name="Submit" value="" class="button" id="but_login"/>
    	


	</form>
</div>
</div>

<div id="errlogin">
	<div class="errmessage"><br /><?php echo "$formess"; ?></div>
</div>

</div>

<?php

	include('footer.php');

?>

</div>
</body>
</html>
