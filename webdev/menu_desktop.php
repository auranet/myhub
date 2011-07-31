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
if (strrpos($_SERVER['HTTP_USER_AGENT'], "Firefox") > 0){
	echo "<link rel=StyleSheet media=\"all\" href=\"css/style_mozilla.css\" type=\"text/css\">";
} else {
	echo "<link rel=StyleSheet media=\"all\" href=\"css/style_ie.css\" type=\"text/css\">";
}
session_start();
if (!isset($_SESSION['SESS_MEMBER_ID'])) {
	header("location:login.php");
}
?>
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

	?>

<div id="wrapper" style="width:1024px; overflow-x:auto; overflow-y:hidden; position:relative; top:320px; left:0px;">
     <div id="scroller" style="position:relative; width:<?php echo 254*(count($links)); ?>px; height: 320px;">     
	
	            <?php
                    foreach ($links as $id => $name) {
                        $form_name = "link" . $id;
                        $im_id = "im" . $id;

                        // fetch button image
                        $tmp_link = new Link($id);
                        $button = $tmp_link->getImgbutton();

                        echo "<div class=\"menu_buttons\">
                        <form name=\"$form_name\" action=\"content.php\" method=\"get\">
                        <div id=\"$im_id\" class=\"menu_button\" style=\"background-image:url('$button'); background-position:top;\" ";
                        echo "ontouchstart=\"this.style.backgroundPosition='bottom'\" ";
                        echo "onclick=\"this.style.backgroundPosition='bottom';document.$form_name.submit();\" ";
                        echo "ontouchend=\"this.style.backgroundPosition='top'\">";
                        // if no button image, overlay text
                        if (!strcmp($button, "../webdev/Images/Menu/Buttons/emptyButton.png"))
                                echo "<span style=\"color:white; position:relative; top:100px; left:80px;\">$name</span>";
                        echo "</div>
                        <input type=\"hidden\" name=\"link_id\" value=\"$id\">
                        </form>
                        </div>";
                        echo "\n";
                    }
                    ?>

	
     </div>
</div>

</body>
</html>
