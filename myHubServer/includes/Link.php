<?php

require_once 'php_mysql_class/config.inc.php';
require_once 'php_mysql_class/Database.singleton.php';

/**
 * Usage:
 * $baba = new Link($id);
 * <$id> is db link's id
 * or:
 * $booba = new Link($array);
 * <$array> is an assosiative array of either the whole row from the database (i.e. from links),
 *          or for a new link, at least: ('url'=>'http://www.mosheufnik.org', 'imgpath'=>'moshe.gif', 'description'=>'handsome', 'name'=>'Moshe Ufnik')
 */
class Link {
    private $id;
    private $url;
    private $imgpath;
    private $imgbutton;
    private $description;
    private $name;
    private $active;

    private $linkUpdate=array(); // assosiative array for update commits
    private $messageUpdate=array(); // array for messages update commits
    private $surveyFreeUpdate=array();
    private $surveyMultiUpdate=array();
    private $businessUpdate=array();
    private $locationUpdate=array();

    // constructor
    function __construct($param) {
        $this->messageUpdate = array();
	$this->surveyFreeUpdate = array();
	$this->surveyMultiUpdate = array();
        if (is_int($param)) {
                $this->loadLinkDataFromDb($param);
                $this->messageUpdate = $this->getMessages();
		$this->surveyFreeUpdate = $this->getFreeSurvey();
		$this->surveyMultiUpdate = $this->getMultiSurvey();
        }
        else if (is_array($param))
            $this->fillLinkDataFromParam($param);
        else
            echo "<pre>bad link construct\n</pre>"; //TODO rais exception or something
        
        $this->commit();
    }


    /***
     * private functions
     ***/
    // setters
    private function setActive() {
        if (! $this->active) {
                $this->active = TRUE;
                $this->linkUpdate['active']=1;
        }
    }
    private function setInactive() {
        $this->active = FALSE;
        $this->linkUpdate['active']=0;
    }
    private function setId($id) {
        $this->id=$id;
    }

    // constructor helpers
    private function fillLinkDataFromParam($row) {
	if (is_array($row)){
          foreach ($row as $key => $value)
                switch ($key) {
                    case 'id': $this->setId($value); break;
                    case 'url': $this->setUrl($value); break;
                    case 'imgpath': $this->setImgpath($value); break;
		    case 'imgbutton': $this->setImgbutton($value); break;
                    case 'description': $this->setDescription($value); break;
                    case 'name': $this->setName($value); break;
                    case 'active': $value ? $this->setActive() : $this->setInactive(); break;
                }
        
          if ($this->id )
              $this->linkUpdate=array(); // reset commit updates array
          else
              $this->setActive (); // handle new link
	} else {
	echo "an entry with the passed link id does not exist in table links";
	}	
    }
    private function loadLinkDataFromDb($id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $this->fillLinkDataFromParam($db->query_first("SELECT * FROM links WHERE id='".$id."'"));
    }


