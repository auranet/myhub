<?php		
require_once '../auth.php';
require_once 'includes/User.php';
require_once 'includes/Business.php';
require_once 'includes/Locations.php';

$_SESSION['ERRPASSWORD']='';
function isValidPassword($password){          
      if ($password !=''){		 
         return true;
      }
      else {
	    $_SESSION['ERRPASSWORD'].="Password is empty.\n";
        return false;
      }   
}

if (! isset($_POST['Submit'])) {
    $_POST['Submit'] = 'Login';
};
if (isset($_SESSION['SESS_MEMBER_ID'])) {
    $userIdSession = $_SESSION['SESS_MEMBER_ID'];
}
        
if(isset($_REQUEST['userId'])){			
    $_SESSION['SESS_EDIT_ID']=$_REQUEST['userId'];
	}

$userId=$_SESSION['SESS_EDIT_ID'];

$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();


$my_user = new User((int)$userId);			
$privilege=$my_user->getType();
$firstName=$my_user->getFirstName();
$lastName=$my_user->getLastName();
$email=$my_user->getUserName();
$password='****';//$my_user->getPassword(); 	
$location=$my_user->getLocation();
$businessBd=$my_user->getBusiness();		

$businessesArr = Business::getTypes();
$business=$businessBd;		

$privilegesArr = Usertype::getTypes();		
$locationsArr = Locations::getTypes();
$privilegekey = array_search($privilege, $privilegesArr);
$locationkey = array_search($location, $locationsArr);
$countbusiness = count($business);



$my_userIdSession = new User((int)$userIdSession);
$loged="You are logged in as " . $my_userIdSession->getFirstName() . " " . $my_userIdSession->getLastName();

if($my_userIdSession->getType()!='Administrator'){
		session_write_close();
		header("location: ../login.php");
		exit();
}

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>


        <title>Meditronic LTD</title>
        <meta http-equiv="Content-Language" content="English" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <link href="css/style.css" rel="stylesheet" type="text/css" />
    </head>


    <body>



        <?php
        //echo ' POST[Submit] = '.$_POST['Submit'];

        if (isset($_POST['Home'])) {
            $home = $_POST['Home'];
            
            $url = "main.php?userId=".$userId;
            header("Location: $url");
        }
        elseif (isset($_POST['Logout'])) {
            $logout = $_POST['Logout'];
            
            $url = "../login.php?userId=".$userId;
            header("Location: $url");
        }
        elseif(isset($_POST['ManageUsers'])) {
            echo ' POST[ManageUsers] = '.$_POST['ManageUsers'];
            if($_POST['ManageUsers']=='Manage Users') {
                $_POST['ManageUsers']='';
                $url = "manager.php";
                header("Location: $url");
            }
        }
        elseif(isset($_POST['Submit'])) {
            if ( $_POST['Submit'] == 'Save Changes' ) {
                
                $_POST['Submit'] = 'Login';
                

                $newbusinessesArr=array();
                foreach ($businessesArr as $key => $value) {
                    if(isset($_POST['businessselect'.$key])) {
                        
                        if($_POST['businessselect'.$key] == 'on') {
                            
                            $newbusinessesArr[$key] = $value;
                        }
                    }
                }
                
                
                $business=$newbusinessesArr;
                $privilege= $_POST['privilegesselect'];
                $location= $_POST['locationselect'];              
                $firstName=$_POST['firstName'];
                $lastName=$_POST['lastName'];
                $email=$_POST['email'];
                $password=$_POST['password'];  
				$validPassword=isValidPassword($password);	
                //$my_user = new User((int)$userId);
				if( $validPassword ){
	                $my_user->setType($privilege);
	                $my_user->setFirstName($firstName);
	                $my_user->setLastName($lastName);
	                if($password != '****')
	                    $my_user->setPassword(sha1($password));

	                $my_user->setLocation($location);
	                $my_user->updateBusiness($newbusinessesArr);
					print_r($my_user);
	                $my_user->commit();                               
	                $privilegekey = $privilege;
	                $locationkey = $location;	                	               
	                //$password='****';
					$url = "manager.php";
					header("Location: $url");
				}
				else
					 $password='****';
            }


        }

?>




        <?php
        include 'header.php';
