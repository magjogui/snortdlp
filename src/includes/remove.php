<?php
	include("common.php");
	$type = $_GET["type"];
	$id = urldecode($_GET["id"]);
	
	
	if($type == "file"){
		include("dbconnect.php");
		$id = mysql_real_escape_string($id);
		
		//deletes the record for this file
		$query = "DELETE FROM rules WHERE rule_id = $id";
		mysql_query($query);
		include("dbclose.php");
		
		//rewrites the rules file with all the rules currently in the db
		rewriteRulesFile();
		
		//returns to inputFile.php
		header("location: ../inputFile.php");
		//die();
	} else if($type == "folder"){
		
		include("dbconnect.php");
		$id = mysql_real_escape_string($id);
		
		
		//TODO: query database for files with type = 2 and path = $id
		// and remove file --> $query = "DELETE FROM rules WHERE type=2 AND path = $id";
		
		
		include("dbclose.php");
		header("location: ../folderPath.php");
	} else if($type == "free"){
		
		include("dbconnect.php");
		$id = mysql_real_escape_string($id);
		
		//deletes the record for this file
		$query = "DELETE FROM rules WHERE rule_id = $id";
		mysql_query($query);
		include("dbclose.php");
		
		//rewrites the rules file with all the rules currently in the db
		rewriteRulesFile();
		
		header("location: ../freeText.php");
	}
	
?>