    /***
     * public functions
     ***/
    // getters
    function getId() {
        return $this->id;
    }
    function getUrl() {
        return $this->url;
    }
    function getImgpath() {
        return $this->imgpath;
    }
    function getImgbutton() {
	return $this->imgbutton;
    }
    function getDescription() {
        return $this->description;
    }
    function getName() {
        return $this->name;
    }
    function getFreeSurveyLocations($survey_id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT locations.id, locations.location FROM locations, surveys_free_loc
               WHERE surveys_free_loc.locations_id=locations.id AND surveys_free_loc.surveys_free_id=".$survey_id) as $value)
            $return[$value['id']] = $value['location'];
        return $return;
    }
    function getFreeSurveyBusinesses($survey_id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT business.id, business.business FROM business, surveys_free_bus
               WHERE surveys_free_bus.business_id=business.id AND surveys_free_bus.surveys_free_id=".$survey_id) as $value) {
            $return[$value['id']] = $value['business'];
        }
        return $return;
    }
    function getFreeSurvey() {
        if(!isset($this->id)) {
            $return = array();
            return $return;
        }
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        $businesses = array();
        $locations = array();
        foreach ($db->fetch_array("SELECT id, question FROM surveys_free
	            WHERE surveys_free.links_id=".$this->id) as $survey){

            $businesses = $this->getFreeSurveyBusinesses($survey['id']);
            $locations  = $this->getFreeSurveyLocations($survey['id']);

            $return[$survey['id']]=array ('question' => $survey['question'], 'surveys_id' => $survey['id'],
                                              'businesses' => $businesses, 'locations' => $locations);  
        }
        return $return;
    }
    function getMultiSurveyLocations($survey_id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT locations.id, locations.location FROM locations, surveys_multi_loc
               WHERE surveys_multi_loc.locations_id=locations.id AND surveys_multi_loc.surveys_multi_id=".$survey_id) as $value)
            $return[$value['id']] = $value['location'];
        return $return;
    }
    function getMultiSurveyBusinesses($survey_id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT business.id, business.business FROM business, surveys_multi_bus
               WHERE surveys_multi_bus.business_id=business.id AND surveys_multi_bus.surveys_multi_id=".$survey_id) as $value) {
            $return[$value['id']] = $value['business'];
        }
        return $return;
    }
    function getMultiSurvey() {
        if(!isset($this->id)) {
            $return = array();
            return $return;
        }
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        $businesses = array();
        $locations = array();
        foreach ($db->fetch_array("SELECT id, question FROM surveys_multi
	            WHERE surveys_multi.links_id=".$this->id) as $survey){
	
            $businesses = $this->getMultiSurveyBusinesses($survey['id']);
            $locations  = $this->getMultiSurveyLocations($survey['id']);
	    $answers = $this->fetchQuestionAnswers($survey['id']);

            $return[$survey['id']]=array ('question' => $survey['question'], 'surveys_id' => $survey['id'],
                                              'businesses' => $businesses, 'locations' => $locations, 'answers' => $answers[0]);  
        }
        return $return;
    }
    function getMessages() {
        if(!isset($this->id)) {
            $return = array();
            return $return;
        }
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        $businesses = array();
        $locations = array();
        foreach ($db->fetch_array("SELECT messages.id, messages.message 
	            FROM messages, linkmessages 
	            WHERE messages.id=linkmessages.messages_id
	                AND linkmessages.links_id=".$this->id) as $message){

            $businesses = $this->getMsgBusinesses($message['id']);
            $locations  = $this->getMsgLocations($message['id']);

            $return[$message['id']]=array ('message' => $message['message'], 'messages_id' => $message['id'],
                                              'businesses' => $businesses, 'locations' => $locations);  
        }
        return $return;
    }
    function getMsgBusinesses($msg_id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT business.id, business.business FROM business, linkmessbus
                WHERE linkmessbus.business_id=business.id AND linkmessbus.messages_id=".$msg_id) as $value) {
            $return[$value['id']] = $value['business'];
        }
        return $return;
    }
    function getMsgLocations($msg_id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT locations.id, locations.location FROM locations, linkmessloc
                    WHERE linkmessloc.locations_id=locations.id AND linkmessloc.messages_id=".$msg_id) as $value)
            $return[$value['id']] = $value['location'];
        return $return;
    }
    function getMyMessages($_location, $_businesses) {
	// returns only messages that have the passed location
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);

	// all messages of the link
	$link_messages = array();
        foreach ($db->fetch_array("SELECT messages_id FROM linkmessages
                    WHERE links_id = ".$this->id) as $value)
            $link_messages[] = $value['messages_id'];
	
	
	// all messages with passed location
	$location_messages = array();
	foreach ($db->fetch_array("SELECT messages_id FROM linkmessloc 
	WHERE locations_id=".$_location) as $value) {
		$location_messages[] = $value['messages_id'];
	}	


	// all messages belonging to  at least one of the specified businesses
	$businesses_messages = array();
	foreach ($_businesses as $biz) {
		foreach ($db->fetch_array("SELECT messages_id FROM linkmessbus 
		WHERE business_id=".$biz) as $message) {
			$businesses_messages[] = $message['messages_id'];
		}
	}
	
	// the intersection
	$messages_to_return = array_intersect($link_messages, array_merge($location_messages, $businesses_messages));
	
	$return = array();
	foreach ($messages_to_return as $index => $id) {
		$tmp = $db->fetch_array("SELECT message FROM messages WHERE id=".$id);
		$tmp2 = $tmp['0'];
		$return[] = $tmp2['message'];
	}
	return $return;
    }
    function getBusinesses(){
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$return = array();
	foreach ($db->fetch_array("SELECT business.id, business.business 
	FROM business, linkbusiness
	WHERE business.id=linkbusiness.business_id
	AND linkbusiness.links_id='".$this->id."'") as $business){
		$return[$business['id']]=$business['business'];
	}
	return $return;
    }
    function getLocations(){
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$return = array();
	foreach ($db->fetch_array("SELECT locations.id, locations.location 
	FROM locations, linklocation 
	WHERE locations.id=linklocation.locations_id 
	AND linklocation.links_id='".$this->id."'") as $location){
		$return[$location['id']]=$location['location'];
	}
	return $return;
    }
    function getAllBusinesses(){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT id, business FROM business") as $item){
                $return[$item['id']]=$item['business'];
        }
        return $return;
    }
    function getAllMessages(){
    $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT id, message FROM messages") as $item){
                $return[$item['id']]=$item['message'];
        }
        return $return;
    }
    function getAllLocations(){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $return = array();
        foreach ($db->fetch_array("SELECT id, location FROM locations") as $item){
                $return[$item['id']]=$item['location'];
        }
        return $return;
    }
    function fetchMyFreeSurvey($_location, $_businesses) {
	// returns an array containing id's of all the free questions in the survey
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);

	$all_link_questions = array();
	// all questions for the given link
	foreach ($db->fetch_array("SELECT id FROM surveys_free WHERE links_id=".$this->id) as $index => $question) {
		$all_link_questions[] = $question['id'];
	}

	$all_location_questions = array();
	// all questions for the specified location
	foreach ($db->fetch_array("SELECT surveys_free_id FROM surveys_free_loc 
	WHERE locations_id=".$_location) as $index => $question){
		$all_location_questions[] = $question['surveys_free_id'];
	}
	
	$all_businesses_questions = array();
	// all questions for at least one of the specified businesses
	foreach ($_businesses as $biz) {
		foreach ($db->fetch_array("SELECT surveys_free_id FROM surveys_free_bus 
		WHERE business_id=".$biz) as $question) {
			$all_businesses_questions[] = $question['surveys_free_id'];
		}
	}
	// contains all the right values, but keying not neat
	$return_tmp = array_intersect($all_link_questions, array_merge($all_location_questions, $all_businesses_questions));
	$return = array();
	foreach ($return_tmp as $key => $value){
		$return[] = $value;
	}
	return $return;
    }
    function fetchMyMultiSurvey($_location, $_businesses) {
	// returns an array containing id's of all the multiple choice questions in the survey
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);

	$all_link_questions = array();
	// all questions for the given link
	foreach ($db->fetch_array("SELECT id FROM surveys_multi WHERE links_id=".$this->id) as $index => $question) {
		$all_link_questions[] = $question['id'];
	}

	$all_location_questions = array();
	// all questions for the specified location
	foreach ($db->fetch_array("SELECT surveys_multi_id FROM surveys_multi_loc 
	WHERE locations_id=".$_location) as $index => $question){
		$all_location_questions[] = $question['surveys_multi_id'];
	}
	
	$all_businesses_questions = array();
	// all questions for at least one of the specified businesses
	foreach ($_businesses as $biz) {
		foreach ($db->fetch_array("SELECT surveys_multi_id FROM surveys_multi_bus 
		WHERE business_id=".$biz) as $question) {
			$all_businesses_questions[] = $question['surveys_multi_id'];
		}
	}

	$intersection =  array_intersect($all_link_questions, array_merge($all_location_questions, $all_businesses_questions));
	$return = array();
	foreach ($intersection as $key => $value){
	    $return[] = $value;
	}
	return $return;
    }
    function fetchMySurvey($_location, $_businesses) {
	// location and businesses should come as id's
	return array_merge($this->fetchMyFreeSurvey($_location, $_businesses), $this->fetchMyMultiSurvey($_location, $_businesses));
    }
    function fetchMultiQuestionText($question_id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$return =  $db->fetch_array("SELECT question FROM surveys_multi WHERE id= ".$question_id);
	return $return[0]['question'];
    }
    function fetchFreeQuestionText($question_id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$return = $db->fetch_array("SELECT question FROM surveys_free WHERE id= ".$question_id);
	if (isset($return[0])) {
	  return $return[0]['question'];
	} else {
	  return "";
	}
    }
    function fetchQuestionAnswers($_survey_question_id) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	try {
	  return $db->fetch_array("SELECT answer1, answer2, answer3, answer4 FROM surveys_multi
	  WHERE id=".$_survey_question_id);
	} catch (Exception $e) {
	  return array();
	}
    }
    /* Usage getters */
    
	function getUsageInfo(){
		$usage_array = array ('name' => $this->getName(), 
							  'last_click' => $this->getLastClick(), 
							  'email' => $this->getEmail(), 
							  'total_clicks' => $this->getTotalClicks(), 
							  'last_month_clicks' => $this->getLastMonthClicks());
		return $usage_array;
	}
	function getLastClick() {
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		foreach ($db->fetch_array("SELECT users_id, timestamp FROM usagelinks 
		WHERE links_id='".$this->id."'") as $timestamp){
			$timestamps[] = $timestamp['timestamp'];
		}
		if (isset($timestamps))
			return max($timestamps);
		else return '0000-00-00 00:00:00';
	}
	function getEmail(){
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$last_click = $this->getLastClick();
		$last_clicker_id_arr = $db->fetch_array("SELECT users_id FROM usagelinks WHERE timestamp='".$last_click."'");
		$last_clicker_email = "";
		if (isset($last_clicker_id_arr[0])){
			$last_clicker_id = $last_clicker_id_arr[0]['users_id'];
			$last_clicker_email_arr = $db->fetch_array("SELECT username FROM users WHERE id='".$last_clicker_id."'");
			$last_clicker_email = $last_clicker_email_arr[0]['username'];
		}
		return $last_clicker_email;
	}
    function getTotalClicks() {
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		foreach ($db->fetch_array("SELECT id FROM usagelinks WHERE links_id='".$this->id."'") as $hit){
			$count[] = $hit;
		}
		if (isset($count))
			return count($count);
		else
			return 0;
	}
	function getlastMonthClicks() {
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$thirty_days_ago = date("Y-m-d H:i:s", strtotime("-30 days"));
		foreach ($db->fetch_array("SELECT id FROM usagelinks WHERE links_id='".$this->id."'
		AND timestamp <= NOW()
		AND timestamp >='".$thirty_days_ago."'") as $hit){
			$count[] = $hit;
		}
		if (isset($count))
			return count($count);
		else
			return 0;
	}
	

   /* Survey getters*/
    
	function getSurveyInfo(){
		$usage_array = array ('name' => $this->getName(), 
							  'last_user_click' => $this->getSurveyLastUserClick(), 
							  //'email' => $this->getSurveyUserEmail(), 
							  'total_clicks' => $this->getSurveyTotalClicks(), 
							  'last_month_clicks' => $this->getSurveyLastMonthClicks());
		return $usage_array;
	}	
	function getSurveyLastUserClick() {
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$maxIdArr=$db->fetch_array("select max(id) from surveys_report WHERE links_id='".$this->id."' " ) ;
		$maxId=$maxIdArr[0]['max(id)'];
		$userclick=$db->fetch_array("SELECT timestamp,users_id FROM surveys_report WHERE id='".$maxId."' " ) ;

		if (isset($userclick) && count($userclick)!= 0){
			if($userclick[0]['users_id']!='' && $userclick[0]['timestamp']!=''){
				return $userclick;
			}
			elseif($userclick[0]['users_id']!='' )
				return 'N/A';
			else
				return 'NOUSER';
			}
		else
			return 'NOUSER';
	}
	
	function getSurveyUserEmail(){
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$last_click = $this->getSurveyLastClick();
		$last_clicker_id_arr = $db->fetch_array("SELECT users_id FROM surveys_report WHERE timestamp='".$last_click."'");
		$last_clicker_email = "";
		if (isset($last_clicker_id_arr[0])){
			$last_clicker_id = $last_clicker_id_arr[0]['users_id'];
			$last_clicker_email_arr = $db->fetch_array("SELECT username FROM users WHERE id='".$last_clicker_id."'");
			$last_clicker_email = $last_clicker_email_arr[0]['username'];
		}
		return $last_clicker_email;
	}
    function getSurveyTotalClicks() {
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		foreach ($db->fetch_array("SELECT id FROM surveys_report WHERE links_id='".$this->id."'") as $hit){
			$count[] = $hit;
		}
		if (isset($count))
			return count($count);
		else
			return 0;
	}
	function getSurveyLastMonthClicks() {
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$thirty_days_ago = date("Y-m-d H:i:s", strtotime("-30 days"));
		foreach ($db->fetch_array("SELECT id FROM surveys_report WHERE links_id='".$this->id."'
		AND timestamp <= NOW()
		AND timestamp >='".$thirty_days_ago."'") as $hit){
			$count[] = $hit;
		}
		if (isset($count))
			return count($count);
		else
			return 0;
	}	
	
	
    // setters
    function setUrl($url) {
        $this->url = $url;
        $this->linkUpdate['url']=$this->url;
    }
    function setImgpath($imgpath) {
        $this->imgpath = $imgpath;
        $this->linkUpdate['imgpath']=$this->imgpath;
    }
    function setImgbutton($imgbutton) {
        $this->imgbutton = $imgbutton;
        $this->linkUpdate['imgbutton']=$this->imgbutton;
    }
    function setName($name) {
        $this->name = $name;
        $this->linkUpdate['name']=$this->name;
    }
    function setDescription($description) {
        $this->description = $description;
        $this->linkUpdate['description']=$this->description;
    }

    // actions
    function delete() {
        $this->setInactive();
    }
    function updateMessages ($messages){
        $this->messageUpdate = $messages;
    }
    function updateSurveyFree ($free_questions){
	$this->surveyFreeUpdate = $free_questions;
    }
    function updateSurveyMulti ($multi_questions){
	$this->surveyMultiUpdate = $multi_questions;
    }
    function addLocation ($location){
	$this->locationUpdate[$location] = TRUE;
    }
    function removeLocation ($location){
	$this->locationUpdate[$location] = FALSE;
    }
    function updateLocations($locations){
	foreach ($this->getAllLocations() as $id => $name){
		if (in_array($name, $locations)){
			$this->addLocation($id);
		} else {
			$this->removeLocation($id);
		}
	}
    }
    function addBusiness($business) {
        $this->businessUpdate[$business]=TRUE;
    }
    function removeBusiness($business) {
        $this->businessUpdate[$business]=FALSE;
    }
    function updateBusinesses($businesses){
    // sets elements in businessUpdate array to true iff their name is found in argument
	foreach ($this->getAllBusinesses() as $id => $name){
		if (in_array($name, $businesses)){
			$this->addBusiness($id);
		} else {
			$this->removeBusiness($id);
		}
	}
    }
    function logUsage($user_id){
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$row = array('users_id' => $user_id, 'links_id' => $this->id, 'timestamp' => date('Y-m-d H:i:s.u'));
	$db->insert("usagelinks", $row);
    }
    function logSurveyMulti($user_id, $question_id, $link_id, $answer) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$row = array('users_id'=>$user_id, 'surveys_multi_id'=>$question_id, 'links_id' => $link_id, 'answer'=> $answer, 'timestamp'=>date('Y-m-d H:i:s'));
	$db->insert("surveys_multi_report", $row);	
    }
    function logSurveyFree($user_id, $question_id, $link_id, $answer) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$row = array('users_id'=>$user_id, 'surveys_free_id'=>$question_id, 'links_id' => $link_id, 'answer'=>$answer, 'timestamp'=>date('Y-m-d H:i:s'));
	$db->insert("surveys_free_report", $row);	
    }
    function logSurvey($user_id, $link_id, $question_type, $question_id, $answer) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$row = array('users_id'=>$user_id, 'links_id' => $link_id, 'survey_type'=>$question_type, 'surveys_id'=>$question_id, 'answer'=>$answer, 'timestamp'=>date('Y-m-d H:i:s'));
	$db->insert("surveys_report", $row);	
    }
    static function getMaxMessagesIndex() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $res=$db->query_first("SELECT MAX(id) FROM messages");
        return $res['MAX(id)'];
    }
    static function getMaxFreeSurveyIndex() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $res=$db->query_first("SELECT MAX(id) FROM surveys_free");
        return $res['MAX(id)'];
    }
    static function getMaxMultiSurveyIndex() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $res=$db->query_first("SELECT MAX(id) FROM surveys_multi");
        return $res['MAX(id)'];
    }
    function debug_print() {
        bt_debug2("<br>--------<br>Link id   = ".$this->getId()."<br>");
        bt_debug2("Link name = ".$this->getName()."<br>");
        bt_debug2("Link url  = ".$this->getUrl()."<br>");
        bt_debug2("Link businesses = ");            bt_debug2($this->getBusinesses());
        bt_debug2("<br>Link locations = ");         bt_debug2($this->getLocations());
        bt_debug2("<br>Link messages = ");          bt_debug2($this->getMessages());
        bt_debug2("<br>Link messageUpdate = ");     bt_debug2($this->messageUpdate);
        bt_debug2("<br>--------<br>");
    }
    function commit() {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        if ($this->linkUpdate) {
            if (isset($this->id))
                $db->update('links', $this->linkUpdate, "id='".$this->id."'");
            else
                $this->id=$db->insert('links', $this->linkUpdate);

	    $this->linkUpdate = array();
        }
       $db->query("DELETE FROM linkmessages WHERE links_id=".$this->id);
        // FIXME $db->query("DELETE FROM linkmessbus  WHERE messages_id=".$msg_id);
        // FIXME $db->query("DELETE FROM linkmessloc  WHERE links_id=".$this->id);
        if ($this->messageUpdate) { // FIXME: message businesses, locations
            foreach ($this->messageUpdate as $index => $value) {
                $msg_id=$value['messages_id'];
                if($msg_id > Link::getMaxMessagesIndex()) {
                    $msg_id=$db->insert('messages', array('message'=>$value['message']));
                }
                else {
                    $db->query("UPDATE messages SET message='".$value['message']."' WHERE id='".$msg_id."'");
                }
                $linkmsg_id1=$db->insert('linkmessages', array('messages_id'=>$msg_id, 'links_id'=>$this->id));
                $db->query("DELETE FROM linkmessbus  WHERE messages_id=".$msg_id);
                $db->query("DELETE FROM linkmessloc  WHERE messages_id=".$msg_id);
                foreach($value['businesses'] as $ind2 => $val2) {
                        $db->insert('linkmessbus', array('business_id' => $ind2, 'messages_id' => $msg_id));
                }
                foreach($value['locations'] as $ind2 => $val2)
                    $db->insert('linkmessloc', array('locations_id' => $ind2, 'messages_id' => $msg_id));
            }
            $this->messageUpdate=array();
        }
		$surveysFreeToRemoveBus=$db->fetch_array("SELECT id from surveys_free where links_id=".$this->id);
		//echo'<br>this->id = '.$this->id;
		//print_r($surveysFreeToRemove);
		
		foreach ($surveysFreeToRemoveBus as $index => $value) {
				$db->query("DELETE FROM surveys_free_bus  WHERE surveys_free_id=".$value['id']);
				$db->query("DELETE FROM surveys_free_loc  WHERE surveys_free_id=".$value['id']);
				}		
				
		$db_survey_free_keys = array();
		foreach ($db->fetch_array("SELECT id FROM surveys_free WHERE links_id=".$this->id) as $index => $value){
		$db_survey_free_keys[$value['id']] = $value['id'];
		}
				
		$form_survey_free_keys = array();
		foreach ($this->surveyFreeUpdate as $index => $value) {						
				$form_survey_free_keys[$index] = $index;
		}						
		
		$surveysFreeToRemove=array_diff($db_survey_free_keys , $form_survey_free_keys);		
		if ($surveysFreeToRemove) {			
			foreach ($surveysFreeToRemove as $index => $value) {
				echo'<br>delete index='.$index;
				$db->query("DELETE FROM surveys_free WHERE id=".$index);
			}
		}
		
		$surveyFreeUpdate=$this->surveyFreeUpdate;
        if ($this->surveyFreeUpdate) {			
			foreach ($this->surveyFreeUpdate as $index => $value) {				
				$surveys_id=$value['surveys_id'];
				if(in_array($index, array_diff($form_survey_free_keys, $db_survey_free_keys)))	{					
					$surveys_id=$db->insert('surveys_free', array('question'=>$value['question'], 'links_id'=>$this->id, 'active'=>1));
				}
				else{
					 $db->query("UPDATE surveys_free SET question='".$value['question']."' WHERE id='".$surveys_id."'");
				
				}
				foreach($value['businesses'] as $ind1 => $val1) {
					$db->insert('surveys_free_bus', array('business_id' => $ind1, 'surveys_free_id' => $surveys_id));
					}
				foreach($value['locations'] as $ind2 => $val2)
					$db->insert('surveys_free_loc', array('locations_id' => $ind2, 'surveys_free_id' => $surveys_id));
			}
			
            $this->surveyFreeUpdate=array();
		}
		
		$surveysMultiToRemoveBus=$db->fetch_array("SELECT id from surveys_multi where links_id=".$this->id);
		foreach ($surveysMultiToRemoveBus as $index => $value) {
				$db->query("DELETE FROM surveys_multi_bus  WHERE surveys_multi_id=".$value['id']);
				$db->query("DELETE FROM surveys_multi_loc  WHERE surveys_multi_id=".$value['id']);
				}
		$db_survey_multi_keys = array();		
		foreach ($db->fetch_array("SELECT id FROM surveys_multi WHERE links_id=".$this->id) as $index => $value){
			$db_survey_multi_keys[$value['id']] = $value['id'];
		}	
		
		$form_survey_multi_keys = array();
		foreach ($this->surveyMultiUpdate as $index => $value) {						
				$form_survey_multi_keys[$index] = $index;
		}				
		
		$surveysMultiToRemove=array_diff($db_survey_multi_keys , $form_survey_multi_keys);
		if ($surveysMultiToRemove) {			
			foreach ($surveysMultiToRemove as $index => $value) {
				echo'<br>delete index='.$index;
				$db->query("DELETE FROM surveys_multi WHERE id=".$index);
			}
		}

        if ($this->surveyMultiUpdate) {
            foreach ($this->surveyMultiUpdate as $index => $value) {
                $surveys_id=$value['surveys_id'];
				$answers = $value['answers'];
				if(in_array($index, array_diff($form_survey_multi_keys, $db_survey_multi_keys)))	{					
					$surveys_id=$db->insert('surveys_multi', array('question'=>$value['question'], 'links_id'=>$this->id, 'answer1'=>$answers['answer1'] ,'answer2'=>$answers['answer2'],'answer3'=>$answers['answer3'],'answer4'=>$answers['answer4'], 'active'=>1));
				}
				else{
					 $db->query("UPDATE surveys_multi SET question='".$value['question']."', links_id='".$this->id."', answer1='".$answers['answer1']."' , answer2='".$answers['answer2']."', answer3='".$answers['answer3']."', answer4='".$answers['answer4']."', active=1 WHERE id='".$surveys_id."'");				
				}
				
                foreach($value['businesses'] as $ind1 => $val1) {
                        $db->insert('surveys_multi_bus', array('business_id' => $ind1, 'surveys_multi_id' => $surveys_id));
                }
                foreach($value['locations'] as $ind2 => $val2)
                    $db->insert('surveys_multi_loc', array('locations_id' => $ind2, 'surveys_multi_id' => $surveys_id));
            }
            $this->surveyMultiUpdate=array();
	}
        if ($this->businessUpdate) {
            foreach ($this->businessUpdate as $index => $active){
                if ($active)
                    $db->insert('linkbusiness', array('business_id'=>$index, 'links_id'=>  $this->id));
                else
                    $db->query("DELETE FROM linkbusiness WHERE links_id=".$this->id." AND business_id=".$index);
            }
            $this->businessUpdate=array();
        }
        if ($this->locationUpdate) {
            foreach ($this->locationUpdate as $index => $active){
                if ($active)
                    $db->insert('linklocation', array('locations_id'=>$index, 'links_id'=>  $this->id));
                else
                    $db->query("DELETE FROM linklocation WHERE links_id=".$this->id." AND locations_id=".$index);
            }
            $this->locationUpdate=array();
        }

    }
	
	public static function getActiveLinks(){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$result=$db->fetch_array("SELECT * FROM links WHERE active=true");
		
		return $result;
	}	
	
	public static function getAllLinks(){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$result=$db->fetch_array("SELECT * FROM links ");
		
		return $result;
	}	
	
	
	public static function getManagerLinks($query){	
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$result=$db->fetch_array($query);		
		return $result;
	}	
	
	public static function getLinkNameById($userId){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);	
	$result=$db->query("SELECT name FROM links WHERE id='".$userId."'");
	$row = mysql_fetch_array($result);
	return $row['name'];;
	}		
	
	public static function getQuestionBySurveyId($surveyId,$type){
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	if($type=='free')	
		$result=$db->query("SELECT question FROM surveys_free WHERE id='".$surveyId."'");
	else
		$result=$db->query("SELECT question FROM surveys_multi WHERE id='".$surveyId."'");
	$row = mysql_fetch_array($result);
	
	return $row['question'];;
	}

	
}
?>
