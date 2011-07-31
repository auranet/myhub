<?php
require_once '../auth.php';
require_once 'includes/User.php';
require_once 'includes/Business.php';
require_once 'includes/Locations.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Meditronic LTD</title>
        <meta http-equiv="Content-Language" content="English" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
	

	
<script type="text/javascript">

</script>		
    </head>
<?php
$fieldlength=10;

function cancel_changes() {
    unset($_FILES);
}

function cutValue($fieldValue,$fieldlength ) {
    $value = substr($fieldValue, 0, $fieldlength);
    if($fieldlength < strlen($fieldValue))
        $value.='...';
    return $value;
}

if (isset($_SESSION['SESS_MEMBER_ID'])) {
    $userId = $_SESSION['SESS_MEMBER_ID'];
}else $userId = 1;

if (! isset($_REQUEST['Submit'])) {
    $_REQUEST['Submit'] = 'Login';
};


$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();

$numrows=User::getActiveUsers();				
$offset=6;
$pages = ceil($numrows/$offset);

$searchwhere="";
$searchstr="";

//echo'REQUEST[Search] = '.$_REQUEST['Search'];
//echo'REQUEST[Submit] = '.$_REQUEST['Submit'];
//echo'_POST[ImportFromCsv] = '.$_POST['ImportFromCsv'];

if(isset($_REQUEST['Submit']) & isset($_REQUEST['SearchStr'])) {
    if ( $_REQUEST['SearchStr'] != "") {
		$searchClean=clean($_REQUEST['SearchStr']);
        $searchwhere = " and  userName LIKE '%" . $searchClean . "%'
					or  firstName LIKE '%" . $searchClean . "%'
					or  lastName LIKE '%" . $searchClean . "%'";

        $querysearch = 'SELECT * FROM users WHERE active=1 ' . $searchwhere ;       
        $resultsearch=User::getManagerUsers($querysearch);        
        $pages = ceil(count($resultsearch)/$offset);
        unset($_REQUEST['Search']);
        $_REQUEST['Submit']='Login';
        $searchstr=$searchClean;

    }
}


$my_user = new User((int)$userId);
$firstName=$my_user->getFirstName();
$lastName=$my_user->getLastName();				
$loged="You are logged in as " . $firstName . " " . $lastName;

$businessesArr = Business::getTypes();
$privilegesArr = Usertype::getTypes();		
$geographysArr = Locations::getTypes();

if($my_user->getType()!='Administrator'){
		session_write_close();
		header("location: ../login.php");
		exit();
}

?>
    <body>
        <?php
        include 'header.php';
        ?>
		<div id="middle" style="height:auto; min-height:600px">
        <form id="Form1" action="manager.php" method="post"  enctype="multipart/form-data">

                <table align="center" width="1000" CELLSPACING="0" CELLPADDING="10"  border="0">
                    <tr>
                        <td style=" width: 250px;">&nbsp;
                            <input id="home" type=button onClick="location.href='main.php'" value='Home'/>
                            <input id="home" type=button onClick="location.href='createuser.php'" value='New User'/>
                        </td>
                        <td  style=" width: 350px;">&nbsp;							                         
                        </td>
                        <td align="right" >&nbsp;</td>
                        <td  align="left" >&nbsp;
                        </td>
                    </tr>

                </table>
				
				<div id="import">
				 &nbsp;&nbsp;
		         <input id="middlebutton" type=button onClick="location.href='exportUsersCsv.php'" value='Export to .csv'/>				 
				 <!--<input id="middlebutton" type=button onClick="location.href='importUsersCsv.php'" value='Import from .csv'/>	-->
				 <input id="middlebutton" type="button" name="ImportFromCsv" value='Import from .csv' onclick="checkFile(this.form);"/>
				 
				<script language='javascript'>                          						
						if(navigator.appName=="Microsoft Internet Explorer"){
							document.write('<input class="file" type="file" name="importfile" id="file" />');
						}
						else{document.write('<input class="fileff" type="file" name="importfile" id="file" />');}
												
						function checkFile(yourForm){
						    var fileVal = yourForm.elements['importfile'].value;
						    //RegEx for valid file name and extensions.
						    var pathExpression = "[?:[a-zA-Z0-9-_\.]+(?:.cvs)";
						    if(fileVal != ""){
						        if(!fileVal.toString().match(pathExpression) && confirm("Are you sure import data to DB?")){
									
									<?php importCsvUpload()?>
						            yourForm.submit();
						        } else {
						            return;
						        }
						    } else {									
						            return;
						    }						    
						}
					 
                 </script>
				<?php
	
					function importCsvUpload() {				
						$errmessage='';
						
									
						if(isset($_FILES['importfile']) && $_FILES["importfile"]["name"]!=''){
						
							list($filename, $extension)=split('\.',$_FILES['importfile']['name'],2);
						    if ($extension == 'csv') {														        			
								if ($_FILES['importfile']["error"] > 0)  {
						            $errmessage.= "Return Code: " . $_FILES['importfile']["error"] . "<br />";
						        }else {  
							            move_uploaded_file($_FILES['importfile']['tmp_name'],IMPORTCSV);
										$_SESSION['ERRMESSAGE']=$errmessage;
										header("Location: importUsersCsv.php");		
						        }															
						    }
						    else {
						       $errmessage.= "File extension is wrong";
							   $_SESSION['ERRMESSAGE']=$errmessage;
						    }						
						}else{										
							unset($_FILES['importfile']);
							}													
					}					
				?>
					

				 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<Search>Search:</Search>
				 <input type="text" name="SearchStr" size="20" maxlength="255" value=<? echo "$searchstr"?> ></input>
                 <input id="smallbutton" type="Submit" name="Submit" value="Search"></input>
