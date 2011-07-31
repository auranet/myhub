<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>suite_2.1</title>
    </head>
    <body>
        <?php
        
        date_default_timezone_set('UTC'); //TODO put somewhere else or remove, date returns E_NOTICE exception if not defined
        require_once '../myHubServer/includes/User.php';
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $db->connect();

        //$my_user = new User(array('username'=>'nitzan', 'password'=>'baba', 'firstname'=>'Nitzan', 'lastname'=>'Zaifman','usertype'=>1,'password'=>'blueskies'));
        //echo "id:".$my_user->getId();

        $my_user = new User(1);
        echo "<p>usertype:".$my_user->getType()."</p>";
        echo "<pre>".print_r($my_user->getLocation())."</pre>";
        echo "<pre>".print_r($my_user->getBusiness())."</pre>";
        $db->close();
        /*$my_user=NULL;

        echo "other username:".$my_other_user->getUserName();
        echo "other usertype:".$my_other_user->getType();
        $my_other_user->setType(4);
        echo "other usertype:".$my_other_user->getType();*/
       
        ?>
    </body>
</html>

