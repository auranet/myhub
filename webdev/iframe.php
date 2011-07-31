<?php
session_start();
if (!isset($_SESSION['SESS_MEMBER_ID'])) {
    header("location:login.php");
}

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

$browser = $_SERVER['HTTP_USER_AGENT'];
if (strrpos($browser, "iPad") > 0){
        // ipad or chrome
        echo "<link rel=StyleSheet media=\"all and (orientation:landscape)\" href=\"css/style.css\" type=\"text/css\">
        <link rel=StyleSheet media=\"all and (orientation:portrait)\" href=\"css/style_portrait.css\" type=\"text/css\">";
}else{
        if (strrpos($browser, "Firefox") > 0) {
            echo "<link rel=StyleSheet media=\"all and (orientation:landscape)\" href=\"css/style_mozilla.css\" type=\"text/css\">";
        }
        else if (strrpos($browser, "MSIE") > 0) {
            echo "<link rel=StyleSheet media=\"all\" href=\"css/style_ie.css\" type=\"text/css\">";
        } else if (strrpos($browser, "Chrome") > 0) {
		echo "<link rel=StyleSheet media=\"all\" href=\"css/style_chrome.css\" type=\"text/css\">";
	}
}

?>
        <meta name="viewport" content="user-scalable=0;"/>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
	

    </head>

    <body class="iframe" style=""> 
        <form action="content.php">
            <?php

            require_once '../myHubServer/includes/Link.php';

            $link_id = (int) $_GET['link_id'];
            $name = $_GET['link_name'];
            $link_url = $_GET['link_url'];

            //update usage
	    try {
            $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
            $db->connect();

            $link = new Link($link_id);
            $link->logUsage($_SESSION['SESS_MEMBER_ID']);
	    }
	    catch (PDOException $e) {
            echo "<script type=\"text/javascript\"> 
                  window.alert('Could not connect to database!');
                  </script>";
            }

            echo
            "<div id=\"iframe_header\">
	<div class=\"back2div\"><input class=\"back2\" type=\"button\" value=\"$name\" onclick=\"submit()\"></div>";
            echo "<input type=\"hidden\" name=\"link_id\" value=$link_id></form>";
            echo "<div class=\"iframe_title\">$name</div>";
            ?>

        <a href="menu.php"><input class="i_back_button" type="button" value="myHub"></a>
	<!-- for some reason, sometimes pressing the button above opens a browser window. The following fixes that -->
	<script type="text/javascript">
		var a=document.getElementsByTagName("a");
		for(var i=0;i<a.length;i++)
		{ 		
		    a[i].onclick=function()
		    {
        		window.location=this.getAttribute("href");
		        return false
		    }
		}
	</script>


        <?php
        //close header div
        echo "</div>";
        // iframe
        echo "<iframe id=\"iframe\" src=\"$link_url\"/></iframe>";
        ?>
    </body>
</html>
