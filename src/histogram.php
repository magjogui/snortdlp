<?php
	
	// Implements the histogram methods

	include("common.php"); //utility class
	
	function selectSubstring($useRepository, $repositoryLocations, $histogram, $inputText, $substringLength){
		
		/*
		 * Return the lowest scored substring from $inputText
		 */
		
		$alpha = 1; //local repository weight
		$beta = .5; //global repository weight
		$substringScores = array();
		$split = explode(" ", standardizeText($inputText)); //split standardized string into words

		//iterate through all possible substrings of the specified length
		for($i=0; $i < count($split) - $substringLength +1; $i++){
			$substring = implode(" ", array_slice($split,$i,$substringLength)); //grab a substring of the correct length
			$repositoryScore = 0;
			if($useRepository){ //trigger on the global variable that indicates if the global repository is being used
				$repositoryScore = repositoryScore($substring);
			}
			$score = $alpha * localScore($histogram, $substring) + $beta * $repositoryScore;
			$substringScores[$i] = $score;
		}
		asort($substringScores); //sort the frequency array by value but preserve keys
		reset($substringScores); //reset the key pointer so we can iterate correctly
		
		//grab the lowest scored substring
		$substring = implode(" ", array_slice($split,key($substringScores),$substringLength));
		if($useRepository){
			while(inRepository($repositoryLocations, $substring)){ //while the chosen substring is found in the repository
				if (!next($substringScores)){ //next() returns false at the end of the array
					return ""; //if a unique substring is not found, return ""
				}
				$substring = implode(" ", array_slice($split,key($substringScores),$substringLength));
			}
		}
		return $substring;
	}
	
	function genHistogram($inputText){
		/*
		 * Generate the histogram of the inputText.
		 * Returns: histogram of inputText
		 */
		
		$words = explode(" ", $inputText); //split our standardized input by spaces
		return array_count_values($words); //return an arrray of occurances
	}
	
	function localScore($histogram, $substring){
		/*
		 * Return a score of a specific substring using the local histogram.
		 */
		
		//need standardizeText() here?
		$words = explode(" ", standardizeText($substring));
		$score = 0;
		
		foreach ($words as $word){
			$score += $histogram[$word];
		}
		
		return $score;
	}
	
	function inRepository($repositoryLocations, $substring){
		//crawl repository locations, checking each for the substring
		
		//TODO: how to properly handle this?
		if (count($repositoryLocations) == 0){
			echo "inRepository() ERRORZ: repository array empty\n";
			return;
		}
		
		foreach ($repositoryLocations as $location){
			if (inDirectory($location, $substring)){
				return True;
			}
		}
		
		return False;		
	}
	
	function inDirectory($startingDirectory, $substring){
		/*
		 * Crawl a directory, searching each file for the specific substring. 
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
		
		$file = fopen($path, 'r') or die("can't open $path");
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
	
	function insertHistogramIntoDatabase($histgram){
		
		//TODO: Correct SQL statement, probably wrong
		
		foreach($histogram as $word => $count){
			$query = "INSERT INTO words VALUE $count";
			queryDatabase($query);
		}
		
		null;
	}
	
	function repositoryScore($substring){
		
		//TODO: Correct SQL statement, probably wrong
		
		//need standardizeText() here?
		
		$words = explode(" ", standardizeText($substring));
		$score = 0;
		
		/*
		foreach ($words as $word){
			$query = "SELECT $word FROM table";
			$score += queryDatabase($query);
		}*/
		
		return $score;
	}
?>