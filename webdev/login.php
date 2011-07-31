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
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="viewport" content="user-scalable=0;">

        <!-- Add to home screen popup balloon script -->	
        <link rel="apple-touch-icon" href="JavaScript/add_home_screen/desktop_icon.png">
        <link rel="stylesheet" href="JavaScript/add_home_screen/style/add2home.css?v2">
        <script type="text/javascript">
            var addToHomeConfig = {
                animationIn:'bubble',           // Animation In
                animationOut:'drop',            // Animation Out
                lifespan:10000,                         // The popup lives 10 seconds
                expire:2,                                       // The popup is shown only once every 2 minutes
                touchIcon:true,
                message:'This Web App is optimized for viewing in full screen mode. Please click the %icon button on the top bar to save it to your home screen and launch it from there.'
            };
        </script>
        <script type="application/javascript" src="JavaScript/add_home_screen/src/add2home.js?v0.9.4"></script>
	<!-- submit login form on return hit -->
	<script type="text/javascript">
        function submit_form(oEvent, oInput) {
        var KeyPress;
        if (oInput.value == '')
        {
            return false;
        }
 
        if (oEvent && oEvent.which)
        {
            oEvent = oEvent;
            KeyPress = oEvent.which;
        }
        else
        {
            oEvent = event;
            KeyPress = oEvent.keyCode;
        }
 
        if (KeyPress == 13)
        {
            document.getElementById('login_form').submit();
            oInput.value = '';
            return true;
        }
        return false;

        }
        </script>

        <title>MyHub</title>
    </head>

    <body class="login" style="margin:0px auto 0px auto;"> 

        <div id="login_title">Log In to <span class="title">MyHub!</span></div>

        <form action="login-exec.php" method="post" id="login_form">
            <table id="login"><tr>
                    <td style="vertical-align:middle;"><label for="username">Username: </label></td>
                    <td><input type="text" id="username" name="login"></td>
                </tr><tr>
                    <td style="vertical-align:middle;"><label for="password">Password: </label></td>
                    <td><input type="password" id="password" name="password" onkeypress="submit_form(event,this);"></td>
                </tr>
            </table>
            <input class="login_submit" type="button" value="submit" onClick="javascript:submit();">
            <?php
            // conditionally display "forgot password?" link
            $top_offset = "480px";
            session_start();
            if (isset($_SESSION['TYPE_COUNT']) and $_SESSION['TYPE_COUNT'] >= 2) {
                echo "<div id=\"forgot_password\"><a href=\"forgotpassword.php\"> Forgot password?</a></div>";
                $top_offset = "260px";
            }

            echo "</form>";
            ?>
	 <!-- don't open a href in new browser window -->
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

    </body>
</html>

