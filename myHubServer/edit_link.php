<?php

require_once '../auth.php';
require_once 'includes/Link.php';
require_once 'includes/User.php';
require_once 'includes/Business.php';
require_once 'includes/Locations.php';

$link=array();

function renameFiles($tdir, $newPath, $linkname)
{
        $dirs = scandir($tdir);
        foreach($dirs as $file)
        {
			if($linkname)
			if (strpos($file, $linkname) !== false){
				rename( $tdir.$file, $newPath);
				}
        }
}

function cancel_changes($link, $newLink) {

    unset($_SESSION['MSGFRAMEARR']);
    unset($_SESSION['MSGFRAME']);
    unset($_SESSION['SURVEYFREEFRAMEARR']);
    unset($_SESSION['SURVEYFREEFRAME']);
    unset($_SESSION['SURVEYMULTIFRAMEARR']);
    unset($_SESSION['SURVEYMULTIFRAME']);	
	unset($_SESSION['new_surveyfree_ind']);
    unset($_SESSION['linkid']);
	$_SESSION['linkid']=-1;
    unset($_POST['Submit']);
    unset($_SESSION['new_msg_ind']);
	unset($_SESSION['new_surveyfree_ind']);
	unset($_SESSION['new_surveymulti_ind']);
    unset($_SESSION['BUTTONPATH']);
    unset($_SESSION['IMAGEPATH']);
    if((isset($link) && $newLink == 0 )&& count($link)!=0) {
        $msgframe=$link->getMessages();
		$surveyfreeframe=$link->getFreeSurvey();
		$surveymultiframe=$link->getMultiSurvey();		
		$newframe = array (
                "msgframe" => $msgframe,
                "surveyfreeframe"     => $surveyfreeframe,
                "surveymultiframe"  => $surveymultiframe);
		
        return $newframe;
    }
    else
        return NULL;
}

function getNewbusinesses($link) {
    $return = array();
    foreach ($link->getAllBusinesses() as $key => $value) {

        if(isset($_POST['businessselect'.$key])) {
            if($_POST['businessselect'.$key] == 'on') {
                $return[$key] = $value;
            }
        }
    }
    return $return;
}

function getNewlocations($link) {
    $return = array();
    foreach ($link->getAllLocations() as $key => $value) {
        if(isset($_POST['locationselect'.$key])) {
            if($_POST['locationselect'.$key] == 'on') {
                $return[$key] = $value;
            }
        }
    }
    return $return;
}

function getNewmsgframe($link, $msgframe) {
    $newbusinessesArr = array();
    $newLocationselectArr = array();
    $newmsgframe = array();
    $message='';

    $businessesArr = Business::getTypes();
    $locationsArr = Locations::getTypes();
    foreach ($msgframe as $key => $value) {
        $msgbusinessesArr = array();
        $msgLocationselectArr = array();
        if(isset($_POST['message'.$key])) {
            $message=$_POST['message'.$key];
            foreach ($businessesArr as $key1 => $value1) {
                if(isset($_POST['msgbusinessselect'.$key.'_'.$key1])) {
                    if($_POST['msgbusinessselect'.$key.'_'.$key1] == 'on') {
                        $msgbusinessesArr[$key1] = $value1;
                    }
                }
            }
            foreach ($locationsArr as $key2 => $value2) {
                if(isset($_POST['msglocationselect'.$key.'_'.$key2])) {
                    if($_POST['msglocationselect'.$key.'_'.$key2] == 'on') {
                        $msgLocationselectArr[$key2] = $value2;
                    }
                }
            }
        }
        else {
            $message = $value['message'];
            $msgbusinessesArr = $value['businesses'];
            $msgLocationselectArr = $value['locations'];
        }
        $newmsgframe[$key] = array (
                "messages_id" => $value['messages_id'],
                "message"     => $message,
                "businesses"  => $msgbusinessesArr,
                "locations"   => $msgLocationselectArr);
    }
    return $newmsgframe;
}

function getNewSurveyFreeframe($link, $surveyfreeframe) {
    $newbusinessesArr = array();
    $newLocationselectArr = array();
    $newframe = array();
    $question='';

    $businessesArr = Business::getTypes();
    $locationsArr = Locations::getTypes();
    foreach ($surveyfreeframe as $key => $value) {
        $sfBusinessesArr = array();
        $sfLocationselectArr = array();
        if(isset($_POST['questionf'.$key])) {
            $question=$_POST['questionf'.$key];
            foreach ($businessesArr as $key1 => $value1) {
                if(isset($_POST['qfbusinessselect'.$key.'_'.$key1])) {
                    if($_POST['qfbusinessselect'.$key.'_'.$key1] == 'on') {
                        $sfBusinessesArr[$key1] = $value1;
                    }
                }
            }
            foreach ($locationsArr as $key2 => $value2) {
                if(isset($_POST['qflocationselect'.$key.'_'.$key2])) {
                    if($_POST['qflocationselect'.$key.'_'.$key2] == 'on') {
                        $sfLocationselectArr[$key2] = $value2;
                    }
                }
            }
        }
        else {
            $question = $value['question'];
            $sfBusinessesArr = $value['businesses'];
            $sfLocationselectArr = $value['locations'];
        }
        $newframe[$key] = array (
                "surveys_id" => $value['surveys_id'],
                "question"     => $question,
                "businesses"  => $sfBusinessesArr,
                "locations"   => $sfLocationselectArr);
    }
    return $newframe;
}


