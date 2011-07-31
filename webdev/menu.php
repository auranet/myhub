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

session_start();
if (!isset($_SESSION['SESS_MEMBER_ID'])) {
              header("location:login.php");
         }

if (strrpos($_SERVER['HTTP_USER_AGENT'], "iPad") > 0){
	// ipad
        echo "<link rel=StyleSheet media=\"all and (orientation:landscape)\" href=\"css/style.css\" type=\"text/css\">
        <link rel=StyleSheet media=\"all and (orientation:portrait)\" href=\"css/style_portrait.css\" type=\"text/css\">";
} else if (strrpos($_SERVER['HTTP_USER_AGENT'], "Chrome")) {
	    //echo "<link rel=StyleSheet media=\"all\" href=\"css/style_chrome.css\" type=\"text/css\">";
            header("location:menu_desktop.php");
} else {
        header("location:menu_desktop.php");
}

?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <title>Welcome to myHub</title>

        <script type="text/javascript" src="JavaScript/iscroll.js"></script>

        <script type="text/javascript">
            var myScroll;

            function loaded() {
                myScroll = new iScroll('wrapper', {
                    snap: true,
                    momentum: false,
                    hScrollbar: false,
                    onScrollEnd: function () {
                        document.querySelector('#indicator > li.active').className = '';
                        document.querySelector('#indicator > li:nth-child(' + (this.currPageX+1) + ')').className = 'active';
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', loaded, false);
        </script>

	
    </head>
    <body class="menu">
        <?php

        require_once '../myHubServer/includes/User.php';
        require_once '../myHubServer/includes/Link.php';
	try {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $db->connect();

        $my_user = new User((int) $_SESSION['SESS_MEMBER_ID']);

        $links = $my_user->getLinks();
	}
	catch (PDOException $e) {
	echo "<script type=\"text/javascript\"> 
	      window.alert('Could not connect to database!');
	      </script>";
	}
        $indicator_dots = ceil(count($links) / 4);
        ?>

        <!--<div id="header">header</div>-->
        <div id="wrapper">
            <div id="scroller" style="width:<?php echo 255 * count($links); ?>px;">
                <ul id="thelist">
                    <?php
                    foreach ($links as $id => $name) {
                        $form_name = "link" . $id;
                        $im_id = "im" . $id;

                        // fetch button image
                        $tmp_link = new Link($id);
                        $button = $tmp_link->getImgbutton();

                        echo "<li>
			<form name=\"$form_name\" action=\"content.php\" method=\"get\">
			<div id=\"$im_id\" class=\"menu_button\" style=\"background-image:url('$button'); background-position:top;\" ";
                        echo "ontouchstart=\"this.style.backgroundPosition='bottom'\" ";
                        echo "onclick=\"this.style.backgroundPosition='bottom';document.$form_name.submit();\" ";
                        echo "ontouchend=\"this.style.backgroundPosition='top'\">";
			// if no button image, overlay text
			if (!strcmp($button, "../webdev/Images/Menu/Buttons/emptyButton.png"))
				echo "<span style=\"color:white; position:relative; top:100px;\">$name</span>"; 
                        echo "</div>
			<input type=\"hidden\" name=\"link_id\" value=\"$id\">
			</form>
			</li>";
                        echo "\n";
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div id="nav">
            <ul id="indicator">

                <li class="active">1</li>
                <?php
                // create as many li's as needed
                $counter = 2;
                while ($indicator_dots > 1) {
                    echo "<li>$counter</li>";
                    echo "\n";
                    $indicator_dots -= 1;
                    $counter += 1;
                }
                ?>
            </ul>
        </div>
	<!-- set vertical positioning depending on presence of address bar -->
	<?php
	  if (strrpos($_SERVER['HTTP_USER_AGENT'], "iPad") > 0){
	    echo "
	    <script type=\"text/javascript\">
	    if (!window.navigator.standalone){
	        var wrapper = document.getElementById(\"wrapper\");
	        wrapper.style.top=\"280px\";
	        document.getElementById(\"indicator\").style.top=\"200px\";
	    }
	    else {
	        document.getElementById(\"wrapper\").style.top=\"340px\";
	        document.getElementById(\"indicator\").style.top=\"280px\";
	    }
	    </script>
	    ";
	  }
	?>
    </body>
</html>
