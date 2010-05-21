<?php
	
	$type = $_GET["type"];
	$id = $_GET["id"];
	
	if($type == "file"){
		include("dbconnect.php");
		$id = mysql_real_escape_string($id);
		
		//gets the rule used for this file
		$query = "SELECT rule FROM rules WHERE rule_id = $id";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$rule = $row['rule'];
		
		//gets the path for the SnortDLP.rules file
		$query = "SELECT snort_rules_path FROM config WHERE config_id = 1";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$snort_path = $row['snort_rules_path'];
		
		//deletes the record for this file
		$query = "DELETE FROM rules WHERE rule_id = $id";
		mysql_query($query);
		include("dbclose.php");
		
		//reads all the lines of the file and searches for rule to delete
		$file_handle = fopen($snort_path, 'r');
		$text = fread($file_handle, filesize($snort_path));
		fclose($file_handle);
		
		//creates an array based on a new line
		$lines = explode("\n", $text);
		$line_num = 0;
		foreach ($lines as $line){
			if ($line == $rule){
				break;
			}
			$line_num++;
		}
		unset($line);
		
		//deletes the rule
		unset($lines["$line_num"]);
		
		//writes back to the file
		$file_handle = fopen($snort_path, 'w+');
		foreach($lines as $line){
			fwrite($file_handle, $line . "\n");
		}
		unset($line);
		
		//closes the file
		fclose($file_handle);
		
		//returns to inputFile.php
		header("location: ../inputFile.php");
		die();
	}
	
?>