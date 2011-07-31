<?php
	
	//Start session
	session_start();

	//Include database connection details
	require_once('include/config.php');

	//Array to store validation errors
	$errmsg_arr = array();

	//Validation error flag
	$errflag = false;

	//Sanitize the POST values
	$login = clean($_POST['login']);
	$password = clean($_POST['password']);
	$newpassword = clean($_POST['newpassword']);
	$cnewpassword = clean($_POST['cnewpassword']);

	//Input Validations
	if($login == '') {
		$errmsg_arr[] = 'Username is missing';
		$errflag = true;
	}
	if($password == '') {
		$errmsg_arr[] = 'Password is missing';
		$errflag = true;
	}

	//If there are input validations, redirect back to the login form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: login.php");
		exit();
	}

	//Create query
	$qry="SELECT * FROM users WHERE username='$login' AND password='".sha1($password)."'";

	$result=mysql_query($qry);

	//Check whether the query was successful or not
	if($result) {
		if(mysql_num_rows($result) == 1) {
				
			$member = mysql_fetch_assoc($result);

			$id = $member['id'];
			$active = $member['active'];
			$username = $member['username'];
			
			$date = date('Y-m-d H:i:s');
			$ip = $_SERVER['REMOTE_ADDR'];
			
			//Check if username is active or not

		if ($active == 1){

				//Check User type (admin)
				$queryUserType = "SELECT usertype_id FROM users WHERE id = '".$id."'";
				$resultUserType = mysql_query($queryUserType);
				$row = mysql_fetch_array($resultUserType);
				$usertype_id = $row['usertype_id'];

				$queryGetAdmin = "SELECT * FROM usertype WHERE id = '$usertype_id'";
				$resultGetAdmin = mysql_query($queryGetAdmin);
				$row = mysql_fetch_array($resultGetAdmin);
	
				$description = $row['description'];
					
				if($description == 'Administrator'){
					// Check and Change password
					if($newpassword == '') {
						$message_arr[] = 'New Password can\'t be empty.<br />Please try again.';
						$_SESSION['FORMSG_ARR'] = $message_arr;
						session_write_close();
						header("location: changepass.php");
						exit();
					}
					if($newpassword != $cnewpassword){
						$message_arr[] = 'Passwords does not match.<br /> Please try again.';
						$_SESSION['FORMSG_ARR'] = $message_arr;
						session_write_close();
						header("location: changepass.php");
						exit();
					}else{

						// Inserts into logging database
						$type = "password changed";
						$queryInsert = "INSERT INTO logging (users_id,users_username, date, ip, type) VALUES ('$id','$username', '$date', '$ip', '$type')";
						$resultInsert = mysql_query($queryInsert);

						//Insert into users new user password
						$queryInsert = "UPDATE users SET Password = '".sha1($_POST['newpassword'])."' WHERE id = '$id'";
						$resultInsert = @mysql_query($queryInsert);

						$message_arr[] = 'Password is changed.<br /> Please login with new password.';
						$_SESSION['FORMSG_ARR'] = $message_arr;
						session_write_close();
						header("location: login.php");
						exit();
					}
				} else {
			
					//Failed for non administrative users
					$message_arr[] = 'Access is denied.<br /> Please contact your system administrator.';
					$_SESSION['FORMSG_ARR'] = $message_arr;
					session_write_close();
					header("location: login.php");
					exit();
				}
			} else {
				//Not active(deleted) users can't change password.
				session_write_close();
				header("location: login.php");
				exit();
			}
		}else {
			//Login failed
			// Inserts into logging database the login - this inserts failed.
			// If failed 5 times, it sends administrator allert

			$username = $login;
			$date = date('Y-m-d H:i:s');
			$ip = $_SERVER['REMOTE_ADDR'];
			$type = "failed";

			// Checks to see the total number of failures within the past half hour
			$time1 = date('Y-m-d H:i:s');
			$time2 = date('Y-m-d H:i:s',time()-1800);
			$time3 = date('Y-m-d H:i:s',time()+1800);

			$queryCheck = "SELECT COUNT(type) as TypeCount FROM logging WHERE type = 'failed' AND date BETWEEN '$time2' AND '$time1' AND users_username ='$username'";
			$resultCheck = mysql_query($queryCheck);

			$row1 = mysql_fetch_array($resultCheck);
			$TypeCount = $row1['TypeCount'];
			$_SESSION['TYPE_COUNT']=$TypeCount;
			
			$queryInsert = "INSERT INTO logging (users_id,users_username, date, ip, type) VALUES ('0','$username', '$date', '$ip', '$type')";
			$resultInsert = mysql_query($queryInsert);
			
				
			if ($TypeCount == 5 | $TypeCount == 10){
			// Send administrative alert by Email
				include('adminwarn-exec.php');
			}

			//Failed for for disabled users
			$errmsg_arr[] = 'Please check your username and password.';
			$errflag = true;

			$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
			session_write_close();
			header("location: login.php");
			exit();
			
		}
		
	}else {
		die("Query failed");
	}
?>


