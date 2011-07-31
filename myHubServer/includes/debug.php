<?php
		function bt_debug($file, $line, $str)
		{
			$f = basename($file);
			bt_debug2("<br>$f($line) BT DEBUG:  "); 
			bt_debug2($str);
			// bt_debug2("<br>");
		}
		function bt_debug2($str)
		{
			print_r($str);
		}
		function bt_debug3($hdr, $str)
		{
			bt_debug2("<br> BT DEBUG:  ".$hdr);
			bt_debug2($str);
			bt_debug2("<br>");
		}
		function bt_error($file, $line, $str)
		{
			$f = basename($file);
			print_r("$f($line) BT ERROR:  ".$str."<br>");
			debug_print_backtrace();
			print_r("<br><br>");
		}
?>
