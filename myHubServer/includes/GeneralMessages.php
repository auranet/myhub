<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Messages
 *
 * @author thewizard
 */
class GeneralMessages {
    /*public static function getMessagesBy($order, $offset=1) {
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        $result=array();
        foreach ($db->fetch_array("SELECT * FROM users") as $row)
            $result[array_splice($row,'id',1)]=$row;
        return $result;
    }*/
    public static function getGeneralMessagesFor($param) {
        foreach ($param as $key => $value)
          switch ($key) {
            case "business": $Business = $value; break;
            case "location": $Location = $value; break;
          }
          
        if (!is_array($Business) || !is_numeric($Location))
          throw new Exception ("Illigal call");

        $bNeedOr=FALSE;
        $businessQuery="";
        $return=array();

        foreach (array_keys($Business) as $businessid) {
            if ($bNeedOr)
                $businessQuery=$businessQuery." OR ";
            $businessQuery=$businessQuery."genmessagebus.business_id=".$businessid;
            $bNeedOr=TRUE;
        }

        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        foreach ($db->fetch_array("SELECT DISTINCT genmessage.id, genmessage.title, genmessage.message FROM genmessage
                LEFT JOIN genmessagebus ON genmessagebus.genmessage_id=genmessage.id
                LEFT JOIN genmessageloc ON genmessageloc.genmessage_id=genmessage.id
                WHERE (".$businessQuery.") OR genmessageloc.locations_id=".$Location) as $genmessage)
            $return[$genmessage['id']]=array ('title' => $genmessage['title'], 'message' => $genmessage['message']);
        return $return;
    }
    public static function getAllMessages() {
        $return=array();
        $db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
        foreach ($db->fetch_array("SELECT DISTINCT genmessage.id, genmessage.title, genmessage.message FROM genmessage
                LEFT JOIN genmessagebus ON genmessagebus.genmessage_id=genmessage.id
                LEFT JOIN genmessageloc ON genmessageloc.genmessage_id=genmessage.id") as $genmessage) {
        
            $businesses=array();
            $locations=array();
            foreach ($db->fetch_array("SELECT business.id, business.business FROM business, genmessagebus
                    WHERE genmessagebus.business_id=business.id AND genmessagebus.genmessage_id=".$genmessage['id']) as $var)
                $businesses=array ('id' => $var['id'], 'business' => $var['business']);
            foreach ($db->fetch_array("SELECT locations.id, locations.location FROM locations, genmessageloc
                    WHERE genmessageloc.locations_id=locations.id AND genmessageloc.genmessage_id=".$genmessage['id']) as $var)
                $locations=array ('id' => $var['id'], 'location' => $var['location']);

            $return[$genmessage['id']]=array ('title' => $genmessage['title'], 'message' => $genmessage['message'],
                                              'businesses' => $businesses, 'locations' => $locations);  
        }
        return $return;
    }
}
?>
