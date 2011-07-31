<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Forgot Password</title>
    </head>

    <body style="background-image:url('Images/Login/loginBackground.png'); background-size: 100%;">


        <div id="root">

            <div id="top">
                <div id="header">

                </div>
            </div>

            <div id="middle">


                <?php
                include('includes/config.php');

                $email = clean($_POST['email']);

// Creates a Random Password and emails it to the user

                /*
                 * The letter l (lowercase L) and the number 1
                 * have been removed, as they can be mistaken
                 * for each other.
                 */


                function createRandomPassword() {
                    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
                    srand((double) microtime() * 1000000);
                    $i = 0;
                    $pass = '';

                    while ($i <= 7) {
                        $num = rand() % 33;
                        $tmp = substr($chars, $num, 1);
                        $pass = $pass . $tmp;
                        $i++;
                    }

                    return $pass;
                }

// Usage
// Check if etered Email exist in database
                $queryCheckEmail = "SELECT username FROM users WHERE username = '$email';";
                $resultCheckEmail = mysql_query($queryCheckEmail);
                $row = mysql_fetch_array($resultCheckEmail);
                $check = $row['username'];

                if ($check) {

                    $password = createRandomPassword();
                    $hash = sha1($password);
                    $queryRandomPassword = "UPDATE users SET Password = '$hash' WHERE username = '$email'";
                    $resultRandomPassword = mysql_query($queryRandomPassword);
                    if ($resultRandomPassword) {

                        $recip = $email;
                        $headers = 'MIME-Version: 1.0' . "\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
                        $headers .= 'Message-ID: <' . time() . rand(1, 1000) . '@myhub.com>' . "\n";
                        $headers .= 'From: Support <noreply@myhub.com>' . "\n";
                        $subject = 'Password Reset ';

                        $message = 'Thank you for resetting your password.' . "<br /><br />";
                        $message .= 'Your new password is: ' . $password . '<br /><br />';

                        // Sends email
                        mail($recip, $subject, $message, $headers);

                        // reset bad login counter
                        start_session();
                        $_SESSION['TYPE_COUNT'] = 0;
                        ?>
                        <p align="center">&nbsp;</p>
                        <h4 align="center" class="err" style="color:white">Password updated. Check your Email for your new password. <br /></h4>
                        <div align="center">
                            <a href="login.php"><font color="black">Click Here to login.</font></a>
                        </div>



        <?php
    } else {
        die("Query failed");
    }
} else {
    ?>

                    <p align="center">&nbsp;</p>
                    <h4 align="center" class="err" style="color:white;">Email does not exist.<br />
    	  Please check your Email address</h4>
                    <div align="center">
                        <a href="login.php"><font color="black">Back</font></a>
                    </div>

                    <?php
                }
                ?>

            </div>

        </div>
  <!-- prevent from opening a href in new browser window -->
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
