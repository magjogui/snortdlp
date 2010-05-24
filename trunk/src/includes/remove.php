<?php
	include("common.php");
	$type = $_GET["type"];
	$id = urldecode($_GET["id"]);
	
	
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
		
		
		//gets the rule used for this file
		$query = "SELECT rule FROM rules";
		$result = mysql_query($query);
		$file_handle = fopen($snort_path, 'w+');
		
		//writes header
		fwrite($file_handle, "********************************************\n");
		fwrite($file_handle, "*              SnortDLP Rules              *\n");
		fwrite($file_handle, "********************************************\n");
		
		//re-writes all rules from the db
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			fwrite($file_handle, $row['rule']);
		}
		
		//closes db connection
		include("dbclose.php");
		//closes the file
		fclose($file_handle);
		
		//include("dbclose.php");
		
		//rewriteRulesFile();
		
		//returns to inputFile.php
		header("location: ../inputFile.php");
		//die();
	}
	
	if($type == "folder"){
		
		include("dbconnect.php");
		$id = mysql_real_escape_string($id);
		
		
		//TODO: query database for files with type = 2 and path = $id
		// and remove file --> $query = "DELETE FROM rules WHERE type=2 AND path = $id";
		
		
		include("dbclose.php");
		header("location: ../folderPath.php");
	}
	
?>