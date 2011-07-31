<?php
	session_start();
	
	//Include database connection details
	require_once('include/config.php');
	
	$errmess='';
	if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) {
			
		unset($_SESSION['ERRMSG_ARR']);

	}

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
<title>Change Password</title>
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="root">
	

	<div id="header"></div>
	
	<div id="middle" >
	
	<div id="pagediv">

    	<span class="style4">Welcome to MyHub</span><br/>
	
    	<span class="style5">change password</span>
	
	</div>

	
        <div id="logdiv">
      
	    <div class="style1"><br /><br />
	    This application is for Medtronic authorized personnel only.<br />
	    Only MyHub administrators can use it.<br /><br />
	    </div>
    
        <div id="fieldset">

		<form name="loginForm" method="post" action="changepass-exec.php">
  
	    	<span class="style1">Username:&nbsp;<input name="login" type="text" class="textfield" id="login" /></span>
	    	<div id="separator"><br /></div>
    	
			<span class="style1">Password:&nbsp;<input name="password" type="password" class="textfield" id="password" /></span>
			<div id="separator"><br /><br /></div>
		
			<span class="style1">New Password:&nbsp;<input name="newpassword" type="password" class="textfield" id="newpassword" /></span>
			<div id="separator"><br /></div>

			<span class="style1">Confirm:&nbsp;<input name="cnewpassword" type="password" class="textfield" id="cnewpassword" /></span>
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
