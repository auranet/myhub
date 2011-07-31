<?php		
require_once '../auth.php';
require_once 'includes/User.php';
require_once 'includes/Business.php';
require_once 'includes/Locations.php';
$_SESSION['ERRMAIL']='';
$_SESSION['ERRPASSWORD']='';
$_SESSION['ERRUSEREXIST']='';

function isValidEmail($email){
      $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
     
      if (eregi($pattern, $email)){
		 
         return true;
      }
      else {
	    $_SESSION['ERRMAIL'].="Email address not valid.\n";
        return false;
      }   
}

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
    $userId = $_SESSION['SESS_MEMBER_ID'];    
}
if (! isset($_POST['email'])) {
    $_POST['email']='';
};
			
try{

$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();

		
$privilege='';
$firstName='';
$lastName='';
$email='';
$password=''; 		
$location='';
$businessBd='';	
$my_user = new User((int)$userId);	
$firstNameLogin=$my_user->getFirstName();
$lastNameLogin=$my_user->getLastName();	
$loged="You are logged in as " . $firstNameLogin . " " . $lastNameLogin;

$businessesArr = Business::getTypes();
$privilegesArr = Usertype::getTypes();		
$locationsArr = Locations::getTypes();
$business=$businessBd;		

$countbusiness = count($business);

if($my_user->getType()!='Administrator'){
		session_write_close();
		header("location: ../login.php");
		exit();
}
}catch(PDOException $e) {
                            echo 'No Results';
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
        if(isset($_POST['Submit'])) {

            if ( $_POST['Submit'] == 'Save user' ) {
                
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
                $locationselect= $_POST['locationselect'];                
                $firstName=$_POST['firstName'];
                $lastName=$_POST['lastName'];
                $email=$_POST['email'];
                $password=$_POST['password'];
                $location=$_POST['locationselect'];                
				
				$validEmail=isValidEmail($email);
				$validPassword=isValidPassword($password);
				$isUserExists=User::isUserExists($email);
				
				if($isUserExists) {
					$_SESSION['ERRUSEREXIST'].='This user '.$email. ' exists in db';
				 }
				 
				if($validEmail && $validPassword && !$isUserExists){				
	                $newUserArr['id'] = '';
	                $newUserArr['firstname'] = $firstName;
	                $newUserArr['lastname'] = $lastName;
	                $newUserArr['lastlogin'] = '';
	                $newUserArr['username'] = $email;
	                $newUserArr['usertype_id'] = $privilege;
	                $newUserArr['locations_id'] = $location;
	                $newUserArr['password'] = sha1($password);
	                $newUserArr['active'] = '';
	                				
	               
	                $my_user = new User($newUserArr);                    
	                $my_user->updateBusiness($newbusinessesArr);
	                $my_user->commit();
	                    	              
	                $_POST['firstName']='';
	                $_POST['lastName']='';
	                $_POST['email']='';
	                $_POST['password']='';
					$url = "manager.php";
					header("Location: $url");
				}
            }
        }
?>
<?php
        include 'header.php';
?>
        <form id="Form1" action="createuser.php" method="post" >

            <div id="middle" style="height:auto; min-height:600px">

                <table  style=" margin-left: auto; margin-right: auto" width="1000" cellpadding="10" CELLSPACING="0"  border="0">
                    <tr>
                        <td align="left"  style=" width: 260px">
                            <input id="home" type=button onClick="location.href='main.php'" value='Home'/>
                            <input id="middlebutton" type=button onClick="location.href='manager.php'" value='Manager Users'/>

                        </td>
                        <td align="left">
                            <a>Create user</a>
                        </td>
                        <td  >&nbsp;</td>
                    </tr>
                    <tr >
                        <td >&nbsp;</td>
                        <td align="center"><h1>Create user</h1></td>
                        <td >&nbsp;	</td>
                    </tr>

                </table>
                <table  border="0" cellpadding="8" CELLSPACING="0"  border="0"  style=" margin-left: auto; margin-right: auto" width="1000">
                    <tr>
                        <td style=" width: 300px"><h2>Privileges</h2></td>
                        <?php
                        try {
                            echo '<td ><select name="privilegesselect" value="" style="width: 400px;">';
                            /*** query the database ***/
                            foreach ($privilegesArr as $key => $value) {
                                echo '<option value="'.$key.'"';
                                if($key==$privilege) {
                                    echo ' selected';
                                }
                                echo '>'. $value . '</option>'."\n";
                            }
                            echo' </select>&nbsp;</td>';
                        }
                        catch(PDOException $e) {
                            echo 'No Results';
                        }
                        ?>

                    </tr>

                    <tr>&nbsp;
                        <td ><h2>First Name</h2></td><td  ><input type="text" name="firstName" maxlength="30"
                                                                  value="" style="width: 400px;" /></td>
                    </tr>
                    <tr>&nbsp;
                        <td ><h2>Last Name</h2></td><td ><input type="text" name="lastName" maxlength="30"
                                                                value="" style="width: 400px; "/></td>
                    </tr>
                    <tr>&nbsp;
                        <td ><h2>Email</h2></td><td ><input type="text" name="email"
                                                             style="width: 400px;" /></td>
                    </tr>
                    <tr>&nbsp;
                        <td ><h2>Password</h2></td><td ><input type="password" name="password"
                                                                style="width: 400px;" /></td>
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


                            echo '<td>';
							//echo'&nbsp&nbsp<input type="checkbox" onClick="toggle(this)" /> Toggle All<br/>';
							echo '<div style="height:80; overflow:auto; width: 400px; border-width:2px; border-color: green;">';

                            foreach ($businessesArr as $key => $value) {

                                echo '<input class="business" type="checkbox" name="businessselect'.$key.'"';

                                echo '>';
                                echo $value;
                                echo '<br>';
                            }

                            echo '</div>';							
							echo'</td>';

                        }
                        catch(PDOException $e) {
                            echo 'No Results';
                        }
?>
<script language="JavaScript">
function toggle(source) {
  checkboxes = document.getElementsByClassName('business');
  for each(var checkbox in checkboxes)
    checkbox.checked = source.checked;
}
</script>

                    </tr>

<?php
$db->close();
?>

            </table>
            </div>
<?
						if(isset($_SESSION['ERRMAIL'])){
							echo '<div id="error" align="center">';		
							echo $_SESSION['ERRMAIL'];
							echo '</div>';
							unset($_SESSION['ERRMAIL']);							
						}
						if(isset($_SESSION['ERRPASSWORD'])){
							echo '<div id="error" align="center">';		
							echo $_SESSION['ERRPASSWORD'];
							echo '</div>';
							unset($_SESSION['ERRPASSWORD']);							
						}
						if(isset($_SESSION['ERRUSEREXIST'])){
							echo '<div id="error" align="center">';		
							echo $_SESSION['ERRUSEREXIST'];
							echo '</div>';
							unset($_SESSION['ERRUSEREXIST']);							
						}
?>			
            <table CELLSPACING="0" CELLPADDING="10"  border="0"   style=" margin-left: auto; margin-right: auto" width="1000">
                <tr>
                    <th align="center">
                        <input id="submit" type="Submit"  name="submit" value="Save user" />
                        <input type="hidden" name="Submit" value="Save user" />
                    </th>
                </tr>
            </table>
        </form>
<?php
include 'footer.php';
?>

    </body>
</html>
