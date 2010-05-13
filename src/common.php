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
	
	function insertHistogramInDatabase($histgram){
		
		//TODO: implement database methods
		
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

?>