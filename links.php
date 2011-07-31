<?php

	require_once('auth.php');

	require_once('include/config.php');

	
	$querySelect = "SELECT * FROM users WHERE id = '".$_SESSION['SESS_MEMBER_ID']."'";
	$resultSelect = mysql_query($querySelect);
	$row = mysql_fetch_array($resultSelect);
	$firstname = $row['firstname'];
	$lastname = $row['lastname'];
	$username = $row['username'];


//Check last login
$queryCount = "SELECT count(*) as LoginCount FROM logging WHERE users_username='".$username."' AND type='login'";
$resultCheck = mysql_query($queryCount);
$row = mysql_fetch_array($resultCheck);
$LoginCount = $row['LoginCount'];

if ( $LoginCount >= 2 ) {
	$count = $LoginCount - 2;
	$queryDate = "SELECT date FROM logging WHERE users_username='".$username."' AND type='login' LIMIT ".$count.",1";

} else {
	$queryDate = "SELECT date FROM logging WHERE users_username='".$username."' AND type='login'";
}
	$resultDate = mysql_query($queryDate);
	$row1 = mysql_fetch_array($resultDate);
	$LastDate = $row1['date'];

	
	
	//Check User type (admin)
	
	$queryUserType = "SELECT usertype_id FROM users WHERE id = '".$_SESSION['SESS_MEMBER_ID']."'";
	$resultUserType = mysql_query($queryUserType);
	$row = mysql_fetch_array($resultUserType);
	$usertype_id = $row['usertype_id'];

	$queryGetAdmin = "SELECT * FROM usertype WHERE id = '$usertype_id'";
	$resultGetAdmin = mysql_query($queryGetAdmin);
	$row = mysql_fetch_array($resultGetAdmin);

	$description = $row['description'];

	
	
?>


<div class="links" align="right">

		<a href="index.php">Home</a>
<?php if($description == 'Administrator'){ ?>
		 |
		<a href="myHubServer/index.php">Admin</a>
<?php } ?>
		 |
		<a href="profile.php">My Profile</a>
		 |
		<a href="forum/">My Forum</a>
		 |
</div>

<div align="left">
	<h2>  Welcome, <?php echo "$firstname $lastname"; ?></h2>
	<b>  Your status: <?php echo "$description";?></b><br>
	<b>  Last Login: <?php echo "$LastDate";?></b><br>
</div>

