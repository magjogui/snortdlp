<?php
	include("common.php"); //utility class
	
	function selectSubstringHistogram($histogram, $inputText, $substringLength){
		
		/*
		 * Return the lowest scored substring from $inputText using a passed histogram
		 */
		
		$alpha = 1; //local repository weight
		$beta = .5; //global repository weight
		$substringScores = array();
		$split = explode(" ", standardizeText($inputText)); //split standardized string into words

		//iterate through all possible substrings of the specified length
		for($i=0; $i < count($split) - $substringLength +1; $i++){
			$substring = implode(" ", array_slice($split,$i,$substringLength)); //grab a substring of the correct length
			$repositoryScore = repositoryScore($substring);
			$score = $alpha * localScore($histogram, $substring) + $beta * $repositoryScore;
			$substringScores[$i] = $score;
		}
		asort($substringScores); //sort the frequency array by value but preserve keys
		reset($substringScores); //reset the key pointer so we can iterate correctly
		
		//grab the lowest scored substring
		$substring = implode(" ", array_slice($split,key($substringScores),$substringLength));
		
		while(inRepository($substring)){ //while the chosen substring is found in the repository
			if (!next($substringScores)){ //next() returns false at the end of the array
				return ""; //if a unique substring is not found, return ""
			}
			$substring = implode(" ", array_slice($split,key($substringScores),$substringLength));
		}
		
		return $substring;
	}
	
	function genHistogram($inputText){
		/*
		 * Generate the histogram of the inputText.
		 * Returns: histogram of inputText
		 * 
		 * Every time we generate a histogram, insert it into the global histogram table
		 */
		
		$words = explode(" ", $inputText); //split our standardized input by spaces
		$histogram = array_count_values($words); //return an arrray of occurances
		insertHistogramIntoDatabase($histogram);
		
		return $histogram;
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

	function selectSubstringModifiedHistogram($histogram, $inputText, $substringLength){
		
		//only consider the middle 50% of the text when generating the histogram
		$inputText = substr($inputText, strlen($inputText) * .25, strlen($inputText)*.5);
		
		$substring = selectSubstringHistogram($histogram, $inputText, $substringLength);
		
		return $subtring;
	}
	
	function selectSubstringRandom($inputText, $substringLength){
		
		
		$split = explode(" ", standardizeText($inputText)); //split standardized string into words
		
		$pos = rand(0, strlen($inputText) - $substringLength); //get a random position
		$substring = implode(" ", array_slice($split,$pos,$substringLength)); 
		
		while(inRepository($substring)){ //while the chosen substring is found in the repository
			$pos = rand(0, strlen($inputText) - $substringLength); //get a random position
			$substring = implode(" ", array_slice($split,$pos,$substringLength)); //pull out the random substring
		}
		
		return $substring;
		
	}
	
	
?>