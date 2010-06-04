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
		
		if (isset($_SERVER['HTTP_REFERER'])) {
			header("location: $_SERVER['HTTP_REFERER']");
		} else {
			header("location: ../inputFile.php");
		} 

	} else if($type == "folder"){
		
		include("dbconnect.php");
		$id = mysql_real_escape_string($id);
		
		//deletes the record for this file
		$query = "DELETE FROM rules WHERE rule_id = $id";
		mysql_query($query);
		
		include("dbclose.php");
		
		if (isset($_SERVER['HTTP_REFERER'])) {
			header("location: $_SERVER['HTTP_REFERER']");
		} else {
			header("location: ../folderPath.php");
		}

	} else if($type == "free"){
		
		include("dbconnect.php");
		$id = mysql_real_escape_string($id);
		
		//deletes the record for this file
		$query = "DELETE FROM rules WHERE rule_id = $id";
		mysql_query($query);
		include("dbclose.php");
		
		//rewrites the rules file with all the rules currently in the db
		rewriteRulesFile();
		
		if (isset($_SERVER['HTTP_REFERER'])) {
			header("location: $_SERVER['HTTP_REFERER']");
		} else {
			header("location: ../freeText.php");
		}

	}	

		header("location: ../freeText.php");
	} else if($type == "db"){
		include("dbconnect.php");
		$id = mysql_real_escape_string($id);
		
		//deletes the record for this file
		$query = "DELETE FROM rules WHERE rule_id = $id";
		mysql_query($query);
		include("dbclose.php");
		
		//rewrites the rules file with all the rules currently in the db
		rewriteRulesFile();
		
		header("location: ../database.php");
	}

?>