<?php		

						if(isset($_SESSION['IMPORTCSV'])){
							echo '<div align="center"><h2 style="padding-top:5px">';		
							echo $_SESSION['IMPORTCSV'];
							echo '</h2></div>';
							unset($_SESSION['IMPORTCSV']);
							unset($_SESSION['ERROR']);
							}
						if(isset($_SESSION['ERRMESSAGE'])){
							echo '<div align="center"><h2 style="padding-top:5px">';		
							echo $_SESSION['ERRMESSAGE'];
							echo '</h2></div>';
							unset($_SESSION['ERRMESSAGE']);							
						}
						
?>
	
				</div>
				
				
				
                <div align="center"><h1 style="padding-top:10px">Manage Users</h1></div>



                <table align="center"  border="0" CELLSPACING="0" CELLPADDING="2" width="1000">
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                    </tr>
                    <tr >
                        <td align="center">
                            <b><a href="?orderBy=id">#</a></b>
                        </td>
                        <td>
                            <b><a href="?orderBy=lastName">Last and First name</a></b>
                        </td>
                        <td>
                            <b><a href="?orderBy=userName">Email</a></b>
                        </td>
                        <td>
                            <b><a href="?orderBy=usertype_id">Privileges</a></b>
                        </td>
                        <td>
                            <b><a >Last Login</a></b>
                        </td>
                        <td>
                            <b><a >Login Counter</a></b>
                        </td>
                        <td>
                            <b><a href="?orderBy=locations_id">Location</a></b>
                        </td>
                        <td>
                            <b><a>Business</a></b>
                        </td>
                        <td>
                            <b><a>Edit</a></b>
                        </td>
                        <td>
                            <b><a>Delete</a></b>
                        </td>
                    </tr>
                    <?php
                    try {
                        /*** query the database ***/

                        if (!isset($_REQUEST['startrow']) or !is_numeric($_REQUEST['startrow'])) {
                            //we give the value of the starting row to 0 because nothing was found in URL
                            $startrow = 0;
                            $_REQUEST['startrow']='0';
                            //otherwise we take the value from the URL
                        } else {
                            $startrow = (int)$_REQUEST['startrow'];
                        }
                        $orderBy = array('id','lastName','userName', 'usertype_id', 'lastLogin',  'business', 'locations_id');


                        if(!isset($_SESSION['orderBy'])) {
                            $_SESSION['orderBy']= 'id';
                            $_SESSION['DESCCOUNT']= 1;
                            $_SESSION['DESC']='';

                        }elseif (isset($_REQUEST['orderBy']) && in_array($_REQUEST['orderBy'], $orderBy)) {
                            $_SESSION['orderBy']= $_REQUEST['orderBy'];
                            $_SESSION['DESCCOUNT']+= 1;
                            if($_SESSION['DESCCOUNT'] % 2)
                                $_SESSION['DESC']='DESC';
                            else
                                $_SESSION['DESC']='';
                            $_REQUEST['orderBy']='';
                        }


                        if (isset($_REQUEST['delete'])) {
                            $delete = $_REQUEST['delete'];
                            //echo'delete = '.$delete;
                            $userDelete=new User((int)$delete);
                            $userDelete->delete();
                            $userDelete->commit();
                            $numrows=User::getActiveUsers();
                            $pages = ceil($numrows/$offset);
                            $startrow = 0;
                        }else {
                            $delete = 0;
                        }

                        if (isset($_REQUEST['Exporttocsv'])) {
                            $exporttocsv = $_REQUEST['Exporttocsv'];
                            
                            $query="SELECT * FROM users WHERE active=1";
                            $url = "csv.php?export=".$query;
                            header("Location: $url");
                            //CSVExport("SELECT * FROM users WHERE active=1");
                        }
                        if (isset($_REQUEST['Newusersfromcsv'])) {
                            $newusersfromcsv = $_REQUEST['Newusersfromcsv'];
                            
                            CSVExport("SELECT * FROM users WHERE active=1");
                        }

                        $query = 'SELECT * FROM users WHERE active=1 ' . $searchwhere .
                                ' ORDER BY ' . $_SESSION['orderBy'] . ' '. $_SESSION['DESC'] . ' LIMIT ' . $startrow . ' , ' . $offset ;

                        //echo'query = '.$query;
                        $result=User::getManagerUsers($query)or die(mysql_error());
                        //print_r( $result);
                        $num=count($result);
                        //print_r( $result);
                        if(count($result)) {
                            foreach ($result as $key => $value) {
                                $userId=$value['id'];
                                $my_user=new User((int)$userId);
                                $firstName=$my_user->getFirstName();
                                $lastName=$my_user->getLastName();
                                $email=$my_user->getUserName();
                                $privileges=$my_user->getType();
                                //$lastLogin=$my_user->getLastLogin();
                                $geography=$my_user->getLocation();
								

                                $business=$my_user->getBusiness();


                                //Check last login
                                $queryCount = "SELECT count(*) as LoginCount FROM logging WHERE users_username='".$email."' AND type='login'";
                                $resultCheck = mysql_query($queryCount);
                                $row = mysql_fetch_array($resultCheck);
                                $LoginCount = $row['LoginCount'];
                                $count = 0;
                                if ( $LoginCount >= 2 ) {
                                    $count = $LoginCount - 1;
                                    $queryDate = "SELECT date FROM logging WHERE users_username='".$email."' AND type='login' LIMIT ".$count.",1";

                                } else {
                                    $queryDate = "SELECT date FROM logging WHERE users_username='".$email."' AND type='login'";
                                }
                                $resultDate = mysql_query($queryDate);
                                $row1 = mysql_fetch_array($resultDate);
                                $lastLogin = $row1['date'];




                                echo '<tr>';
                                /*** create the colomn values ***/
                                echo '<td align="center"><h3>' . $userId . '&nbsp;</h3></td>';
                                echo '<td ><h3><p class="twrap">' . $lastName .' '.$firstName.'&nbsp;</p></h3></td>';
                                echo '<td><h3>' . $email . '&nbsp;</h3></td>';
                                echo '<td><h3>' . $privileges . ' &nbsp;</h3></td>';
                                echo '<td><h3>' . $lastLogin . ' &nbsp;</h3></td>';
                                echo '<td><h3>' . $LoginCount . ' &nbsp;</h3></td>';
                                echo '<td><h3>' . $geographysArr[$geography] . '&nbsp;</h3></td>';
                                echo '<td ><h3>';
                                $lastindex=array_pop(array_keys($business));
                                foreach ($business as $key => $value) {
                                    echo $value;
                                    if($lastindex!=$key){
                                        echo',';
										echo '<br>';
									}
                                }
                                echo '&nbsp;</h3></td>';
                                echo '<td><h3>';
                                echo '<a href="edituser.php?userId='.$userId.'">Edit</a>';
								
                                echo '</h3></td>';
                                echo '<td><h3>';
                                echo '<a href="?delete='.$userId.'">Delete</a>';
                                echo '</h3></td>';
                                echo '</tr>';
                            }
                        }

                    }
                    catch(PDOException $e) {
                        echo 'No Results';
                    }

                    // This function tests whether the email address is valid
                    function isValidEmail($mail) {
                        $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
                        echo 'isValidEmail($mail) = ' . $mail;
                        if (eregi($pattern, $mail)) {

                            return true;
                        }
                        else {

                            return false;
                        }
                    }
                    ?>


                </table>
            </div>
        </form>


		
        <div align="center"><h2 style="padding-top:10px">Pages</h2></div>
        <table align="center" >
