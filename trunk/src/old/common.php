<?php
	// Commonly used functions, including the regex and rule generating functions
	
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
	
	function queryDatabase($query){

		/*
		 * Connect to our specific database and pass along $query
		 */
		
		// Connect to db
		include("/includes/dbconnect.php");
		
		$query = mysql_real_escape_string($query); //sanitize the string before passing to MySQL
		
		//perform the query and cleanup
		$result = mysql_query($query);
		
		// Close db connection
		include("/includes/dbclose.php");
		
		return $result;
	}
	
	function insertHistogramIntoDatabase($histgram){
		
		//TODO: Correct SQL statement, probably wrong
		
		// Connect to db
		include("/includes/dbconnect.php");
		
		foreach($histogram as $word => $count){
			$query = "INSERT INTO words VALUES (null, $count)";
			//queryDatabase($query);
		}
		
		// Close db connection
		include("/includes/dbclose.php");
		
		null;
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
			$lines = count(file($snortFile)) or die("can't open $snortFile");
			return $sidBase + $lines - 5;
		}
	}
	
	function standardizeText($inputText){
		/*
		 * Standard function to standardize a text string.
		 * Converts $inputText to lowercase and removes whitespace.
		 */
				
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
		$fh = fopen($file, 'a') or die("can't open $file");
		fwrite($fh, $string . "\n");
		fclose($fh);
	}

?>