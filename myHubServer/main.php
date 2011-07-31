<?php		
require_once '../auth.php';
require_once 'includes/User.php';
require_once 'includes/Business.php';
require_once 'includes/Locations.php';
require_once 'includes/Link.php';
//require_once 'checktype.php';

$fieldlength=20;
function cutValue($fieldValue,$fieldlength ) {
    $value = substr($fieldValue, 0, $fieldlength);
    if($fieldlength < strlen($fieldValue))
        $value.='...';
    return $value;
}

if (! isset($_POST['Submit'])) {
    $_POST['Submit'] = 'Login';
};

if (isset($_SESSION['SESS_MEMBER_ID'])) {
    (int)$userId = $_SESSION['SESS_MEMBER_ID'];

}else{
  $url = "../login.php";
  session_write_close();
  header("Location: $url");
}
try{
$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();
$totalnumusers=User::getAllUsers();
$activenumusers=User::getActiveUsers();
$links=Link::getActiveLinks();

$my_user=new User((int)$userId);						
$privilege=$my_user->getType();
$firstName=$my_user->getFirstName();
$lastName=$my_user->getLastName();
$email=$my_user->getUserName();
$password=$my_user->getLocation();	
$location=$my_user->getLocation();
$businessBd=$my_user->getBusiness();

$loged="You are logged in as " . $firstName . " " . $lastName;
$businessesArr = Business::getTypes();
$business=$businessBd;		

$privilegesArr = Usertype::getTypes();		
$locationsArr = Locations::getTypes();

if($privilege!='Administrator'){
		session_write_close();
		header("location: ../login.php");
		exit();
}
				


//Check last login
$queryCount="SELECT count(*) as LoginCount FROM logging WHERE users_username='".$email."' AND type='login'";
$resultCheck=mysql_query($queryCount);
if(count($resultCheck)){
	$row=mysql_fetch_array($resultCheck);
	$LoginCount=$row['LoginCount'];
	}

if ( $LoginCount >= 2 ) {
    $count = $LoginCount - 2;
    $queryDate = "SELECT date FROM logging WHERE users_username='".$email."' AND type='login' LIMIT ".$count.",1";

} else {
    $queryDate = "SELECT date FROM logging WHERE users_username='".$email."' AND type='login'";
}
$resultDate = mysql_query($queryDate);
if(count($resultDate)){
	$row1 = mysql_fetch_array($resultDate);
	$lastlogin = $row1['date'];
	}
}
catch(PDOException $e)
{
	echo 'No Results';
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <head>
        <title>Main</title>
        
        <link href="css/style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
	<div id="root">
        <?php
        include 'header.php';
        ?>
        <form action="main.php" method="post"  >

          <div id="middle">
                <div style="height:30px"></div>
                <table style="table-layout:fixed;" align="center" cellpadding="2" border="0" cellspacing="0" width="1000">
                    <tr align="center">
                        <th width="50px" align="left"></th>
                        <th width="380px" align="left" >

                            <h1><div>Welcome</div><?php
                                echo "<span style='width:350px;
                                        word-wrap:break-word;'>".
                                        $firstName." ".$lastName."</span>"?></h1>

                        </th>
                        <th align="left" width="520px">
                            <h2><b><?php echo "Your status:"?></b>
                                <?php echo " $privilege"?></h2>
                            <h2><b><?php echo "Your last log in:"?></b>
                                <?php echo " $lastlogin"?></h2>
                            <h2><b><?php echo "Total numbers of users:"?></b>
                                <?php echo " $activenumusers"?></h2>

                            <br />  

							<input id="mainbutton" class=button type=button  onClick="location.href='usagereport.php'" value='Reports'>
                            <input id="mainbutton" class=button type=button  onClick="location.href='manager.php'" value='Manage Users'>
                            <input id="mainbutton" class=button type=button  onClick="location.href='edit_link.php?link_id=-1'" value='New Link'>
                        </th>

                    </tr>


                </table>
				
<div id="import" style="margin-top:20px"></div>
                <table align="center" cellpadding="2" border="0" cellspacing="0" width="1000">
					<tr>						
						<th width="50px"  align="center">&nbsp;</th>
						<th width="380px"  align="left"><h1><div>Edit Items</div></h1></th>
						<th width="520px"></th>
							
					</tr>
                    <tr>
                        <th width="50px"  align="center">&nbsp;</th>
                        <th width="380px"  align="left">
							<input id="generalbutton" type=button  onClick="location.href='generalmessages.php'" value='General Messages'>
							<div style="height:205px"></div>										
                        </th>
                        <?php
                        function popup($txt, $popup) {
                            echo '<a href="main.php" title='. "$popup" . '>'." $txt".'</a>';
                        }

                        try {
                            $linklength=15;

                            echo '<th align="left"><div style="height: 250px; overflow:auto; padding:0; border-width:2px; ">';
                            $parity_counter=0;
                            foreach ($links as $key => $value) {
                                if (!$parity_counter) {
                                    echo '<br>';
                                }
                                $link_id=$value['id'];
                                $link_name = stripslashes($value['name']);
                                $link_name_cut = substr($link_name, 0, $linklength);
                                $link_name_onlyletters = ereg_replace("[^A-Za-z0-9]", "", $link_name_cut);
								$link_name_id=$link_name_onlyletters.'_'.$link_id;
								
                                //echo "<input id=\"mainbutton\" type=\"submit\" name=\"site_name\" value=\"$link_name_cut\" ";
								echo" <input id=\"mainbutton\" class=button type=button  onClick=\"location.href='edit_link.php?link_id=$link_id'\" value=\"$link_name_cut\">";
                                //popup("" , $link_name);



                                $parity_counter = (($parity_counter + 1) % 3);
                            }
                            echo '</div><br><br></th>';
                        }
                        catch(PDOException $e) {
                            echo 'No Results';
                        }
                        ?>
						
					
                    </tr>

                    <br><br>
                </table>
            </div>


        </form>

        <?php
        include 'footer.php';
        ?>
</div>
    </body>
</html>

