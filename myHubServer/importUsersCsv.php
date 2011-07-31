<?php
require_once '../auth.php';
require_once 'includes/User.php';
require_once 'includes/Business.php';
require_once 'includes/Locations.php';
 
$databasehost = "localhost";
$databasename = "test";
$databasetable = "sample";
$databaseusername ="test";
$databasepassword = "";
$fieldseparator = ",";
$lineseparator = "\n";
$csvfile = IMPORTCSV;
$numberfields = 8;
$newusers=0;
$updatedusers=0;
$procced=0;
$errmessage='';

$save = 1;
$today = getdate();
$outputfile = 'csv/dbImport_'.$today['mday'].'-'.$today['mon'].'-'.$today['year'].'.txt';	
@unlink($outputfile);

function close(){
unset($_SESSION['ERROR']);
@unlink(IMPORTCSV);
//removeFile();
$url = "manager.php";
header("Location: $url");
exit;
}

function removeFile(){
$fileToRemove = IMPORTCSV;
$errmessage='';
if (file_exists($fileToRemove)) {
   $errmessage.= 'yes the file does exist';

	   if (@unlink($fileToRemove) == true) {
		  $errmessage.= 'the file successfully removed';
	   } else {
			  $errmessage.= 'something is wrong, we may not have enough permission to delete this file';			   
			}
	} else {
			$errmessage.= 'the file is not found, do something about it???';
		}
			
	if(isset($_SESSION['ERROR']))
		$_SESSION['ERROR'].=$errmessage;
	else
		$_SESSION['ERROR']=$errmessage;	
}

function validValue($value, $array, $errmessage){
	
	if(!array_search($value, $array)){
		$errmessage= $value." not found. Make sure you specified the correct value.\n";
		$result=count($array);					
	}
	else
		$result=array_search($value, $array);
		
	
	if(isset($_SESSION['ERROR']))
		$_SESSION['ERROR'].=$errmessage;
	else
		$_SESSION['ERROR']=$errmessage;	
	return $result;
}

function validBusinessValue($value, $array, $errmessage){
	
	//print_r($array);
	
	if(!array_search($value, $array)){
		$errmessage = $value." not found. Make sure you specified the correct value.\n";
		$result=count($array);					
	}
	else
		$result=array_search($value, $array);
		
	if(isset($_SESSION['ERROR']))
		$_SESSION['ERROR'].=$errmessage;
	else
		$_SESSION['ERROR']=$errmessage;	
	return $result;
}

if(!file_exists(IMPORTCSV)) {
	$errmessage.= "File not found. Make sure you specified the correct path.\n";
	if(isset($_SESSION['ERROR']))
		$_SESSION['ERROR'].=$errmessage;
	else
		$_SESSION['ERROR']=$errmessage;	
	close();
}



$file = fopen(IMPORTCSV,"r");

if(!$file) {
	$errmessage.= "Error opening data file.\n";
	if(isset($_SESSION['ERROR']))
		$_SESSION['ERROR'].=$errmessage;
	else
		$_SESSION['ERROR']=$errmessage;	
	close();
}

$size = filesize(IMPORTCSV);

if(!$size) {
	$errmessage.= "File is empty.\n";
	if(isset($_SESSION['ERROR']))
		$_SESSION['ERROR'].=$errmessage;
	else
		$_SESSION['ERROR']=$errmessage;	
	close();
}

$csvcontent = fread($file,$size);

fclose($file);



$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$con = $db->connect();
 

$lines = 0;
$queries = "";
$linearray = array();
$businessesArr = Business::getTypes();
$locationsArr = Locations::getTypes();
$privilegesArr = Usertype::getTypes();

foreach(split($lineseparator,$csvcontent) as $line) {

	$lines++;
	if($lines>1){
	
		$line = trim($line," \t");
		$line = str_replace("\r","",$line);
		
		/************************************
		This line escapes the special character. remove it if entries are already escaped in the csv file
		************************************/
		$line = str_replace("'","\'",$line);
		
		/*************************************/
		
		$linearray = explode($fieldseparator,$line);		
		if(count($linearray) == $numberfields){	
			//$userId=(int)$linearray[0];					
			$username=trim($linearray[0],"\x22\x27");			
			$password=trim($linearray[1],"\x22\x27");
			$firstName=trim($linearray[2],"\x22\x27");			
			$lastName=trim($linearray[3],"\x22\x27");
			$location=trim($linearray[4],"\x22\x27");
			$privilege=trim($linearray[5],"\x22\x27");
			$active=trim($linearray[0],"\x22\x27");
			$businessline=trim($linearray[7],"\x22\x27");
			$business=split(';',$businessline);			
			$newbusinessesArr=array();
			//echo' busines = ';
			//print_r($business);
			foreach ($business  as $key => $value) {			
				if ($value!='')					
					$newbusinessesArr[validBusinessValue($value, $businessesArr, $errmessage)]=$value;
			}
				
			//if($userId!=0){ //Update user $username!=='' && 	
			if($username!==''){
				if(User::isUserExists($username)) {
					$userId=User::getUserIdByUserName($username);

					if($userId!='')	{
						$my_user = new User((int)$userId);						
				        $my_user->setType(validValue($privilege, $privilegesArr, $errmessage));
				        $my_user->setFirstName($firstName);
				        $my_user->setLastName($lastName);
				        if(strlen($password) < 40)
				          $my_user->setPassword(sha1($password));
						$my_user->setLocation(validValue($location, $locationsArr, $errmessage));
				        $my_user->updateBusiness($newbusinessesArr);					
						$my_user->commit();  
						$updatedusers+=1;	
					}else
						$errmessage.='update failed username '.$username.' exists in db'."\n";	
						
					
				}
				else{ //Create new user
						
		                $newUserArr['id'] = '';
		                $newUserArr['firstname'] = $firstName;
		                $newUserArr['lastname'] = $lastName;
		                $newUserArr['lastlogin'] = '';
		                $newUserArr['username'] = $username;
		                $newUserArr['usertype_id'] = validValue($privilege, $privilegesArr, $errmessage);
		                $newUserArr['locations_id'] = validValue($location, $locationsArr, $errmessage);
						if(strlen($password) < 40)
							$newUserArr['password'] = sha1($password);
						else
							$newUserArr['password'] = $password;
							
		                $newUserArr['active'] = '';
		                				
		                if(!User::isUserExists($username)) {
							//echo'Create newUserArr = ';
							//print_r($newUserArr);
		                    $my_user = new User($newUserArr);                    
		                    $my_user->updateBusiness($newbusinessesArr);
		                    $my_user->commit();
							$newusers+=1;	                    
		                }
						else 
							$errmessage.='create failed username '.$username.' exists in db'."\n";					
					}
			}
			else
				$errmessage.='create failed username '.$username.' is empty'."\n";
				
				$procced+=1;
				if(isset($_SESSION['ERROR']))
					$_SESSION['ERROR'].=$errmessage;
				else
					$_SESSION['ERROR']=$errmessage;	
						}
	}
}

@mysql_close($con);

$_SESSION['IMPORTCSV']='Found a total of '.$procced.' records.'."\n".
					   'For details open '.$outputfile.' file.';



if($save) {		
		$file2 = fopen($outputfile,"w");		
		if(!$file2) {
			$_SESSION['ERROR'].= "Error writing to the output file.\n";
		}
		else {
			fwrite($file2,"\n".$_SESSION['ERROR']);			
		}
		fwrite($file2,"Found a total of ".$procced." records.\n");
		fclose($file2);			
}

close();
?>
