<?php
		require_once '../auth.php';
		require_once 'includes/User.php';
		require_once 'includes/Business.php';
		require_once 'includes/Locations.php';
		require_once 'includes/GeneralMessage.php';
		require_once 'includes/GeneralMessages.php';		
		
		if (isset($_SESSION['SESS_MEMBER_ID'])) {
			(int)$userId = $_SESSION['SESS_MEMBER_ID'];
			//echo "userId = ".$userId;
		}else $userId = 1;//TODO only for debugging
		
		
		if (! isset($_REQUEST['Submit'])) {
	        $_REQUEST['Submit'] = 'Login';
	    };



		
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $db->connect();
		$messagesAR=GeneralMessages::getAllMessages();
		 //print_r($messagesAR);
		$numrows=GeneralMessage::getCountMessages();
		//echo ' numrows = '.$numrows;
		$offset=6;
		$pages = ceil($numrows/$offset);
		//echo ' pages = '.$pages;
					
		$my_user = new User((int)$userId);			
		$firstName=$my_user->getFirstName();
		$lastName=$my_user->getLastName();			
		$loged="You are logged in as " . $firstName . " " . $lastName;
		
		if($my_user->getType()!='Administrator'){
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
<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
</head>
<body>
<?php
        include 'header.php';
?>

<form id="Form1" action="generalmessages.php" method="request">

<div id="middle" style="height:auto; min-height:600px">
<table align="center" width="1000" cellpadding="10" CELLSPACING="0"   border="0">
	<tr>
		<td width="250">			
			<input id="home" type=button onClick="location.href='main.php'" value='Home'/>
			<input id="middlebutton" type=button onClick="location.href='createmsg.php'" value='New Message'/>
									
		</td>			
		<td  width="400">			
			<!--<input id="bigbutton" type=button onClick="location.href='../underconst.php'" value='Messages from .csv'/>
			<input id="middlebutton" type=button onClick="location.href='../underconst.php'" value='Export to .csv'/>-->
		</td>	
		<td  >&nbsp;</td>		
	</tr>


</table>	
	<div align="center"><h1 style="padding-top:10px">General Messages</h1></div>	
	
	
	
<table align="center" border="0" cellpadding="6" width="1000" CELLSPACING="0" >
	<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
	</tr>
    <tr>
        <td>
            <b><a href="?orderByGm=id">#</a></b>			
        </td>
       <td>
            <b><a href="?orderByGm=title">Title</a></b>			
        </td>
       <td>
            <b><a href="?orderByGm=message">Message</a></b>			
        </td>	
       <td>
            <b><a>Location</a></b>			
        </td>
       <td>
            <b><a>Business</a></b>			
        </td>		
		<td>
			<b><a>Delete</a></b>
		</td>
    </tr>
		
<?php
    try
    {
        /*** query the database ***/
				
				
		if (!isset($_GET['startrow']) or !is_numeric($_GET['startrow'])) {
			//we give the value of the starting row to 0 because nothing was found in URL
			$startrow = 0;
			$_GET['startrow']='0';
		//otherwise we take the value from the URL
		} else {
			$startrow = (int)$_GET['startrow'];
		}		
	
		$orderByGm = array('id','title','message');
		
		
		
		if(!isset($_SESSION['orderByGm'])){
				$_SESSION['orderByGm']= 'id';	
				$_SESSION['DESCCOUNT']= 1;
				$_SESSION['DESC']='';
				
		}elseif (isset($_REQUEST['orderByGm']) && in_array($_REQUEST['orderByGm'], $orderByGm)) {						
				$_SESSION['orderByGm']= $_REQUEST['orderByGm'];
				$_SESSION['DESCCOUNT']+= 1;
				if($_SESSION['DESCCOUNT'] % 2)
					$_SESSION['DESC']='DESC';
				else
					$_SESSION['DESC']='';
				$_REQUEST['orderByGm']='';
		}		
		
		if(!isset($_SESSION['delete']))
                     $_SESSION['delete'] = 0;
            

                if(isset($_REQUEST['delete'])){
                    if( $_SESSION['delete'] != $_REQUEST['delete']){


                            if($_REQUEST['delete'] != ''){
                                    $delete = $_REQUEST['delete'];
                                    //if(GeneralMessage::isMessagesExisted($delete)){
                                    $messageDelete=new GeneralMessage((int)$delete);
                                    $messageDelete->delete();
                                    $messageDelete->commit();
                                    $numrows=GeneralMessage::getCountMessages();
                                    $pages = ceil($numrows/$offset);
                                    $_SESSION['delete']=(int)$delete;
                                    //}


                                    $startrow = 0;
                            }

                    
                    }
                }
		
		
		$query = 'SELECT * FROM genmessage ORDER BY ' . $_SESSION['orderByGm'] . ' '. $_SESSION['DESC'] . ' LIMIT ' . $startrow . ' , ' . $offset ;	
										  	
		$result=GeneralMessage::getManagerMessages($query)or die(mysql_error());
		$num=count($result); 	

		foreach ($result as $key => $value){
				//echo'$key = '.$key;
				//echo'$value[id] = '.$value['id'];
				$messageId=$value['id'];
				//echo'userId = '.$userId;						
				$my_message=new GeneralMessage((int)$messageId);										
				$messageId=$my_message->getId();				
				$title=$my_message->getTitle();
				$message=$my_message->getMessage();
				$location=$my_message->getLocation();
				$business=$my_message->getBusiness();
				//print_r($business);
				echo '<tr>';    								
				/*** create the colomn values ***/ 
				echo '<td align="left"><h3>' . $messageId . '&nbsp;</h3></td>'; 
				echo '<td ><h3><p class="twrap">' . $title .'&nbsp;</p></h3></td>';
				echo '<td><h3><p class="mwrap">' . $message . '&nbsp;</p></h3></td>';
				echo '<td ><h3>'; 
						$lastindex=array_pop(array_keys($location));
						foreach ($location as $key => $value){
							echo ''.$value.'';
							if($lastindex!=$key)
								echo',';
							echo '<br>';
							
						}
				echo '&nbsp;</h3></td>'; 
				echo '<td ><h3>'; 
						$lastindex=array_pop(array_keys($business));
						foreach ($business as $key => $value){
							echo ''.$value.'';
							if($lastindex!=$key){
								echo',';
								echo '<br>';
							}
							
						}
				echo '&nbsp;</h3></td>';				 
				echo '<td><h3>';
				echo '<a href="?delete='.$messageId.'">Delete</a>';
				echo '</h3></td>'; 				
				echo '</tr>'; 						
			}
						
		
    }
    catch(PDOException $e)
    {
        echo 'No Results';
    }
	
	// This function tests whether the email address is valid  
   function isValidEmail($mail){
      $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
     echo 'isValidEmail($mail) = ' . $mail; 
      if (eregi($pattern, $mail)){
		 
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
<table align="center" style=" margin-left: auto; margin-right: auto"  >
 
	<?	
		$startrow = 0;
		$page=1;
		$prev='Prev';
		$next='Next';

		echo '<tr>';
		if(isset($_GET['startrow']))
			if((int)$_GET['startrow']>0){
				$startrowprev=(int)$_GET['startrow'] - $offset;
				echo '<th><a href="'.$_SERVER['PHP_SELF'].'?startrow=' . $startrowprev . '">' . $prev . '</a></th>';	
			}
		
		
		while (ceil($startrow/$offset) < $pages) {		
					echo '<th>' . 							
					'<a href="'.$_SERVER['PHP_SELF'].'?startrow='. $startrow .'">'.($page).'</a>' .				
					'</th>';		
					$startrow += $offset;
					$page ++;
		}			
			
		if(isset($_GET['startrow']))
			if($pages > ceil((int)$_GET['startrow']+$offset/$offset)){
					$startrownext=(int)$_GET['startrow'] + $offset;
					echo '<th><a href="'.$_SERVER['PHP_SELF'].'?startrow=' . $startrownext . '">' . $next . '</a></th>';	
			}			
		echo '</tr>';
		
		
		
	?>
      
</table>

<?php
        include 'footer.php';
?>

</body>
</html>