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
			$links=Link::getActiveLinks();				
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
<form id="Form1" action="surveyreport.php" method="request" >


	
<table style=" margin-left: auto; margin-right: auto" width="1000px" cellpadding="10" CELLSPACING="0"  border="0">
	<tr>
		<td style=" width: 400px">
			<input id="middlebutton" type=button onClick="location.href='main.php'" value='Home'/>
			<input id="middlebutton" type=button onClick="location.href='exportSurveyCsv.php'" value='Export to .csv'/>															
		</td>			
	
	</tr>


</table>	
	
<div align="center"><h1 style="padding-top:10px">Survey Report</h1></div>
	
	
	
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
            <b><a >Survey name</a></b>
        </td>
						
        <td>
            <b><a >&nbsp;&nbsp;Last time answered</a></b>
        </td>
      <td>
            <b><a >&nbsp;&nbsp;Email last survey user</a></b>
        </td>		
		<td>
            <b><a >&nbsp;&nbsp;Total answers</a></b>
        </td>		

		<td>
            <b><a>&nbsp;&nbsp;Last 30 days click count</a></b>
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
		$orderByTimeStamp = array('name');
			
		

		
		if(!isset($_SESSION['orderByTimeStamp'])){
				$_SESSION['orderByTimeStamp']= 'timestamp';	
				$_SESSION['DESCCOUNTUR']= 1;
				$_SESSION['DESCUR']='';
				
		}elseif (isset($_REQUEST['orderByTimeStamp']) && in_array($_REQUEST['orderByTimeStamp'], $orderByTimeStamp)) {						
				$_SESSION['orderByTimeStamp']= $_REQUEST['orderByTimeStamp'];
				$_SESSION['DESCCOUNTUR']+= 1;
				if($_SESSION['DESCCOUNTUR'] % 2)
					$_SESSION['DESCUR']='DESC';
				else
					$_SESSION['DESCUR']='';
				$_REQUEST['orderByTimeStamp']='';	
		}	
		
	
		
		
		//$query = 'SELECT * FROM surveys_free_report ORDER BY ' . $_SESSION['orderByTimeStamp'] . ' '. $_SESSION['DESCUR'] . ' LIMIT ' . $startrow . ' , ' . $offset ;
		$query = 'SELECT * FROM links WHERE active=true LIMIT ' . $startrow . ' , ' . $offset ;		
		$result=Link::getManagerLinks($query)or die(mysql_error());		
		$num=count($result); 		
			
		foreach ($result as $key => $value){
				$last_click='N/A';
				$email='N/A';
				
				$link=new Link((int)$value['id']);
				$survey_array = $link->getSurveyInfo();
				$name = $survey_array['name'];
				$last_user_click = $survey_array['last_user_click'];

				if($last_user_click!='NOUSER'){
					if($last_user_click!='N/A'){
						$last_click=$last_user_click[0]['timestamp'];
						$email=User::getUserNameById($last_user_click[0]['users_id']);									
					}
					$total_clicks = $survey_array['total_clicks'];
					$last_month_clicks = $survey_array['last_month_clicks'];
					
					echo '<tr>';    								
					/*** create the colomn values ***/ 		
					echo '<td><h3>' .  $name .'&nbsp;</h3></td>';
					echo '<td><h3>&nbsp;&nbsp;' .  $last_click . ' &nbsp;</h3></td>';
					echo '<td><h3>&nbsp;&nbsp;' .  $email . '&nbsp;</h3></td>';				
					echo '<td><h3>&nbsp;&nbsp;' .  $total_clicks . ' &nbsp;</h3></td>';
					echo '<td><h3>&nbsp;&nbsp;' .  $last_month_clicks . ' &nbsp;</h3></td>';
					echo '</tr>'; 					
			}
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
