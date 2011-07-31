<?php		
require_once '../auth.php';
require_once 'includes/User.php';
require_once 'includes/Business.php';
require_once 'includes/Locations.php';
require_once 'includes/Message.php';

if (! isset($_POST['Submit'])) {
    $_POST['Submit'] = 'Login';
};

if (isset($_SESSION['SESS_MEMBER_ID'])) {
    $userId = $_SESSION['SESS_MEMBER_ID'];

}else $userId = 1;	
try{
$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();

$my_user = new User((int)$userId);
$firstName=$my_user->getFirstName();
$lastName=$my_user->getLastName();
$privilege=$my_user->getType();		
$title='Please Enter title subject';			
$message='Please Enter content here';				
$loged="You are logged in as " . $firstName . " " . $lastName;
$businessesArr = Business::getTypes();					
$locationsArr = Locations::getTypes();						
$manageName = 'General Messages';

if($privilege!='Administrator'){
		session_write_close();
		header("location: ../login.php");
		exit();
}
	
}
catch(PDOException $e)
{
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
            $newbusinessesArr=array();
            $newLocationselectArr=array();

            if ( $_POST['Submit'] == 'Save Message' ) {                
                $_POST['Submit'] = 'Login';
               
                foreach ($businessesArr as $key => $value) {
                    if(isset($_POST['businessselect'.$key])) {                       
                        if($_POST['businessselect'.$key] == 'on') {                            
                            $newbusinessesArr[$key] = $value;
                        }
                    }
                }
                foreach ($locationsArr as $key => $value) {
                    if(isset($_POST['locationselect'.$key])) {                        
                        if($_POST['locationselect'.$key] == 'on') {                            
                            $newLocationselectArr[$key] = $value;
                        }
                    }
                }
                               
                $business=$newbusinessesArr;

                $title=$_POST['title'];
                $message=$_POST['message'];                
                $newMessageArr['title'] = $title;
                $newMessageArr['message'] = $message;
                


                $my_message = new Message($newMessageArr);
                $my_message->updateBusiness($newbusinessesArr);
                $my_message->updateLocation($newLocationselectArr);
                $my_message->commit();
               
				$url = "generalmessages.php";
				header("Location: $url");

            }

        }

        ?>



        <?php
        include 'header.php';
        ?>
		<div id="middle" style="height:auto; min-height:600px">
        <form id="Form1" name=form1 action="createmsg.php" method="post"  style=" margin-left: auto; margin-right: auto">

            

                <table  style=" margin-left: auto; margin-right: auto" width="1000" cellpadding="10" CELLSPACING="0"border="0">
                    <tr>
                        <td align="left" width="300">
                            <input id="home" type=button onClick="location.href='main.php'" value='Home'/>
                                <input id="bigbutton" type=button onClick="location.href='generalmessages.php'" value='General Messages'/>

                                    </td>
                                    <td align="left">
                                        <b><? echo "$manageName"?></b>
                                    </td>
                                    <td  >&nbsp;</td>
                                    </tr>


                                    </table>




                                    <table  border="0" cellpadding="10"  style=" margin-left: auto; margin-right: auto" width="1000" CELLSPACING="0" >

                                        <tr>
                                            <td align="left" width="1000"><h1>Create General Message</h1></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <textarea name="title" rows=1 cols=80 wrap="soft" onfocus="titleblank()"><?php echo $title ?></textarea>
                                                <script type="text/javascript">
                                                    function titleblank()
                                                    {
                                                        document.form1.title.value ="";
                                                    }
                                                </script>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <textarea name="message" rows=5 cols=80 wrap="soft" onfocus="messageblank()"><?php echo $message ?></textarea>
                                                <script type="text/javascript">
                                                    function messageblank()
                                                    {
                                                        document.form1.message.value ="";
                                                    }
                                                </script>
                                            </td>
                                        </tr>
                                    </table>
                                    <table  border="0" cellpadding="8"  style=" margin-left: auto; margin-right: auto" width="1000" CELLSPACING="0" >
                                        <tr>
                                            <td><h1>Businesses</h1></td>
                                            <td><h1>Locations</h1></td>
                                        </tr>
                                        <tr>


                                            <?php

                                            try {
                                                $index=array_shift(array_keys($businessesArr));

                                                echo '<td><div style=" height:200px; overflow:auto; width: 80%; border-width:2px; border-color: green;">';

                                                foreach ($businessesArr as $key => $value) {
                                                    echo '<input type="checkbox" name="businessselect'.$key.'"';
                                                    echo '>';
                                                    echo $value;
                                                    echo '<br>';
                                                }


                                                echo '</div>
					</td>';

                                            }
                                            catch(PDOException $e) {
                                                echo 'No Results';
                                            }

                                            try {
                                                $index=array_shift(array_keys($businessesArr));

                                                echo '<td><div style="height:200px; overflow:auto; width: 80%; border-width:2px; border-color: green;">';

                                                foreach ($locationsArr as $key => $value) {
                                                    echo '<input type="checkbox" name="locationselect'.$key.'"';
                                                    echo '>';
                                                    echo $value;
                                                    echo '<br>';
                                                }


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



                                    <table border="0" cellpadding="8"  style=" margin-left: auto; margin-right: auto" width="1000" CELLSPACING="0" >
                                        <tr>
                                            <th align="right">
                                                <input id="submit" type="Submit"  name="submit" value="Save Message" />
                                                    <input type="hidden" name="Submit" value="Save Message" />
                                                        </th>
                                                        </tr>
                                                        </table>
</form>
                                    </div>
                                                        
<?php
include 'footer.php';
                                                        ?>


                                                        </body>
                                                        </html>
