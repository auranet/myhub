<?php
	// check if session active
	session_start();
	if (!isset($_SESSION['SESS_MEMBER_ID'])) {
		header("location:login.php");	
	}
	

	// print header 
	if (!strrpos($_SERVER['HTTP_USER_AGENT'], "iPad")){
	echo "
	<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/strict.dtd\">
	<html style=\"border:0px; padding:0px; background:#1f2e3d;\" xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
	<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" />
	";
	} else {
	echo "<html><head>
	<meta name=\"viewport\" content=\"maximum-scale=1.0, minimum-scale=1.0, initial-scale=1.0, user-scalable=0;\"/>
	<meta name=\"apple-mobile-web-app-capable\" content=\"yes\">
	<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\">
	";
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
	
		echo "<script type=\"text/javascript\">
			function clickedAnswer(answer){
			  document.getElementById('mult_ans').value=answer;
			  var imgs = document.getElementsByTagName('img');
			  for (var i = 0; i < imgs.length-1; i++) {
				imgs[i].src='Images/Survey/radioBtn.png';
			  }
			}
		      </script>";
	echo "</head>";
	echo "<body class=\"survey\">";

	// logic
	
	require_once '../myHubServer/includes/Link.php';
	require_once '../myHubServer/includes/User.php';

	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$db->connect();

	$link_id = (int)$_GET['link_id'];

	$link = new Link($link_id);
	$user = new User((int)$_SESSION['SESS_MEMBER_ID']);
	// curr_q of question to display
	$report_q = (int)$_GET['report_q'];
	$curr_q = $report_q + 1;
	$start = false;
		
    if ($curr_q == 0 || (!isset($_SESSION['MULTI_QUESTIONS_ARRAY']) && !isset($_SESSION['FREE_QUESTION_ARRAY']) )) {
	// survey start: store array of qestion id's in session
	$location_id = $user->getLocation();
	$businesses_ids = $user->getBusinessIds();

        $free_questions = $link->fetchMyFreeSurvey($location_id, $businesses_ids);
        $_SESSION['FREE_QUESTIONS_ARRAY'] = $free_questions;
        $multi_questions = $link->fetchMyMultiSurvey($location_id, $businesses_ids);
        $_SESSION['MULTI_QUESTIONS_ARRAY'] = $multi_questions;
                
        $curr_q = 1;
	$start = true;
	}

        $free = count($_SESSION['FREE_QUESTIONS_ARRAY']);
        $multi = count($_SESSION['MULTI_QUESTIONS_ARRAY']);
        $all = $free + $multi;

	if (isset($_GET['answer']))
		$_answer = $_GET['answer']; 

	//logging
	if (!$start){ // nothing to report at beginning
	    if ($report_q <= $multi){
		$link->logSurvey($user->getId(), $link_id, "multi", $_SESSION['MULTI_QUESTIONS_ARRAY'][$report_q - 1], $_answer);
	    } else {
		$link->logSurvey($user->getId(), $link_id, "free", $_SESSION['FREE_QUESTIONS_ARRAY'][$report_q - $multi - 1], $_answer);
	    }
	}
	
	// did we reach the end of the survey?
	$finish = false;
	if ($curr_q > $all) {
		$finish = true;
	}
	
	// header elements
	echo "<div id=\"surv_title\">".$link->getName()." Survey</div>";
	// the following is weird, made this way in order to work in IE
	echo "<a href=\"content.php?link_id=".$link_id."\"><input id=\"surv_back\" type=\"button\" value=\"".$link->getName()."\" onclick=\"location.href='content.php?link_id=".$link_id."'\"></a>";
	//echo "<div id=\"surv_back\">".$link->getName()."</div>";
	echo "<div id=\"surv_text\"></div>";

      if (!$finish){
	// question to display
	if ($curr_q <= $multi) {
		// present a multiple-choice question
		$question_id = $_SESSION['MULTI_QUESTIONS_ARRAY'][$curr_q - 1];
		$_answers = $link->fetchQuestionAnswers($question_id);
		$_answers = $_answers[0];
		$i=0;
		foreach ($_answers as $ans){
		    $i+=1;
		    $tmp = "answer".$i;
		    if ($ans)
			$answers["$tmp"] = $ans;
		}
		$question_text = $link->fetchMultiQuestionText($question_id);
	} else {
		$question_id = $_SESSION['FREE_QUESTIONS_ARRAY'][$curr_q - $multi - 1];
		//echo "fetching question no ".$question_id;
		$question_text = $link->fetchFreeQuestionText($question_id);
	}
		
	// build table to present question
	echo "<div id=\"surv_bodydiv\">";
	echo "<table id=\"surv_table\">
	<tr>
		<td id=\"surv_question\" colspan=2>$question_text</td></tr>";
	
	if (isset($answers)) {
	    foreach ($answers as $key => $ans) {
		$im_id = "im".$key;
		echo "<tr onclick=\"clickedAnswer('$ans');
                document.getElementById('$im_id').src='Images/Survey/radioBtnHit.png'\">
		<td style=\"width:30px; padding:5px; vertical-align:middle;\">
			<img id=\"$im_id\" src=\"Images/Survey/radioBtn.png\"></td>
		<td style=\"padding:5px; vertical-align:middle;\"><div class=\"surv_mult_answer\">$ans</div></td>
		</tr>";
	    }
	     echo "</table></div>
	     <form id=\"surv_cont_form\" name=\"goon\" action=\"\">
		<input type=\"hidden\" name=\"link_id\" value=\"$link_id\">
		<input type=\"hidden\" name=\"report_q\" value=\"$curr_q\">
		<input id=\"mult_ans\" type=\"hidden\" name=\"answer\" value=\"not_selected\">
	     "; 
	} else {
	    echo "</table>
		 <form id=\"surv_cont_form\" name=\"goon\" action=\"\">
		 <textarea class=\"surv_free_answer\" name=\"answer\"></textarea>
		 <input type=\"hidden\" name=\"link_id\" value=\"$link_id\">
		 <input type=\"hidden\" name=\"report_q\" value=\"$curr_q\">
		 </div>";
		
        	if (strrpos($_SERVER['HTTP_USER_AGENT'], "iPad") > 0){
		 echo "<div style=\"height:12px;\"></div>"; // position fix on mobile safari
		}
	    // continue button
		}

	echo "</form>";
        	if (strrpos($_SERVER['HTTP_USER_AGENT'], "iPad") > 0){
		 echo "<img id=\"surv_cont\" src=\"Images/Survey/continueBtnHit.png\" 
       		 ontouchstart=\"document.getElementById('surv_cont').src='url(Images/Survey/continueBtn.png)';\" 
	         onclick=\"document.goon.submit();\" 
        	 ontouchend=\"document.getElementById('surv_cont').src='url(Images/Survey/continueBtnHit.png)';\"> 
		 ";
		} else {
	         echo "<div id=\"surv_cont\" 
		 style=\"background-image:url(Images/Survey/continueBtnHit.png); width:253px; height:60px;\" 
        	 onmousedown=\"document.getElementById('surv_cont').style.backgroundImage='url(Images/Survey/continueBtn.png)';\" 
	         onclick=\"document.goon.submit();\" 
	         onmouseup=\"document.getElementById('surv_cont').style.backgroundImage='url(Images/Survey/continueBtnHit.png)';\"></div>
		 ";
		}
      } else {
	echo "<div id=\"surv_bodydiv\"><div id=\"surv_finished\">Thank you for completing the survey!</div></div>";
		
		echo "<form name=\"finish\" action=\"content.php\">
		<input type=\"hidden\" name=\"link_id\" value=\"$link_id\">
		</form>";
        	if (strrpos($_SERVER['HTTP_USER_AGENT'], "iPad") > 0){
		 echo "<img id=\"surv_finish\" src=\"Images/Survey/finishedBtn.png\" 
       		 ontouchstart=\"document.getElementById('surv_finish').src='url(Images/Survey/finishedBtn-hit.png)';\" 
	         onclick=\"document.finish.submit();\" 
        	 ontouchend=\"document.getElementById('surv_finish').src='url(Images/Survey/finishedBtn.png)';\"> 
		 ";
		} else {
	         echo "<div id=\"surv_finish\" 
		 style=\"background-image:url(Images/Survey/finishedBtn.png); width:253px; height:60px;\" 
        	 onmousedown=\"document.getElementById('surv_finish').style.backgroundImage='url(Images/Survey/finishedBtn-hit.png)';\" 
	         onclick=\"document.finish.submit();\" 
	         onmouseup=\"document.getElementById('surv_finish').style.backgroundImage='url(Images/Survey/finishedBtn.png)';\"></div>
		 ";
		}
      }

	// dots
	//$left = 512 - (16*($all+1))/2;
	//echo "<table id=\"dots\" style=\"padding:2px; position:relative; left:".$left."px;\"><tr>";
	echo "<table id=\"dots\" style=\"padding:2px; margin-left:auto; margin-right:auto;\"><tr>";
	for ($i=0; $i<=$all; $i++) {
	    $background = "";
	    if ($i == $curr_q - 1){
		$background = "style=\"background:#eaffff\"";
	    }
	    echo "<td><div id=\"surv_dot\" ".$background."></div></td>";
	}
	echo "</tr></table>";
	
	// general messages
	echo "<div id=\"surv_general_messages\">";
        foreach ($user->getGeneralMessage() as $key => $entry) {
            echo $entry['message'];
            echo " ";
        }
        echo "</div>";

	// keep MyHub button from opening a browser window
	echo "<script type=\"text/javascript\">
                var a=document.getElementsByTagName(\"a\");
                for(var i=0;i<a.length;i++)
                {               
                    a[i].onclick=function()
                    {
                        window.location=this.getAttribute(\"href\");
                        return false
                    }
                }
	      </script>";

	echo "</body></html>";

?>
