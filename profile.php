<?php

	require_once('auth.php');

	require_once('include/config.php');


$querySelect = "select * FROM users WHERE id = '" . $_SESSION['SESS_MEMBER_ID'] . "'";
$result = mysql_query($querySelect);
//echo mysql_error();


$row = mysql_fetch_array($result);

$id = $row['id'];
$firstname = $row['firstname'];
$lastname = $row['lastname'];
$currentpassword = $row['password'];
$sucess = '';
$error = '';
$changed = '0';

$logged="You are logged in as " . $firstname . " " . $lastname;


if (array_key_exists('_submit_check', $_POST)) {

		// If Form is submitted - it will run the following commands to post to the database
		$checkpassword = sha1($_POST['checkpassword']);
		$password = $_POST['password'];
		$cpassword = $_POST['cpassword'];
		if ($currentpassword == $checkpassword){
			if($password != $cpassword){
				$error = "<center><h4>Passwords does not match. Please try your submission again.</h4></center>";
				$changed = '0';
			}
			elseif ($password==$cpassword){

				if(strlen($password)>6 // at least 7 chars
					&& strlen($password)<21){ // at most 20 chars

					$queryInsert = "UPDATE users SET Password = '".sha1($_POST['password'])."' WHERE id = '$id'";
					$resultInsert = @mysql_query($queryInsert);

					//Check whether the query was successful or not
					if($resultInsert) {
				 		$sucess = "<center><h4>Password Changed Successfully.</h4></center>";
				 		$changed = '1';
					} else {

						echo mysql_errno() . ": " . mysql_error() . "\n";
					}
				} else {
					$error = "<center><h4>Passwords must contain 7-20 characters. Please try again.</h4></center>";
					$changed = '0';
				}

			}
		} else {
			$error = "<center><h4>Wrong current password.</h4></center>";
			$changed = '0';
		}
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

<?php
	include('header.php');
?>


<div id="middle">

<?php
	include('links.php');
?>
	
<!-- // Place any code after this comment -->

<div id="changepass">
<?php echo $sucess . $error . "<br /><br />";
			if ($changed == '1') {
?>

			<span align="center">Click <a href="index.php">here</a> to return.</span>
<?php
			} else {
?>

		<div id="q1" class="question"><center><u>Change Password</u></center></div><br>

        <form id="changepass" name="changepass" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
       	 <table width="350" border="0" align="center" cellpadding="2" cellspacing="0">

        <tr>
        	<td  align="right"><a style="width: 131px">Current Password:</a></td>
        	<td><input name="checkpassword" type="password" class="textfield" id="checkpassword" style="width: 137px" /></td>
		</tr>
		<tr>
        	<td  align="right"><a style="width: 131px">New password:</a></td>
        	<td><input name="password" type="password" class="textfield" id="password" style="width: 137px" /></td>
		</tr>
		<tr>
        	<td  align="right"><a style="width: 131px">Confirm password:</a></td>
        	<td><input name="cpassword" type="password" class="textfield" id="cpassword" style="width: 137px" /></td>
		</tr>
		<tr></tr>
		<tr>
			<td></td>
		   	<td align="left"><input type="submit" name="Submit" value="Submit">
		   	<input type="hidden" name="_submit_check" value="1"></td>
        </tr>

        </table>
        </form>


<?php
 		}
?>

</div>


<!-- // Place any code before this comment-->
	

</div>

<?php

	include('footer.php');

?>

</div>

</body>
</html>
