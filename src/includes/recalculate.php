<?php
	include("common.php");
	$type = $_GET["type"];
	$id = urldecode($_GET["id"]);
	
	if($type == "file"){
		include("dbconnect.php");
		$id = mysql_real_escape_string($id);
		
		$config = getConfig();
		$snortFile = $config['snortFile'];
		$substringLength= $config['substringLength'];
		$scoringMethod = "histogram";
		
		//get the existing count for this specific file
		$query = "SELECT sid, path, file_name, count FROM rules WHERE rule_id = $id";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		include("dbclose.php");	
		
		$path = $row['path']."/".$row['file_name'];
		$count = $row['count'];
		$sid = $row['sid'];
		$file = fopen($path, 'r') or die("processFile(): can't open " . $path);
		$substring = "";
		$inputText = fread($file, filesize($path));
		fclose($file);
		
		//get the next lowest scored substring
		switch($scoringMethod){
			case "histogram":
				$substring = selectSubstringHistogram(genHistogram($inputText), $inputText, $substringLength, $count+1);
				break;
			case "modifiedhist":
				//$substring = selectSubstringModifiedHistogram(genHistogram($inputText), $inputText, $substringLength);
				break;
			case "multipleRandSamples":
				break;
			case "random":
				//$substring = selectSubstringRandom($inputText, $substringLength);
				break;
			default:
				$substring = selectSubstringHistogram(genHistogram($inputText), $inputText, $substringLength, $count+1);
		}

		$rule = createSnortRule($sid, $row['path']."/".$row['file_name'], $substring);
		$regex = createRegex($substring);
		
		if ($snortFile != ""){
			writeToFile($snortFile, $rule);
			//echo "Snort rule written to $snortFile<br><br>";
		}
		
		//update the rule, regex and count for the rule
		include("dbconnect.php");
		$rule = mysql_real_escape_string($rule);
		$regex = mysql_real_escape_string($regex);
		
		$query = "UPDATE rules SET rule='$rule', regex='$regex', count=" . ($count+1) . " WHERE rule_id=$id";
		mysql_query($query);
		include("dbclose.php");
		
		//rewrites the rules file with all the rules currently in the db
		rewriteRulesFile();
		
		if (isset($_SERVER['HTTP_REFERER'])) {
			header("location: " . $_SERVER['HTTP_REFERER']);
		} else {
			header("location: ../manageFiles.php");
		}
	}
?>
	