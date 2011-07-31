<?php
if (!strrpos($_SERVER['HTTP_USER_AGENT'], "iPad")){
echo "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/strict.dtd\">
<html style=\"border:0px; padding:0px; background:#1f2e3d;\" xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" />
";
} else {
echo "<html><head>";
}
if (strrpos($_SERVER['HTTP_USER_AGENT'], "iPad") > 0){
	// ipad
        echo "<link rel=StyleSheet media=\"all and (orientation:landscape)\" href=\"css/style.css\" type=\"text/css\">
        <link rel=StyleSheet media=\"all and (orientation:portrait)\" href=\"css/style_portrait.css\" type=\"text/css\">";
}else{
  	if (strrpos($_SERVER['HTTP_USER_AGENT'], "Firefox")) {
       	    echo "<link rel=StyleSheet media=\"all\" href=\"css/style_mozilla.css\" type=\"text/css\">";
	}
	else if (strrpos($_SERVER['HTTP_USER_AGENT'], "Chrome")) {
	    echo "<link rel=StyleSheet media=\"all\" href=\"css/style_chrome.css\" type=\"text/css\">";
	}
	else if (strrpos($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
	    echo "<link rel=StyleSheet media=\"all\" href=\"css/style_ie.css\" type=\"text/css\">";
	}
}
?>
<meta name="viewport" content="maximum-scale=1.0, minimum-scale=1.0, initial-scale=1.0, user-scalable=0;"/>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
</head>

<body class="content" style="margin:0px auto 0px auto; background-repeat:no-repeat;">
<?php
session_start();
if (!isset($_SESSION['SESS_MEMBER_ID'])) {
    header("location:login.php");
}

$link_id = (int) $_GET['link_id'];

// fetch link data
require_once '../myHubServer/includes/Link.php';
require_once '../myHubServer/includes/User.php';
try {
$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();

$my_link = new Link($link_id);
$my_user = new User((int) $_SESSION['SESS_MEMBER_ID']);

$my_link_imagepath = "../myHubServer/".$my_link->getImgpath();
$my_link_url = $my_link->getUrl();
$my_link_name = $my_link->getName();
$my_link_description = $my_link->getDescription();

$my_location = $my_user->getLocation();
$my_businesses = $my_user->getBusinessIds();
$my_link_messages = $my_link->getMyMessages($my_location, $my_businesses);

$my_general_messages = $my_user->getGeneralMessage();
}
catch (PDOException $e) {
echo "<script type=\"text/javascript\"> 
      window.alert('Could not connect to database!');
      </script>";
}

echo "<a href=\"menu.php\"><input id=\"content_back_button\" class=\"back_button editable\" type=\"button\" value=\"myHub\" onclick=\"location.href='menu.php'\"></a>";
echo "<div id=\"content_title\" class=\"content_title editable\">$my_link_name</div>";
echo "<div id=\"content_image\" class=\"editable\"><img src=\"$my_link_imagepath\"></div>
<div id=\"content_messages\" class=\"editable\">
<div id=\"mess1\">$my_link_name<br /></div>
<div id=\"mess2\"><br />$my_link_name Description and Message Board:<br /><br /></div>
<div id=\"mess3\">$my_link_description<br /><br /></div>";
foreach ($my_link_messages as $index => $message) {
    echo "<div id=\"mess3\">";
	echo $message;
        echo "</div><br />";
}
echo "</div>";

if (strrpos($_SERVER['HTTP_USER_AGENT'], "iPad") > 0){
echo "<form name=\"iframe\" action=\"iframe.php\" method=\"get\">
    <img id=\"launch\" class=\"launch_button editable\" src=\"Images/Content/contentButtonPress-hit.png\" 
	ontouchstart=\"document.getElementById('launch').src='url(Images/Content/contentButtonPress.png)';\" 
	onclick=\"document.iframe.submit();\" 
	ontouchend=\"document.getElementById('launch').src='url(Images/Content/contentButtonPress-hit.png)';\"> 
	<input type=\"hidden\" name=\"link_id\" value=\"$link_id\">
	<input type=\"hidden\" name=\"link_url\" value=\"$my_link_url\">
    <input type=\"hidden\" name=\"link_name\" value=\"$my_link_name\">
	</form>";
} else {
echo "<form name=\"iframe\" action=\"iframe.php\" method=\"get\">
    <div id=\"launch\" class=\"launch_button editable\" style=\"background-image:url(Images/Content/contentButtonPress-hit.png); width:253px; height:60px;\" 
	onmousedown=\"document.getElementById('launch').style.backgroundImage='url(Images/Content/contentButtonPress.png)';\" 
	onclick=\"document.iframe.submit();\" 
	onmouseup=\"document.getElementById('launch').style.backgroundImage='url(Images/Content/contentButtonPress-hit.png)';\"></div>
	<input type=\"hidden\" name=\"link_id\" value=\"$link_id\">
	<input type=\"hidden\" name=\"link_url\" value=\"$my_link_url\">
    <input type=\"hidden\" name=\"link_name\" value=\"$my_link_name\">
	</form>";
}

	//survey
	$location_id = $my_user->getLocation();
        $businesses_ids = $my_user->getBusinessIds();

        $all_questions = $my_link->fetchMySurvey($location_id, $businesses_ids);
        if(count($all_questions)) {

	if (strrpos($_SERVER['HTTP_USER_AGENT'], "iPad") > 0){ 
                 echo "<img id=\"cont_surv\" src=\"Images/Content/mysurvey.png\" 
                 ontouchstart=\"document.getElementById('cont_surv').src='url(Images/Content/mysurvey-hit.png)';\" 
                 onclick=\"location.href='survey.php?link_id=".$link_id."&report_q=-1';\" 
                 ontouchend=\"document.getElementById('cont_surv').src='url(Images/Content/mysurvey.png)';\"> 
                 ";
                } else {
                 echo "<div id=\"cont_surv\" 
                 style=\"background-image:url(Images/Content/mysurvey.png); width:253px; height:60px;\" 
                 onmousedown=\"document.getElementById('cont_surv').style.backgroundImage='url(Images/Content/mysurvey-hit.png)';\" 
                 onclick=\"location.href='survey.php?link_id=".$link_id."&report_q=-1';\" 
                 onmouseup=\"document.getElementById('cont_surv').style.backgroundImage='url(Images/Content/mysurvey.png)';\"></div>
                 ";
                }  
	} else { // insert blank div for positioning considerations
		echo "<div style=\"height:60px;\"></div>";
	}
	

        echo "<div id=\"content_general_messages\" class=\"editable\">";
        foreach ($my_general_messages as $key => $entry) {
            echo $entry['message'];
            echo " ";
        }
        echo "</div>";

	if (strrpos($_SERVER['HTTP_USER_AGENT'], "iPad") > 0) {
	echo "
	<script type=\"text/javascript\">
	    // handle appearance when viewed in a browser window with an address bar
	    if (!window.navigator.standalone) {
		window.addEventListener(\"orientationchange\",function() {
		var orient = window.orientation;
		var editable = document.getElementsByClassName('editable');
		if (orient == 90 || orient == -90) {
		  // landscape
		  for (var i=editable.length - 1; i >= 0; --i) {
		      editable[i].style.top = \"-10px\";
		      }
		  document.getElementById('content_general_messages').style.top = \"15px\";
		  document.getElementById('launch').style.left = \"545px\";
		} else {
		  for (var i=editable.length - 1; i >= 0; --i) {
		      editable[i].style.top = \"-10px\";
		      }
		  document.getElementById('content_image').style.top = \"115px\";
		  document.getElementById('content_general_messages').style.top = \"45px\";
		  document.getElementById('content_title').style.top = \"-45px\";
		  document.getElementById('launch').style.left = \"410px\";
		  document.getElementById('content_messages').style.top = \"-50px\";
		}
		},false);

	    }
		// keep MyHub button from opening a browser window
                var a=document.getElementsByTagName(\"a\");
                for(var i=0;i<a.length;i++)
                {               
                    a[i].onclick=function()
                    {
                        window.location=this.getAttribute(\"href\");
                        return false
                    }
                }
	</script>
	";
	}
	?>
    </body>
</html>
