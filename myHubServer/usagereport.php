<?php
		require_once '../auth.php';
		require_once 'includes/User.php';		
		require_once 'includes/Link.php';		

		if (isset($_SESSION['SESS_MEMBER_ID'])) {
			$userId = $_SESSION['SESS_MEMBER_ID'];			
		}else $userId = 1;
			
		if (! isset($_REQUEST['Submit'])) {
	        $_REQUEST['Submit'] = 'Login';
	    };

        try{
			$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	        $db->connect();
			$links=Link::getAllLinks();				
			$numrows=count($links);						
			$offset=15;
			$pages = ceil($numrows/$offset);		
			$my_user = new User((int)$userId);
			$firstName=$my_user->getFirstName();
			$lastName=$my_user->getLastName();				
			$loged="You are logged in as " . $firstName . " " . $lastName;
			
			if($my_user->getType()!='Administrator'){
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
<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
</head>
<body>
<?php
        include 'header.php';
?>

<div id="middle">
<form id="Form1" action="usagereport.php" method="request" >


	
<table style=" margin-left: auto; margin-right: auto" width="1000px" cellpadding="10" CELLSPACING="0"  border="0">
	<tr>
		<td style=" width: 200px">
			<input id="middlebutton" type=button onClick="location.href='main.php'" value='Home'/>
			<input id="middlebutton" class=button type=button  onClick="location.href='surveyreport.php'" value='Survey Reports'>				
									
		</td>			
	
	</tr>


</table>	
	
<div align="center"><h1 style="padding-top:10px">Usage Links</h1></div>
	
	
	
	<table style=" margin-left: auto; margin-right: auto"  border="0" CELLSPACING="0" CELLPADDING="6" width="1000px">
	<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	
	</tr>
    <tr>
   
       <td >
            <b><a href="?orderByUr=name">Name</a></b>
        </td>
       <td>
            <b><a >&nbsp;&nbsp;Email</a></b>
        </td>						
        <td>
            <b><a >&nbsp;&nbsp;Last Click Time</a></b>
        </td>
		<td>
            <b><a >&nbsp;&nbsp;Total Clicks</a></b>
        </td>		

		<td>
            <b><a>&nbsp;&nbsp;Clicks in Last 30 days</a></b>
        </td>
    </tr>		
<?php
    try
    {
        /*** query the database ***/
						
		if (!isset($_REQUEST['startrow']) or !is_numeric($_REQUEST['startrow'])) {
			//we give the value of the starting row to 0 because nothing was found in URL
			$startrow = 0;
			$_REQUEST['startrow']='0';
		//otherwise we take the value from the URL
		} else {
			$startrow = (int)$_REQUEST['startrow'];
		}		
		$orderByUr = array('name');
			
		

		
		if(!isset($_SESSION['orderByUr'])){
				$_SESSION['orderByUr']= 'id';	
				$_SESSION['DESCCOUNTUR']= 1;
				$_SESSION['DESCUR']='';
				
		}elseif (isset($_REQUEST['orderByUr']) && in_array($_REQUEST['orderByUr'], $orderByUr)) {						
				$_SESSION['orderByUr']= $_REQUEST['orderByUr'];
				$_SESSION['DESCCOUNTUR']+= 1;
				if($_SESSION['DESCCOUNTUR'] % 2)
					$_SESSION['DESCUR']='DESC';
				else
					$_SESSION['DESCUR']='';
				$_REQUEST['orderByUr']='';	
		}	
		
		

		
		
		$query = 'SELECT * FROM links ORDER BY ' . $_SESSION['orderByUr'] . ' '. $_SESSION['DESCUR'] . ' LIMIT ' . $startrow . ' , ' . $offset ;			
		$result=Link::getManagerLinks($query)or die(mysql_error());		
		$num=count($result); 
		
			
		foreach ($result as $key => $value){
				
				$link=new Link((int)$value['id']);
				$usage_array = $link->getUsageInfo();
				$name = $usage_array['name'];
				$email = $usage_array['email'];
				$last_click = $usage_array['last_click'];
				$total_clicks = $usage_array['total_clicks'];
				$last_month_clicks = $usage_array['last_month_clicks'];
				
				echo '<tr>';    								
				/*** create the colomn values ***/ 
				
				echo '<td><h3>' .  $name .'&nbsp;</h3></td>';
				echo '<td><h3>&nbsp;&nbsp;' .  $email . '&nbsp;</h3></td>';
				echo '<td><h3>&nbsp;&nbsp;' .  $last_click . ' &nbsp;</h3></td>';
				echo '<td><h3>&nbsp;&nbsp;' .  $total_clicks . ' &nbsp;</h3></td>';
				echo '<td><h3>&nbsp;&nbsp;' .  $last_month_clicks . ' &nbsp;</h3></td>';
				echo '</tr>'; 					
			}

    }
    catch(PDOException $e)
    {
        echo 'No Results';
    }
	
?>
	
	
</table>

</form>
</div>
<div align="center"><h2 style="padding-top:10px">Pages</h2></div>
<table style=" margin-left: auto; margin-right: auto"  >
<?	
		$startrow = 0;
		$page=1;
		$prev='Prev';
		$next='Next';

		echo '<tr>';
		if(isset($_REQUEST['startrow']))
			if((int)$_REQUEST['startrow']>0){
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
			if($pages > ceil(((int)$_REQUEST['startrow']+$offset)/$offset)){
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
