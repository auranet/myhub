<html>
    <head>
        <link rel=StyleSheet media="all and (orientation:landscape)" href="css/style.css" type="text/css">
        <link rel=StyleSheet media="all and (orientation:portrait)" href="css/style_portrait.css" type="text/css">
        <meta name="viewport" content="user-scalable=0;"/>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
    </head>

    <body class="forgotpassword" style="background-image:url('Images/Login/loginBackground.png'); background-size:100% 100%;"> 
        <div id="middle">

            <div id="logdiv" style="color:white;">
                <span style="position:relative; left:325px; top:280px; font-size:14px;"><b>Please enter email address to reset your pasword:</b></span><br />
                <br />
                <br />


                <form id="passwordreset" name="passwordreset" method="post" action="forgotpassword-exec.php">
                    <table width="300" border="0" cellpadding="2" cellspacing="0" style="position:relative; left:338px; top:274px;">
                        <tr>
                            <td width="60" style="color:white;"><b>Email</b></td>
                            <td width="240" style="text-align:right;">
                                <input name="email" type="text" class="textfield" id="email" style="width: 230px; height:25px; font-size:16px;" /></td>
                        </tr>


                        <tr>
                            <td></td>
                            <td style="text-align:right"><input type="submit" name="Submit" value="Submit" style="height:25px; font-style:bold; font-size:14px;">
                            </td>

                        </tr>
                    </table>
                </form>
            </div>

    </body>
</html>
