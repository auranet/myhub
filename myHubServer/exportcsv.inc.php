<?php

function exportMysqlToCsv($table,$filename)
{
	$businessesArr = Business::getTypes();
    $locationsArr = Locations::getTypes();
	$privilegesArr = Usertype::getTypes();
	
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
			if($i==5)
				$l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
					stripslashes('location')) . $csv_enclosed;
			elseif($i==6)
				$l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
					stripslashes('usertype')) . $csv_enclosed;					
	        else
				$l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
					stripslashes(mysql_field_name($result, $i))) . $csv_enclosed;
			
	        $schema_insert .= $l;
	        $schema_insert .= $csv_separator;
		}
    } // end for
	
	$l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
            stripslashes('business')) . $csv_enclosed;
    $schema_insert .= $l;
    $schema_insert .= $csv_separator;
		
    $out = trim(substr($schema_insert, 0, -1));
    $out .= $csv_terminated;
 
    // Format the data
    while ($row = mysql_fetch_array($result))
    {
        $schema_insert = '';
        for ($j = 0; $j < $fields_cnt+1; $j++)
        {
			if($j!=0){
				if($j==8){
						$businessArr = '';
						$my_user=new User((int)$row[0]);
						$business=$my_user->getBusiness();					
						foreach ($business as $key => $value) {
							$businessArr .= $value. ';';						
						} 
						//$business=stripslashes('businessval');
	                    $schema_insert .= $csv_enclosed . 
						str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $businessArr) . $csv_enclosed;
				}		
	            elseif (($row[$j] == '0' || $row[$j] != ''))
	            {
	                if ($csv_enclosed == '')
	                {
	                    $schema_insert .= $row[$j];
	                } else
	                {

						if($j==5)
							$row[$j]=$locationsArr[$row[$j]];
						if($j==6)
							$row[$j]=$privilegesArr[$row[$j]];
						
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