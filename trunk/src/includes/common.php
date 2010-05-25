<?php
	// Commonly used functions, including the regex and rule generating functions	
	//include ("histogram.php");
	include ("sampling.php");
	
	function createSnortRule($sid, $alertName, $inputText){
		/*
		 * Given a sid, name and input text, return a Snort rule
		 * containing a PCRE generated from the input text.
		 * 
		 * Note: $alertName should usually be set to the name of the input file
		 */
		
		$regex = createRegex($inputText);
		$rule = "alert tcp \$HOME_NET any -> \$EXTERNAL_NET any (msg:\"Possible detection of: $alertName\"; pcre:\"$regex\"; classtype:data-loss; sid:$sid;)";
		
		return $rule;
	}
	
	function getConfig(){
		/*
		 * Return an array containing location of the Snort file and the 
		 * substring length from the stored configuration in the database.
		 */
		
		$configVariables = Array();
		
		include("includes/dbconnect.php");
		//gets the snort rules file
		$query = "SELECT substr_length, snort_rules_path FROM config WHERE config_id = 1";
		$result = mysql_query($query);
		$num_rows = mysql_num_rows($result);
		
		//checks if the user has configured the snort rule path
		if($num_rows!=1){
			header("location: config?new=1");
			die();
		}
		
		//sets variables to db values
		$row = mysql_fetch_array($result);
		$snortFile = $row['snort_rules_path']; //sets snortFile to the file and path from db
		$substringLength = $row['substr_length']; //sets the substringLength to length from db
		
		$configVariables['snortFile'] = $snortFile;
		$configVariables['substringLength'] = $substringLength;
		
		include("includes/dbclose.php");
		
		return $configVariables;
	}
	
	function createRegex($inputText){
		/*
		 * Create a PCRE regular expression from $inputText. 
		 */
		
		$words = explode(" ", standardizeText($inputText));
		array_map("sanitizeRegex",$words); //sanitize each word
		
		//glue words together and build the regex
		$regex = "/(" . implode(")( )*(", $words) . ")/is";
	
		return $regex;
	}
	
	function sanitizeRegex($inputText){
		/*
		 * Escape any reserved regex characters in $inputText.
		 */
		
		$reserved = array("\\","[","^","$",".","|","?","*","+","(",")","{","}");
		
        for($i = 0; $i < count($reserved); $i++){
        	$char = $reserved[$i];
        	$inputText = str_replace($char, "\\".$char, $inputText);
        }

		return $inputText;
	}
	
	function insertHistogramIntoDatabase($histogram){
		//TODO: make this cleaner, find a better way to do this

		include("includes/dbconnect.php");
		
		foreach($histogram as $word => $count){
			$existing = false;
			$word = mysql_real_escape_string($word); //should we do this??? TODO
			
			$query = "SELECT count FROM words WHERE word=\"$word\"";
			$result = mysql_query($query);
			
			while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
				$count += $row['count'];
				$existing = true;
			}
			
			if($existing){ //messy ... better way to do this?
				$query = "UPDATE words SET count=$count WHERE word='$word'";
			}
			else{
				$query = "INSERT INTO words (word, count) VALUES ('$word', $count)";
			}

			mysql_query($query);
		}
		include("includes/dbclose.php"); 
	}
	
	function getNextsid($snortFile){
		/*
		 * Count the lines in the snort file and return the next logical sid for snort rules
		 */
		$sidBase = 1000000;
		if($snortFile == ""){ //if the snort file wasn't set, return the base SID
			return $sidBase;
		}
		else{
			$lines = count(file($snortFile)) or die("getNextsid(): can't open $snortFile");
			return $sidBase + $lines - 2;
		}
	}
	
	function standardizeText($inputText){
		/*
		 * Standard function to standardize a text string.
		 * Converts $inputText to lowercase and removes whitespace.
		 */
		
		//clean all non-ascii charaters from the input text
		//$inputText = preg_replace('/[^(\x20-\x7F)]*/','', $inputText);
		
		//convert the string to lowercase and split on whitespace
		$split = preg_split('/\s+/', strtolower($inputText)); 
		
		//reassemble the string, separated by a single space
		$cleanText = trim(implode(" ", $split));
		
		return $cleanText;
	}
	
	function writeToFile($file, $string){
		/*
		 * Write $string out to $file.
		 */
		$fh = fopen($file, 'a') or die("writeToFile(): can't open $file");
		fwrite($fh, $string . "\n");
		fclose($fh);
	}
	/**
	 * TODO: Fix this method (this code works in another file, not sure why it doesn't work here
	 */
	function rewriteRulesFile(){
		
		include("dbconnect.php");
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
	}
	
	function inRepository($substring){
		// TODO: CHECK THE ACCURACY OF THIS!!!
		
		$repositoryLocations = returnRepositoryLocations();
		
		//TODO: how to properly handle this?
		if (count($repositoryLocations) == 0){
			//repository array empty
			return False;
		}
		
		foreach ($repositoryLocations as $location){
			if (inFile($location, $substring)){
				return True;
			}
		}
		
		return False;		
	}
	
	function inDirectory($startingDirectory, $substring){
		/*
		 * Crawl a directory, searching each file for the specific substring. 
		 * 
		 * Not used anymore.... remove? TODO
		 */
		
		if($dObj = dir($startingDirectory)) {
			while($thisEntry = $dObj->read()) { 
				if ($thisEntry != "." && $thisEntry != "..") {
					$path = "$startingDirectory/$thisEntry";
					//Check if the substring is in the specific file
					if (inFile($path, $substring)){
						return True;
					}
					
					// If the entry is a directory, recursively call our function on it
					if(($thisEntry != 0) && is_dir($startingDirectory/$thisEntry)){
						inDirectory("$startingDirectory/$thisEntry", $substring);
					} 
				} else { 
						//ignore "." and ".." to prevent an infinite loop
				}
			}
		}
		return False;
	}
	
	function inFile($path, $substring){
		/*
		 * If substring is found in the specified file, return True.
		 * Otherwise, return false. 
		 * 
		 * Possibly rewrite this to pass a string that we build a 
		 * regular expression from, and place in common.php?
		 */
		$file = fopen($path, 'r') or die("inFile(): can't open $path");
		$text = fread($file, filesize($path));
		fclose($file);
		$standardText = standardizeText($text);
		
		//if substring is found within the file, flag
		if (strripos($standardText,$substring)){
			#echo "Found \"$substring\" in \"$path\"\n";
			return True;
		}
		
		return False;
	}
	
	function repositoryScore($substring){
		
		//TODO: Correct SQL statement, probably wrong
		
		//need standardizeText() here?
		include("dbconnect.php");
		
		$words = explode(" ", standardizeText($substring));
		$score = 0;
		
		foreach ($words as $word){
			$query = "SELECT word_id, word, count FROM words WHERE word=\"$word\"";
			$result = mysql_query($query);
			while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
				$score += $row['count'];
			}
		}
		include("dbclose.php");
		
		return $score;
	}
	
	function returnRepositoryLocations(){
		
		$locations = array();
		
		include("dbconnect.php");
		//gets the rule used for this file
		$query = "SELECT file_name, path FROM rules";
		$result = mysql_query($query);
		
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			array_push($locations, $row['path'] . '/' . $row['file_name']);
		}
		
		//closes db connection
		include("dbclose.php");
		
		return $locations;
	}
	
	function processFolder($startingDirectory, $includeSubfolders, $scoringMethod, $substringLength, $snortFile){
		/*
		 * Crawl a directory a process each file found.
		 * 
		 * Used by folderPath.php
		 */
		if($dObj = dir($startingDirectory)) {
			while($thisEntry = $dObj->read()) { 
				if ($thisEntry != "." && $thisEntry != "..") {
					$path = "$startingDirectory/$thisEntry";
					
					//process the file we found
					processFile(2, $path, $scoringMethod, $substringLength, $snortFile);
					
					// If we are processing subdirectories and the entry is a directory, recursively call our function on it
					if( ($includeSubfolders) && ($thisEntry != 0) && is_dir($startingDirectory/$thisEntry)){
						processFolder("$startingDirectory/$thisEntry", $includeSubfolders, $scoringMethod, $substringLength, $snortFile);
					} 
				} else { 
						//ignore "." and ".." to prevent an infinite loop
				}
			}
		}
	}
	
	function processFile($type, $path, $scoringMethod, $substringLength, $snortFile){
		
		/*
		 * Process an individual filepath.
		 * 
		 * Type = 1 for individual processed files, 2 for files processed from a folder crawl.
		 */
		$file = fopen($path, 'r') or die("processFile(): can't open $path");
		$substring = "";
		$inputText = fread($file, filesize($path));
		fclose($file);
		
		switch($scoringMethod){
			case "histogram":
				$substring = selectSubstringHistogram(genHistogram($inputText), $inputText, $substringLength);
				break;
			case "modifiedhist":
				//$substring = selectSubstringModifiedHistogram(genHistogram($inputText), $inputText, $substringLength);
				break;
			case "multipleRandSamples":
				$substring = "";
				break;
			case "random":
				//$substring = selectSubstringRandom($inputText, $substringLength);
				break;
			default:
				$substring = selectSubstringHistogram(genHistogram($inputText), $inputText, $substringLength);
		}
		$rule = createSnortRule(getNextsid($snortFile), $path, $substring);
		
		if ($snortFile != ""){
			//if snortFile was passed, write the rule out to the snort file
			writeToFile($snortFile, $rule);
		}
		
		//writes file to the database
		include("dbconnect.php");
		
		$parts = explode("/", $path); //get our path element parts
		$fileName = array_pop($parts);
		$path = implode("/", $parts); //rebuild our path
		
		$path = mysql_real_escape_string($path);
		$fileName = mysql_real_escape_string($fileName);
		$rule = mysql_real_escape_string($rule);
		$regex = mysql_real_escape_string(createRegex($substring));
		
		$query = "INSERT INTO rules (file_name, path, rule, regex, count, type) VALUES ('$fileName', '$path', '$rule', '$regex', 1, $type)";
		mysql_query($query);
		include("dbclose.php");

		return;
	}

?>