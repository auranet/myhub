<?php
require_once 'includes/User.php';
require_once 'includes/Link.php';
function exportMysqlToCsv($table,$filename)
{
	
    $csv_terminated = "\n";
    $csv_separator = ",";
    $csv_enclosed = '"';
    $csv_escaped = "\\";
    $sql_query = "select * from $table";
 
    // Gets the data from the database
    $result = mysql_query($sql_query);
    $fields_cnt = mysql_num_fields($result);
 
 
    $schema_insert = '';
 
    for ($i = 0; $i < $fields_cnt; $i++)
    {
		if($i!=0){
			if($i==1)
				$str='Link name';
			if($i==2)
				$str='User name';
			if($i==3)
				$str='Question';	
			if($i==4)
				$str='Answer';	
			if($i==5)
				$str='Type';	
			if($i==6)
				$str='Timestamp';					
			$l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $str) . $csv_enclosed;			
	        $schema_insert .= $l;
	        $schema_insert .= $csv_separator;
		}
    } // end for
	
		
    $out = trim(substr($schema_insert, 0, -1));
    $out .= $csv_terminated;
 
    // Format the data
    while ($row = mysql_fetch_array($result))
    {
        $schema_insert = '';
        for ($j = 0; $j < $fields_cnt; $j++)
        {
			if($j!=0){
				if (($row[$j] == '0' || $row[$j] != ''))
	            {
	                if ($csv_enclosed == '')
	                {
	                    $schema_insert .= $row[$j];
	                } else
	                {
						if($j==1)
							$row[$j]=Link::getLinkNameById($row[$j]);
						if($j==2)
							$row[$j]=User::getUserNameById($row[$j]);
						if($j==3){
							$row[$j]=Link::getQuestionBySurveyId($row[$j], $row[$j+2]);							
							}
						$schema_insert .= $csv_enclosed . 
						str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $row[$j]) . $csv_enclosed;					
	                }
	            } else
	            {
	                $schema_insert .= '';
	            }
	 
	            if ($j < $fields_cnt)
	            {
	                $schema_insert .= $csv_separator;
	            }
			
			}
        } // end for
 
        $out .= $schema_insert;
        $out .= $csv_terminated;
		
    } // end while
 
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Length: " . strlen($out));
    // Output to browser with appropriate mime type, you choose ;)
    header("Content-type: text/x-csv");
    //header("Content-type: text/csv");
    //header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename=$filename");
    echo $out;
    exit;
 
}
 
?>