?>
        <form name="edituser" action="edituser.php" method="post">


            <div id="middle" style="height:auto; min-height:600px">


                <table CELLSPACING="0"  style=" margin-left: auto; margin-right: auto" width="1000" cellpadding="10">
                    <tr>
                        <td align="left"width="260">
                            <input id="home" type=button onClick="location.href='main.php'" value='Home'/>
                            <input id="middlebutton" type=button onClick="location.href='manager.php'" value='Manager Users'/>

                        </td>
                        <td align="left">
                            <a>Edit user</a>

                        </td>
                        <td  >&nbsp;</td>
                    </tr>
                    <tr >
                        <td >&nbsp;</td>
                        <td align="center"> <h1>Edit user</h1>	</td>	
                        <td >&nbsp</td>
                    </tr>

                </table>
                <table  CELLSPACING="0"border="0" cellpadding="8"  style=" margin-left: auto; margin-right: auto" width="1000">
                    <tr>
                        <td width="300"><h2>Privileges</h2></td>
                        <?php

                        try {
                            echo '<td  ><select name="privilegesselect" value="" style="width: 400px;">';
                            /*** query the database ***/
                            foreach ($privilegesArr as $key => $value) {
                                echo '<option value="'.$key.'"';
                                if((int)$key==(int)$privilegekey) {
                                    echo ' selected';
                                }
                                echo '>'. $value . '</option>'."\n";
                            }


                            echo'</select></td>';
                        }
                        catch(PDOException $e) {
                            echo 'No Results';
                        }
                        ?>


                    </tr>

                    <tr>
                        <td ><h2>First Name</h2></td><td  ><input type="text" name="firstName" maxlength="30"
                                                                  value="<? echo "$firstName"?>" style="width: 400px;" /></td>
                    </tr>
                    <tr>
                        <td ><h2>Last Name</h2></td><td ><input type="text" name="lastName" maxlength="30"
                                                                value="<? echo "$lastName"?>" style="width: 400px;" /></td>
                    </tr>

                    <tr>
                        <td ><h2>Email</h2></td><td ><input type="text" name="email" 
                                                            value="<? echo "$email"?>" style="color:  gray;width: 400px;" readonly="readonly" /></td>
                    </tr>
                    <tr>
                        <td ><h2>Password</h2></td><td ><input type="text" name="password" onclick="passwordblank()"
                                                               value="<? echo "$password"?>" style="width: 400px;" />

                            <script type="text/javascript">
                                function passwordblank()
                                {
                                    document.edituser.password.value ="";
                                }
                            </script>

                        </td>
                    </tr>


                    <tr>
                        <td ><h2>Location</h2></td>
<?php
try {
                            echo '<td ><select name="locationselect" value="" style="width: 400px;">';
                            foreach ($locationsArr as $key => $value) {
                                echo '<option value="'.$key.'"';
                                if($key==$location) {
                                    echo ' selected';
                                }
                                echo '>'. $value . '</option>'."\n";
                            }
                            echo '</select></td>';
                        }
                        catch(PDOException $e) {
                            echo 'No Results';
                        }
?>

                    </tr>

                    <tr>
                        <td><h2>Business</h2></td>





<?php

try {
    $index=array_shift(array_keys($businessesArr));
    foreach($business as $index => $value) {
                                $whatsOn[]=$value;
                            }

                            echo '<td><div style="height:80; overflow:auto; width: 400px; border-width:2px; border-color: green;">';

                            foreach ($businessesArr as $key => $value) {

                                echo '<input type="checkbox" name="businessselect'.$key.'"';
                                foreach ($business as $key1 => $value1) {
                                    if($value==$value1) {
                                        echo 'checked="YES"';
                                        $dsp_checked="checked=\"yes\"";
                                    }else
                                        $dsp_checked="";
                                }
                                echo '>';
                                echo $value;
                                echo '<br>';

                                //echo "<input type=checkbox name=\"$value\" ".$dsp_checked." value=\"yes\">".$value."<br />";
                            }
                            // ALERT: if a name of a business equals a name of a location there will be a problem

                            echo '</div>
					</td>';

                        }
                        catch(PDOException $e) {
                            echo 'No Results';
                        }
                        $db->close();

                        ?>
                    </tr>

                </table>

           </div>
<?

						if(isset($_SESSION['ERRPASSWORD'])){
							echo '<div id="error" align="center">';		
							echo $_SESSION['ERRPASSWORD'];
							echo '</div>';
							unset($_SESSION['ERRPASSWORD']);							
						}

?>	
            </div>
            <table CELLSPACING="0" border="0" cellpadding="8"  style=" margin-left: auto; margin-right: auto" width="1000">
                <tr>
                    <th align="center">
                        <input id="submit" type="Submit" name="submit" value="Save Changes" />
                        <input type="hidden" name="Submit" value="Save Changes" />
                    </th>
                </tr>
            </table>
        </form>



<?php
include 'footer.php';
?>

    </body>
</html>