function getNewSurveyMultiframe($link, $surveymultiframe) {
    $newbusinessesArr = array();
    $newLocationselectArr = array();
    $newframe = array();
    $question='';

    $businessesArr = Business::getTypes();
    $locationsArr = Locations::getTypes();

    foreach ($surveymultiframe as $key => $value) {
        $smBusinessesArr = array();
        $smLocationselectArr = array();
        if(isset($_POST['questionm'.$key])) {
            $question=$_POST['questionm'.$key];
			$answer1=$_POST['answer1_'.$key];
			$answer2=$_POST['answer2_'.$key];
			$answer3=$_POST['answer3_'.$key];
			$answer4=$_POST['answer4_'.$key];
			//$answer5=$_POST['answer5_'.$key];
            foreach ($businessesArr as $key1 => $value1) {
                if(isset($_POST['qmbusinessselect'.$key.'_'.$key1])) {
                    if($_POST['qmbusinessselect'.$key.'_'.$key1] == 'on') {
                        $smBusinessesArr[$key1] = $value1;
                    }
                }
            }
            foreach ($locationsArr as $key2 => $value2) {
                if(isset($_POST['qmlocationselect'.$key.'_'.$key2])) {
                    if($_POST['qmlocationselect'.$key.'_'.$key2] == 'on') {
                        $smLocationselectArr[$key2] = $value2;
                    }
                }
            }
        }
        else {
            $question = $value['question'];
			$answer1=$value['answers']['answer1'];
			$answer2=$value['answers']['answer2'];
			$answer3=$value['answers']['answer3'];
			$answer4=$value['answers']['answer4'];
			//$answer5=$value['answers']['answer5'];
            $smBusinessesArr = $value['businesses'];
            $smLocationselectArr = $value['locations'];
        }
        $newframe[$key] = array (
                "surveys_id" => $value['surveys_id'],
                "question"   => $question,
				"answers" 	 => array (
					"answer1" => $answer1,
					"answer2" => $answer2,
					"answer3" => $answer3,
					"answer4" => $answer4),
					//"answer5" => $answer5),	
                "businesses"  => $smBusinessesArr,
                "locations"   => $smLocationselectArr);
    }
    return $newframe;
}

function fileUpload($linkname, $link) {
    $type='';
	$linkid=$link->getId();
	$rand = rand(1, 1000000);
    if ((($_FILES['userfile']['type'] == "image/x-png")
                    || ($_FILES["userfile"]["type"] == "image/png"))
            && ($_FILES['userfile']["size"] < 300000)) {
        if ($_FILES['userfile']["error"] > 0) {
            //echo "Return Code: " . $_FILES['userfile']["error"] . "<br />";
        }
        else {

            if (file_exists(THUMBNAILPATH .$linkid.'_'.$rand.'.png')) {
                //echo $_FILES['userfile']["name"] . " already exists. ";
            }
			$imagePath=THUMBNAILPATH.$linkname.'_'.$linkid.'-'.$rand.'.png';
			renameFiles(THUMBNAILPATH, $imagePath, $linkname);
            move_uploaded_file($_FILES['userfile']["tmp_name"],$imagePath);
            //echo "Stored in: " . THUMBNAILPATH . $imagePath;
            $_SESSION['IMAGEPATH']=$imagePath;
        }
    }
    else {
        //echo "Invalid file";
    }
}

function buttonUpload($linkname, $link) {

    $type='';
    $linkid=$link->getId();
	$rand = rand(1, 1000000);
    if ((($_FILES['buttonfile']['type'] == "image/x-png")
                    || ($_FILES["buttonfile"]["type"] == "image/png"))
            && ($_FILES['buttonfile']["size"] < 120000)) {
        if ($_FILES['buttonfile']["error"] > 0) {
        }
        else {
		$buttonPath=BUTTONPATH.$linkname.'_'.$linkid.'-'.$rand.'.png';
		renameFiles(BUTTONPATH, $buttonPath, $linkname);
		move_uploaded_file($_FILES['buttonfile']['tmp_name'],$buttonPath);
		$_SESSION['BUTTONPATH']=$buttonPath;
		}
    }
    else {
        //echo "Invalid file";
    }
}

