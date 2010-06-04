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
		$words = array_map("sanitizeRegex",$words); //sanitize each word

		//glue words together and build the regex
		$regex = "/(" . implode(')([\\r\s]*?)(', $words) . ")/i";
		
		return $regex;
	}
	
	function sanitizeRegex($inputText){
		/*
		 * Escape any reserved regex characters in $inputText.
		 */
		$reserved = array("\\", "/", "[","^","$",".","|","?","*","+","(",")","{","}");
		
        for($i = 0; $i < count($reserved); $i++){
        	$char = $reserved[$i];
        	$inputText = str_replace($char, "\\".$char, $inputText);
        }

		return $inputText;
	}
	
	function insertHistogramIntoDatabase($histogram){

		include("includes/dbconnect.php");
		$sql = array();
		
		foreach($histogram as $word => $count){
			$sql[] = '("'.$word.'", '.$count.')';
		}
		
		//the following query will insert the new count if non exists
		//or upon duplication it will add the new value to the existing count
		$query = "INSERT INTO words (word,count) VALUES " . implode(',', $sql) . " ON DUPLICATE KEY UPDATE count = count + VALUES (count)";
		mysql_query($query);
		include("includes/dbclose.php"); 
	}
	
	function getNextsid(){
		/*
		 * Count the lines in the snort file and return the next logical sid for snort rules
		 */
		$sidBase = 1000000;
		
		include("includes/dbconnect.php");
		//gets the snort rules file
		$query = "SELECT sid FROM rules ORDER BY sid DESC LIMIT 1";
		$result = mysql_query($query);
		
		if(mysql_num_rows($result) == 0){
			return $sidBase;
		}
		
		$row = mysql_fetch_array($result);
		$sid = $row['sid'] + 1;
		include("includes/dbclose.php"); 
		
		return $sid;
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
		//$split = preg_split('/[\s|\n|\r]+/', strtolower($inputText));
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
	 * rewrites the snort rules file based on the rows in the database
	 */
	function rewriteRulesFile(){
		
		include("dbconnect.php");
		//gets the rule used for this file
		$query = "SELECT rule FROM rules";
		$result = mysql_query($query);
		
		$config = getConfig();
		$snortFile = $config['snortFile'];
		$file_handle = fopen($snortFile, 'w+');
		
		//writes header
		fwrite($file_handle, "#############################################\n");
		fwrite($file_handle, "#              SnortDLP Rules               #\n");
		fwrite($file_handle, "#############################################\n");
		
		//re-writes all rules from the db
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			fwrite($file_handle, $row['rule']);
		}
		
		//closes db connection
		include("dbclose.php");
		//closes the file
		fclose($file_handle);
		return;
	}
	
	function openShare($ip, $user, $pass, $folder){
		exec("sudo mkdir /mnt/share");
		exec("sudo mount -t cifs //" . $ip . "/" . $folder . " /mnt/share -o username=" . $user . ",password=" . $pass);
		
		return "/mnt/share/";
	}
	
	function closeShare(){
		//TODO: check why this isn't executing correctly
		exec("sudo umount -l /mnt/share");
		exec("sudo rmdir /mnt/share");
		return;
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
			return True;
		}
		
		return False;
	}
	
	function scoreHistogram($histogram){
		//Takes a local score histogram and returns a histogram of the weighted total of the local and repository scores
		
		$alpha = 1; //local repository weight
		$beta = .5; //global repository weight
		
		include("dbconnect.php");
		
		$query = "SELECT word, count FROM words WHERE word IN ('" . implode("','", array_keys($histogram)) ."')";
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			$word = strtolower($row['word']);
			$histogram[$word] = $histogram[$word] * $alpha + $row['count'] * $beta;	//weight the local and global/repository scores
		}

		include("dbclose.php");
		return $histogram;
	}
	
	function returnRepositoryLocations(){
		/*
		 * Query the database for all file locations.
		 */
		
		$locations = array();
		
		include("dbconnect.php");
		//gets the rule used for this file
		$query = "SELECT file_name, path FROM rules";
		$result = mysql_query($query);
		
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			array_push($locations, $row['path'] . '/' . $row['file_name']);
		}

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
					$path = "$startingDirectory$thisEntry";
					
					//process the file we found
					if (!is_dir($path)){
						processFile(2, $path, $scoringMethod, $substringLength, $snortFile); //if the found file is not a directory, process it
					}
					
					// If we are processing subdirectories and the entry is a directory, recursively call our function on it
					if( ($includeSubfolders) && is_dir($path)){
						processFolder($path."/", $includeSubfolders, $scoringMethod, $substringLength, $snortFile);
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
		
		if(!fileAlreadyProcessed($path)){ 
			
			$file = fopen($path, 'r') or die("processFile(): can't open $path");
			$substring = "";
			$inputText = fread($file, filesize($path));
			fclose($file);
			
			switch($scoringMethod){
				case "histogram":
					$substring = selectSubstringHistogram(genHistogram($inputText), $inputText, $substringLength, 0);
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
					$substring = selectSubstringHistogram(genHistogram($inputText), $inputText, $substringLength, 0);
			}
			
			if($substring == ""){
				return; //if no unique substring is found, skip this file
			}
			
			$sid = getNextsid();
			$rule = createSnortRule($sid, $path, $substring);
			
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
			
			$query = "INSERT INTO rules (file_name, path, rule, regex, count, sid, type) VALUES ('$fileName', '$path', '$rule', '$regex', 1, $sid, $type)";
			mysql_query($query);
			include("dbclose.php");
		}
		
		return;
	}
	
	function fileAlreadyProcessed($path){
		/*
		 * Query the database and see if this specific file has already been processed.
		 */
		
		include("dbconnect.php");
		
		$parts = explode("/", $path); //get our path element parts
		$fileName = array_pop($parts);
		$path = implode("/", $parts); //rebuild our path
		
		$query = "SELECT path, file_name FROM rules WHERE path = '$path' AND file_name = '$fileName'";
		$result = mysql_query($query);
		$num_rows = mysql_num_rows($result);
		
		include("dbclose.php");
		
		if ($num_rows > 0){
			return true;
		}
		else {
			return false;
		}
	}

?>