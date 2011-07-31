<?php

session_start();

include('include/config.php');

$email = clean($_POST['email']);

// Creates a Random Password and emails it to the user

/*
 * The letter l (lowercase L) and the number 1
 * have been removed, as they can be mistaken
 * for each other.
*/


function createRandomPassword() {
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;

	while ($i <= 7) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }

    return $pass;
}


// Usage


// Check if etered Email exist in database
$queryCheckEmail = "SELECT id,username,active FROM users WHERE username = '$email';";
$resultCheckEmail = mysql_query($queryCheckEmail);
$row = mysql_fetch_array($resultCheckEmail);
$checkname = $row['username'];
$checkactive = $row['active'];
$checkid = $row['id'];


if ($checkname) {

	if ($checkactive == 1){
	
		//Check User type (admin)
		$queryUserType = "SELECT usertype_id FROM users WHERE id = '".$checkid."'";
		$resultUserType = mysql_query($queryUserType);
		$row = mysql_fetch_array($resultUserType);
		$usertype_id = $row['usertype_id'];

		$queryGetAdmin = "SELECT * FROM usertype WHERE id = '$usertype_id'";
		$resultGetAdmin = mysql_query($queryGetAdmin);
		$row = mysql_fetch_array($resultGetAdmin);
	
		$description = $row['description'];
					
		if($description == 'Administrator'){

			$password = createRandomPassword();
			$hash = sha1($password);
			$queryRandomPassword = "UPDATE users SET Password = '$hash' WHERE username = '$email'";
			$resultRandomPassword = mysql_query($queryRandomPassword);
			
			if($resultRandomPassword) {

				$recip = $email;
				$headers = 'MIME-Version: 1.0' . "\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
				$headers .= 'Message-ID: <'. time() . rand(1,1000). '@myhub.com>' . "\n";
				$headers .= 'From: MyHub Support <noreply@myhub.com>' . "\n";
				$subject = 'MyHub Password Reset ';

				$message = "Thank you for resetting your MyHub password. " . "\n <br /> ";
				$message .= "Your new password is: " . $password . "\n <br /> ";
				$message .= "Please change it after next login.\n";

				// Sends email
				mail( $recip, $subject, $message, $headers );
				$message_arr[] = 'Password is resetted.<br />Please check your Email.';
				$_SESSION['FORMSG_ARR'] = $message_arr;

				session_write_close();
				header("location: login.php");
				exit();
					
			}else {
				die("Query failed");
			}
		}else{
		
			//Failed for non admin user
			$message_arr[] = 'Access is denied.<br />Please contact your system administrator.';
			$_SESSION['FORMSG_ARR'] = $message_arr;
			session_write_close();
			header("location: login.php");
			exit();

		}
			
	}else{

		//Failed for disabled account
			
		$message_arr[] = 'This account is not exist.<br />Please contact Server Administrator.';
		$_SESSION['FORMSG_ARR'] = $message_arr;
		session_write_close();
		header("location: forgotpassword.php");
		exit();

	}
}else{

	//Failed for unknown email
			
	$message_arr[] = 'This Email does not exist.<br />Please check your Email address.';
	$_SESSION['FORMSG_ARR'] = $message_arr;
	session_write_close();
	header("location: forgotpassword.php");
	exit();

}

?>