try {
    $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
    $db->connect();

    // init logic
    // link_id is passed both from manager form and from this form, so it should always be set

				
				
    $newLink = 0;
    if(isset($_REQUEST['link_id'])) {
        if ((int)$_REQUEST['link_id'] == -1)
            $newLink = 1;
		else{
			$linkId=$_REQUEST['link_id'];
			$_SESSION['linkid']=$linkId;
		}
    }

    if (isset($_SESSION['SESS_MEMBER_ID'])) {
        $userId = $_SESSION['SESS_MEMBER_ID'];
    }else $userId = 1;

    if (! isset($_POST['Submit'])) {
        $_POST['Submit'] = 'Login';
    }

    if (isset($_POST['Home'])) {
        $home = $_POST['Home'];
        $url = "main.php";
        cancel_changes($link, $newLink);
        session_write_close();
        header("Location: $url");
    }
    if (isset($_POST['Logout'])) {
        $logout = $_POST['Logout'];
        $url = "../logout.php";
        cancel_changes($link, $newLink);
        session_write_close();
        header("Location: $url");
    }
    if (isset($_POST['ManageUsers'])) {
        $url = "manager.php";
        cancel_changes($link, $newLink);
        session_write_close();
        header("Location: $url");
    }

    $msgframe = array();
    if ($newLink == 1 ) {		
        // came from manager page to create new link
        $link_vals = array('url' => "http://www.", 'imgpath' => "thumbnails/contentThumbnail.png", 'name' => "New Link", 'description' => "", 'imgbutton' => "../webdev/Images/Menu/Buttons/emptyButton.png");
        $link = new Link($link_vals);
        $_SESSION['linkid'] = $link->getId();
        $manageName = 'Create Item';
        $msgframe = array();
		$surveyfreeframe = array();
		$surveymultiframe = array();
    } else {
        // either came to edit a link or page reloaded after save
        $manageName = 'Edit Item';
        $link = new Link((int)$_SESSION['linkid']);
    }

    // load vars
    $id=$link->getId();
    $businesses=$link->getBusinesses();
    $locations=$link->getLocations();


	if(isset($_POST['Upload'])) {
            if ( $_POST['Upload'] == 'Upload' ) {
                fileUpload($link->getName(),$link);
            }
        }

    if(isset($_POST['UploadButton'])) {
            if ( $_POST['UploadButton'] == 'Upload Button' ) {
                buttonUpload($link->getName(),$link);
            }
        }

    if (!isset($_SESSION['MSGFRAME'])) {
        $_SESSION['MSGFRAME']="UPDATE";
    }
    if (!isset($_SESSION['SURVEYFREEFRAME'])) {
        $_SESSION['SURVEYFREEFRAME']="UPDATE";
    }
    if (!isset($_SESSION['SURVEYMULTIFRAME'])) {
        $_SESSION['SURVEYMULTIFRAME']="UPDATE";
    }	

    if(isset($_POST['url'])) {
        $url=$_POST['url'];
		$imagePath='';
		$buttonPath='';
		if(isset($_SESSION['IMAGEPATH']))
			$imagePath=$_SESSION['IMAGEPATH'];				
		if(isset($_SESSION['BUTTONPATH']))
			$buttonPath=$_SESSION['BUTTONPATH'];

        $name=$_POST['name'];
        $description=$_POST['description'];
        $businesses=getNewbusinesses($link);
        $locations=getNewlocations($link);       
    }
    else {		
        $url=$link->getUrl();
		if(isset($_SESSION['IMAGEPATH']) && $_SESSION['IMAGEPATH'] != ''){
                    $imagePath=$_SESSION['IMAGEPATH'];
		}else
			$imagePath=$link->getImgpath();
        if(isset($_SESSION['BUTTONPATH']) && $_SESSION['BUTTONPATH'] != ''){
                    $buttonPath=$_SESSION['BUTTONPATH'];
		}
		else
			$buttonPath=$link->getImgbutton();
        $_SESSION['IMAGEPATH']=$imagePath;
        $_SESSION['BUTTONPATH']=$buttonPath;
        $name=$link->getName();
        $description=$link->getDescription();
        $businesses=$link->getBusinesses();
        $locations=$link->getLocations();
        
    }
	

	
	if(isset($_SESSION['MSGFRAMEARR']))
	{
		$msgframe=$_SESSION['MSGFRAMEARR'];
	}else{
		 if ($newLink == 0)
            $msgframe = $link->getMessages();
	}
	
	if(isset($_SESSION['SURVEYFREEFRAMEARR']))
	{
		$surveyfreeframe=$_SESSION['SURVEYFREEFRAMEARR'];
	}else{
		 if ($newLink == 0)
            $surveyfreeframe = $link->getFreeSurvey();
	}
	
	if(isset($_SESSION['SURVEYMULTIFRAMEARR']))
	{
		$surveymultiframe=$_SESSION['SURVEYMULTIFRAMEARR'];
	}else{
		 if ($newLink == 0)
            $surveymultiframe = $link->getMultiSurvey();
	}

	

				
   if ($_SESSION['MSGFRAME']=="UPDATE")
        $_SESSION['MSGFRAMEARR']=$msgframe;
   if ($_SESSION['SURVEYFREEFRAME']=="UPDATE")
        $_SESSION['SURVEYFREEFRAMEARR']=$surveyfreeframe;		
   if ($_SESSION['SURVEYMULTIFRAME']=="UPDATE")
        $_SESSION['SURVEYMULTIFRAMEARR']=$surveymultiframe;	

		
}
catch(PDOException $e) {
    echo 'No Results';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Medtronic LTD</title>
        <meta http-equiv="Content-Language" content="English" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>




        <link href="css/style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php


        if(isset($_POST['Submit'])) {

            if ( $_POST['Submit'] == 'Save Changes' ) {
                $_SESSION['MSGFRAME']="UPDATE";
                $_POST['Submit'] = 'Login';

                if(!isset($_SESSION['MSGFRAMEARR'])) {
                    if($newLink == 1)
                        $_SESSION['MSGFRAMEARR'] = array();
                    else
                        $_SESSION['MSGFRAMEARR'] = $link->getMessages();
                }

                if(isset($_SESSION['IMAGEPATH']) && $_SESSION['IMAGEPATH'] != ''){
                    $imagePath=$_SESSION['IMAGEPATH'];
					$link->setImgpath($imagePath);

					}
                if(isset($_SESSION['BUTTONPATH']) && $_SESSION['BUTTONPATH'] != ''){
                    $buttonPath=$_SESSION['BUTTONPATH'];
					$link->setImgbutton($buttonPath);

					}

                $newmsgframe = getNewmsgframe($link, $_SESSION['MSGFRAMEARR']);
				$newsurveyfreeframe = getNewSurveyFreeframe($link, $_SESSION['SURVEYFREEFRAMEARR']);
				$newsurveymultiframe = getNewSurveyMultiframe($link, $_SESSION['SURVEYMULTIFRAMEARR']);
				$businesses=getNewbusinesses($link);
				$locations=getNewlocations($link);
				
                $link->setUrl($url);
                $link->setName(double_to_single($name));
                $link->setDescription($description);
                $link->updateBusinesses($businesses);
                $link->updateLocations($locations);
                $link->updateMessages($newmsgframe);
				/*
				echo'<br> newsurveyfreeframe=';
				print_r($newsurveyfreeframe);
				echo'<br> newsurveymultiframe=';
				print_r($newsurveymultiframe);
				*/
				$link->updateSurveyFree($newsurveyfreeframe);
				$link->updateSurveyMulti($newsurveymultiframe);
                $link->commit();				
                $newframe=cancel_changes($link, $newLink);
				$msgframe=$newframe['msgframe'];
				$surveyfreeframe=$newframe['surveyfreeframe'];
				$surveymultiframe=$newframe['surveymultiframe'];
				cancel_changes($link, $newLink);	
				$url = "main.php";
				header("Location: $url");
            }
            elseif ( $_POST['Submit'] == 'Delete Link' ) {
                if($newLink == 0) {
                    $link->delete();
                    $link->commit();
                }
				echo'<br> Delete Link';
				$newframe=cancel_changes($link, $newLink);
				$msgframe=$newframe['msgframe'];
				$surveyfreeframe=$newframe['surveyfreeframe'];
				$surveymultiframe=$newframe['surveymultiframe'];
                $url = "main.php";
                header("Location: $url");
            }
        }


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
        <?php
        include 'header.php';
        ?>
        <form id="editlink" name="editlink" action="edit_link.php" method="post" enctype="multipart/form-data">


            <div id="middle" style="align:center; height:auto; min-height:600px"/>
            <table  align="center" width="1000" cellpadding="10" CELLSPACING="0"  border="0">
                 <tr>
                        <td align="left" width="105">
                            <!--<input id="home" type=button onClick="location.href='main.php'" value='Home'/>-->
							<input id="home" type="submit" name="Home" value="Home"/>

                        </td>
                        <td align="left">
                                        <b><? echo "$manageName"?></b>
                        </td>

                </tr>


            </table>
			<div id="import" align="center"><h1 style="padding-top:10px"><? echo "$manageName"?></h1></div>

            <table  align="center" border="0" cellpadding="8" width="1000" CELLSPACING="0" >

	<script type="text/javascript">
	function alphanumericCheck(string) {
	// checks if string contains at least one alphanumeric character
	var regex=/[0-9A-Za-z]+/; //^[a-zA-z]+$/
	if(regex.test(string)){
	return true;
	} else {
	alert("The name of a link must contain at least one alpha-numeric character");
	return false;
	}
	}
	</script>

                <?php

                echo "<tr><td width=\"200px\"><h2>URL</h2></td>
		  <td><input type=\"text\" name=\"url\" value=\"$url\" style=\"width: 600px;\"></td>
		  <td>&nbsp;</td></tr>";

                echo "<tr><td><h2>Name</h2></td>
					  <td><input type=\"text\" name=\"name\" value=\"$name\" style=\"width: 600px;\" onblur=\"alphanumericCheck(this.value);\"></td>
					  <td>&nbsp;</td></tr>";

                echo "<tr><td><h2>Description</h2></td>
					 <td><input type=\"text\" name=\"description\" value=\"$description\" style=\"width: 600px;\"></td>
					 <td>&nbsp;</td></tr>";


                ?>
                <tr>
                    <td style=" width: 200px">
                        <h2>Thumbnail</h2>
                    </td>
                    <td>

                        <?php
                        if($imagePath!='') {

                            echo '<img src="'.$imagePath.'" height="260px" width="240px" />';

                        }
                        echo "<br /><input type=hidden name=\"imgpath\" value=\"$imagePath\" style=\"width: 600px;\">";


                        ?>
                        <script>
                         //alert(navigator.appName);
						 if(navigator.appName=="Microsoft Internet Explorer"){
							document.write('<input style=" width: 600px;" type="file" name="userfile" id="file" />');}
						 else{document.write('<div style="  width:600px;"><input size="45" type="file" name="userfile" id="file" /></div>');}
                        </script>
                                               <!--<input style="width: 600px;" type="file" name="userfile" id="file" />-->



                    </td>
                    <td>
                        <input id="middlebutton"type="submit" name="Upload" value="Upload" />
                    </td>
                </tr>

                <tr>
                    <td style=" width: 200px">
                        <h2>Menu Button</h2>
                    </td>
                    <td>

                        <?php

                        if($buttonPath!='') {
                            echo '<div style="height:315px; width:253px; background-image:url(\''.$buttonPath.'\')"
                                onmousedown="this.style.backgroundPosition=\'bottom\'"
                                onmouseup="this.style.backgroundPosition=\'top\'"


></div>';

                        }
                        echo "<br /><input type=hidden name=\"imgpath\" value=\"".$buttonPath."\" style=\"width: 600px;\">";
                        ?>
						<script>

						 if(navigator.appName=="Microsoft Internet Explorer"){
							document.write('<input style="width: 600px;" type="file" name="buttonfile" id="file" />');}
						 else{document.write('<div style="width:600px"><input size="45" type="file" name="buttonfile" id="file" /></div>');}
                        </script>


                    </td>
                    <td>
                        <input id="middlebutton"type="submit" name="UploadButton" value="Upload Button" />
                    </td>
                </tr>

<?php
                        echo '<tr>';						
                        echo '<td width="200px">&nbsp;</td>';
                        echo '<td>';
								echo'<table border="0" width="606px">';
								echo'<tr><th>';
									echo'<h2>Business</h2><div style="height:100px; overflow:auto; width: 290px; float: left;">';
									
			                        foreach ($link->getAllBusinesses() as $key => $value) {                              
		                                echo '<h2><input type="checkbox" name="businessselect'.$key.'"';
		                                if (in_array($value, $businesses))
		                                    echo 'checked="YES"';
		                                echo "/>$value<br></h2>";
			                        }
								echo'</th>';
								echo '<th><h2>&nbsp;Location</h2><div style="height:100px; overflow:auto; width: 290px; float: right;">';

		                            foreach ($link->getAllLocations() as $key => $value) {
		                                echo '<h2><input type="checkbox" name="locationselect'.$key.'"';
		                                if (in_array($value, $locations))
		                                    echo ' checked="YES"';
		                                echo "/>$value<br></h2>\n";
		                            }
								echo' </th>';								
								echo'</tr></table>';
	                    echo '</td>';
                        						                                                                    
						echo '<td>&nbsp;</td>';
                        echo'</tr>';
?>

                <?php
                echo "<input type=hidden name=\"link_id\" value=$id>";
                ?>


                <?php
				
				if(isset($_REQUEST['deletekey'])) {
                    $_SESSION['MSGFRAME']="DELETE";                   
                    $delete=$_REQUEST['deletekey'];
                    unset($msgframe[$delete]);
                    $_SESSION['MSGFRAMEARR']= getNewmsgframe($link, $msgframe);
                    $msgframe = $_SESSION['MSGFRAMEARR'];		
					
                }
                elseif (isset($_POST['AddMessage'])) {

                    $_SESSION['MSGFRAME']="ADD";

                    if(isset($_SESSION['new_msg_ind']))
                        $new_msg_ind = intval($_SESSION['new_msg_ind']) + 1;
                    else
                        $new_msg_ind = Link::getMaxMessagesIndex() + 1;

                    $_SESSION['new_msg_ind'] = $new_msg_ind;

                    $msgframe[$new_msg_ind] =  array (  
                            "messages_id" => $new_msg_ind,
                            "message"     => "message new",
                            "businesses"  => array(),
                            "locations"   => array()
                    );
                }

//delete/add survey free
				 if(isset($_REQUEST['deletekeysf'])) {
                    $_SESSION['SURVEYFREEFRAME']="DELETE";                   
                    $delete=$_REQUEST['deletekeysf'];
                    unset($surveyfreeframe[$delete]);
                    $_SESSION['SURVEYFREEFRAMEARR']= getNewSurveyFreeframe($link, $surveyfreeframe);
                    $surveyfreeframe = $_SESSION['SURVEYFREEFRAMEARR'];				
                }
                elseif (isset($_POST['AddSurveyFree'])) {
                    $_SESSION['SURVEYFREEFRAME']="ADD";
                    if(isset($_SESSION['new_surveyfree_ind']))
                        $new_surveyfree_ind = intval($_SESSION['new_surveyfree_ind']) + 1;
                    else
                        $new_surveyfree_ind = Link::getMaxFreeSurveyIndex() + 1;

                    $_SESSION['new_surveyfree_ind'] = $new_surveyfree_ind;

                    $surveyfreeframe[$new_surveyfree_ind] =  array (                           
							"surveys_id" => $new_surveyfree_ind,
                            "question"     => "Please enter question here",
                            "businesses"  => array(),
                            "locations"   => array()
                    );



                }				
//delete/add survey multi
				 if(isset($_REQUEST['deletekeysm'])) {
                    $_SESSION['SURVEYMULTIFRAME']="DELETE";                   
                    $delete=$_REQUEST['deletekeysm'];
                    unset($surveymultiframe[$delete]);
                    $_SESSION['SURVEYMULTIFRAMEARR']= getNewSurveyMultiframe($link, $surveymultiframe);
                    $surveymultiframe = $_SESSION['SURVEYMULTIFRAMEARR'];				
                }
                elseif (isset($_POST['AddSurveyMulti'])) {
                    $_SESSION['SURVEYMULTIFRAME']="ADD";
                    if(isset($_SESSION['new_surveymulti_ind']))
                        $new_surveymulti_ind = intval($_SESSION['new_surveymulti_ind']) + 1;
                    else
                        $new_surveymulti_ind = Link::getMaxMultiSurveyIndex() + 1;

                    $_SESSION['new_surveymulti_ind'] = $new_surveymulti_ind;


                    $surveymultiframe[$new_surveymulti_ind] =  array (  
                            "surveys_id" => $new_surveymulti_ind,
                            "question"     => "Please enter question here",
							"answers" => array (
								"answer1"     => "",
								"answer2"     => "",
								"answer3"     => "",
								"answer4"     => ""),
								//"answer5"     => "answer5 new"),														
                            "businesses"  => array(),
                            "locations"   => array()
                    );


                }		


				if(isset($_POST['AddSurveyMulti']) || isset($_POST['AddSurveyFree']) || isset($_POST['AddMessage'])){
						$_SESSION['MSGFRAMEARR'] = getNewmsgframe($link, $msgframe);
	                    $msgframe = $_SESSION['MSGFRAMEARR'];
	                    $_SESSION['SURVEYFREEFRAMEARR'] = getNewSurveyFreeframe($link, $surveyfreeframe);
	                    $surveyfreeframe = $_SESSION['SURVEYFREEFRAMEARR'];
	                    $_SESSION['SURVEYMULTIFRAMEARR'] = getNewSurveyMultiframe($link, $surveymultiframe);
	                    $surveymultiframe = $_SESSION['SURVEYMULTIFRAMEARR'];					
				}

				
				
                $businessesArr = Business::getTypes();
                $locationsArr = Locations::getTypes();
                if(count($msgframe))
					$msgCounter = 1;
                    foreach ($msgframe as $key => $value) {
                        $busWhatsOn = array();
                        $locWhatsOn = array();

                        echo '<tr>';
                        echo '<th align="left" width="200px"><h2>Message '.$msgCounter.'</h2></th>';
                        echo '<th >
						 <textarea name="message'.$key.'" rows=2  wrap="soft" style="width: 600px;">'. $value['message'] .'</textarea>
						 </th>';
                        echo '</tr>';
                        echo '<tr>';						
                        echo '<td width="200px">&nbsp;</td>';
                        echo '<td>';
								echo'<table border="0" width="606px">';
								echo'<tr><th>';
									echo'<h2>Business</h2><div style="height:100px; overflow:auto; width: 290px; float: left;">';
									$msgCounter++;

			                        foreach($value['businesses'] as $index => $value1) {
			                            $busWhatsOn[$index]=$value1;
			                        }
			                        foreach ($businessesArr as $busindex => $busvalue) {
			                            echo '<h2><input type="checkbox" name="msgbusinessselect'.$key.'_'.$busindex.'"';
			                            if (in_array($busvalue, $busWhatsOn))
			                                echo ' checked="YES"';
			                            echo '>';
			                            echo $busvalue;
			                            print(" <br></h2>\n");
			                        }
								echo'</th>';
								echo '<th><h2>&nbsp;Location</h2><div style="height:100px; overflow:auto; width: 290px; float: right;">';
			                        foreach($value['locations'] as $index => $value1) {
			                            $locWhatsOn[$index]=$value1;
			                        }
			                        foreach ($locationsArr as $locindex => $value3) {
			                            echo '<h2><input type="checkbox" name="msglocationselect'.$key.'_'.$locindex.'" ';
			                            if (in_array($value3, $locWhatsOn))
			                                echo 'checked="YES"';
			                            echo '>';
			                            echo $value3;
			                            echo '<br></h2>';
			                        }
								echo' </th>';								
								echo'</tr></table>';
	                    echo '</td>';
                        						                       
                                               
						echo' <td>';
						echo" <input id=\"middlebutton\" type=button  onClick=\"location.href='?deletekey=$key', submitForm();\" value=\"Delete\">";						
						echo '</td>';
                        echo'</tr>';
                    }  

//survey free
                if(count($surveyfreeframe))
					$surveysFreeCounter = 1;
                    foreach ($surveyfreeframe as $key => $value) {
					
                        $busWhatsOn = array();
                        $locWhatsOn = array();
                        echo '<tr>';
                        echo '<th align="left" width="200px"><h2>Free Text Question '.$surveysFreeCounter.'</h2></th>';
                        echo '<th ><h2>Question</h2>						
						 <textarea name="questionf'.$key.'" rows=2  wrap="soft" style="width: 600px; ">'. $value['question'] .'</textarea>						 
						 </th>';
                        echo '</tr>';
                        echo '<tr>';						
                        echo '<td width="200px">&nbsp;</td>';
                        echo '<td>';
								echo'<table border="0" width="606px">';
								echo'<tr><th>';
									echo'<h2>Business</h2><div style="height:100px; overflow:auto; width: 290px; float: left;">';
									$surveysFreeCounter++;

			                        foreach($value['businesses'] as $index => $value1) {
			                            $busWhatsOn[$index]=$value1;
			                        }
			                        foreach ($businessesArr as $busindex => $busvalue) {
			                            echo '<h2><input type="checkbox" name="qfbusinessselect'.$key.'_'.$busindex.'"';
			                            if (in_array($busvalue, $busWhatsOn))
			                                echo ' checked="YES"';
			                            echo '>';
			                            echo $busvalue;
			                            print(" <br></h2>\n");
			                        }
								echo'</th>';
								echo '<th><h2>&nbsp;Location</h2><div style="height:100px; overflow:auto; width: 290px; float: right;">';
			                        foreach($value['locations'] as $index => $value1) {
			                            $locWhatsOn[$index]=$value1;
			                        }
			                        foreach ($locationsArr as $locindex => $value3) {
			                            echo '<h2><input type="checkbox" name="qflocationselect'.$key.'_'.$locindex.'" ';
			                            if (in_array($value3, $locWhatsOn))
			                                echo 'checked="YES"';
			                            echo '>';
			                            echo $value3;
			                            echo '<br></h2>';
			                        }
								echo' </th>';								
								echo'</tr></table>';
	                    echo '</td>';
                        						                       
                                               
						echo' <td>';
						echo" <input id=\"middlebutton\" type=button  onClick=\"location.href='?deletekeysf=$key', submitForm();\" value=\"Delete\">";
						//echo '<a href="?deletekey='.$key.'" class="read-more" ><input  id=middlebutton type=button value=Delete ></input></a>';
						echo '</td>';
                        echo'</tr>';
                    }   		
					
                       					
//survey multi
                if(count($surveymultiframe))
					$surveysMultiCounter = 1;
                    foreach ($surveymultiframe as $key => $value) {

                        $busWhatsOn = array();
                        $locWhatsOn = array();
                        echo '<tr>';
                        echo '<th align="left" width="200px"><h2>Multiple Choice Question '.$surveysMultiCounter.'</h2></th>';
                        echo '<th ><h2>Question</h2>
						 <textarea name="questionm'.$key.'" rows=2  wrap="soft" style="width: 600px;">'. $value['question'] .'</textarea>
						 </th>';
                        echo '</tr>';
						echo '<tr>';
						echo '<th width="200px"></th>';
					
						echo '<th ><div style="height:150px; overflow:auto; width: 604px; ">';
						echo '<h2>Option 1</h2><textarea name="answer1_'.$key.'" rows=2  wrap="soft" style="width: 570px;">'. $value['answers']['answer1'] .'</textarea>';
						echo '<h2>Option 2</h2><textarea name="answer2_'.$key.'" rows=2  wrap="soft" style="width: 570px;">'. $value['answers']['answer2'] .'</textarea>';
						echo '<h2>Option 3</h2><textarea name="answer3_'.$key.'" rows=2  wrap="soft" style="width: 570px;">'. $value['answers']['answer3'] .'</textarea>';
						echo '<h2>Option 4</h2><textarea name="answer4_'.$key.'" rows=2  wrap="soft" style="width: 570px;">'. $value['answers']['answer4'] .'</textarea>';
						//echo '<h2>Answer 5</h2><textarea name="answer5_'.$key.'" rows=2  wrap="soft" style="width: 475px;">'. $value['answers']['answer5'] .'</textarea>';
						echo'</th>';
						
						echo '</tr>';

						
                        echo '<tr>';						
                        echo '<td width="200px">&nbsp;</td>';
                        echo '<td>';
								echo'<table border="0" width="606px">';
								echo'<tr><th>';
									echo'<h2>Business</h2><div style="height:100px; overflow:auto; width: 290px; float: left;">';
									$surveysMultiCounter++;

			                        foreach($value['businesses'] as $index => $value1) {
			                            $busWhatsOn[$index]=$value1;
			                        }
			                        foreach ($businessesArr as $busindex => $busvalue) {
			                            echo '<h2><input type="checkbox" name="qmbusinessselect'.$key.'_'.$busindex.'"';
			                            if (in_array($busvalue, $busWhatsOn))
			                                echo ' checked="YES"';
			                            echo '>';
			                            echo $busvalue;
			                            print(" <br></h2>\n");
			                        }
								echo'</th>';
								echo '<th><h2>&nbsp;Location</h2><div style="height:100px; overflow:auto; width: 290px; float: right;">';
			                        foreach($value['locations'] as $index => $value1) {
			                            $locWhatsOn[$index]=$value1;
			                        }
			                        foreach ($locationsArr as $locindex => $value3) {
			                            echo '<h2><input type="checkbox" name="qmlocationselect'.$key.'_'.$locindex.'" ';
			                            if (in_array($value3, $locWhatsOn))
			                                echo 'checked="YES"';
			                            echo '>';
			                            echo $value3;
			                            echo '<br></h2>';
			                        }
								echo' </th>';								
								echo'</tr></table>';
	                    echo '</td>';
                        						                       
                                               
						echo' <td>';
						echo" <input id=\"middlebutton\" type=button  onClick=\"location.href='?deletekeysm=$key', submitForm();\" value=\"Delete\">";
						//echo '<a href="?deletekey='.$key.'" class="read-more" ><input  id=middlebutton type=button value=Delete ></input></a>';
						echo '</td>';
                        echo'</tr>';
                    }   				
                ?>
				<script language='javascript'>                          						
					function submitForm(){						  						            							
						document.editlink.submit();	
					}				 
				</script>
            </table>
			<table  align="center" border="0" cellpadding="8" width="1000" CELLSPACING="0" >
				<td >&nbsp;</td>
				<td >&nbsp;</td>
				<td >	
									<input id="submit" type="submit" name="AddSurveyFree" value="Add Free Question" >
									<input id="surveymulti" type="submit" name="AddSurveyMulti" value="Add Multiple Question" >
									<input id="submit" type="submit" name="AddMessage" value="Add Message" > 
				</td>
			</table>


            <table  align="center" border="0" cellpadding="8" width="1000" CELLSPACING="0" >
                <tr>

                    <th >

                        <input id="submit" type="submit" name="Submit" value="Save Changes"/>
                        <input id="submit" type="submit" name="Submit" value="Delete Link" />
                    </th>

                </tr>
            </table>
        </form>


        <?php
        include 'footer.php';
        ?>


    </body>
</html>