<?	
$startrow = 0;
            $page=1;
            $prev='Prev';
            $next='Next';
            //echo '<tr align="center"><th><a> Pages </a></th> </tr>';
            //echo 'pages=' . $pages;

            echo '<tr>';
            if(isset($_REQUEST['startrow']))
                if((int)$_REQUEST['startrow']>0) {
                    $startrowprev=(int)$_REQUEST['startrow'] - $offset;
                    echo '<th ><a href="'.$_SERVER['PHP_SELF'].'?startrow=' . $startrowprev . '">' . $prev . '</a>&nbsp;</th>';
                }


            while (ceil($startrow/$offset) < $pages) {

                if($pages-$page > $offset)
                    echo '<th >' .
                            '<a href="'.$_SERVER['PHP_SELF'].'?startrow='. $startrow .'">'.($page).'</a>' .
                            '</th>';
                else
                    echo '<th >' .
                            '<a href="'.$_SERVER['PHP_SELF'].'?startrow='. $startrow .'">'.($page).'</a>' .
                            '</th>';
                $startrow += $offset;
                $page ++;
            }
            if(isset($_REQUEST['startrow']))
                if($pages > ceil(((int)$_REQUEST['startrow']+$offset)/$offset)) {
                    $startrownext=(int)$_REQUEST['startrow'] + $offset;
                    echo '<th >&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?startrow=' . $startrownext . '">'.$next.'</a></th>';
                }
            echo '</tr>';
?>

        </table>


<?php
include 'footer.php';
        ?>

    </body>
</html>