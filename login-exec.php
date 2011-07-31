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
			
			//Check if user is active or not
			if ($active == 1){
				//Login Successful
				
				//Check User type (admin)
				$id = $member['id'];

				$queryUserType = "SELECT usertype_id FROM users WHERE id = '".$id."'";
				$resultUserType = mysql_query($queryUserType);
				$row = mysql_fetch_array($resultUserType);
				$usertype_id = $row['usertype_id'];

				$queryGetAdmin = "SELECT * FROM usertype WHERE id = '$usertype_id'";
				$resultGetAdmin = mysql_query($queryGetAdmin);
				$row = mysql_fetch_array($resultGetAdmin);
	
				$description = $row['description'];
					
				if($description == 'Administrator'){
					//Login Successful

					// Sets the Session ID
					session_regenerate_id();
					$_SESSION['SESS_MEMBER_ID'] = $member['id'];

					// Inserts into logging database the login
					$type = "login";
					$queryInsert = "INSERT INTO logging (users_id,users_username, date, ip, type) VALUES ('$id','$username', '$date', '$ip', '$type')";
					$resultInsert = mysql_query($queryInsert); 				
					session_write_close();
					header("location: myHubServer/main.php");
					exit();
				
				} else {

					//Failed for non administrative users
					$errmsg_arr[] = 'Access is denied.';
					$errmsg_arr[] = 'Please contact your system administrator.';
					$errflag = true;
					// Inserts into logging database the bad login for not active user or non admin 
					$type = "denied";
					$queryInsert = "INSERT INTO logging (users_id,users_username, date, ip, type) VALUES ('$id','$username', '$date', '$ip', '$type')";
					$resultInsert = mysql_query($queryInsert);
					$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
					session_write_close();
					header("location: login.php");
					exit();
				}

			} else {
				
				// Inserts into logging database the bad login for not active user or non admin 
				$type = "denied";
				$queryInsert = "INSERT INTO logging (users_id,users_username, date, ip, type) VALUES ('$id','$username', '$date', '$ip', '$type')";
				$resultInsert = mysql_query($queryInsert);
				
				//Failed for for disabled users
				$errmsg_arr[] = 'This account is disabled.';
				$errmsg_arr[] = 'Please contact your system administrator.';
				$errflag = true;

				$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
				session_write_close();
				header("location: login.php");
				exit();

				
			}			
		}else {
			//Login failed

			// Inserts into logging database the login - this inserts failed - If failed 5 times, it locks out

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
