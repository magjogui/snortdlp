<?php
	//common is already called by including the histogram.php class
	//include("common.php"); //utility class
	include("histogram.php");
	
	
	function selectSubstringModifiedHistogram($useRepository, $repositoryLocations, $histogram, $inputText, $substringLength){
		
		//only consider the middle 50% of the text when generating the histogram
		$inputText = substr($inputText, strlen($inputText) * .25, strlen($inputText)*.5);
		
		$substring = selectSubstringHistogram($useRepository, $repositoryLocations, $histogram, $inputText, $substringLength);
		
		return $subtring;
	}
	
	function selectSubstringRandom($useRepository, $repositoryLocations, $inputText, $substringLength){
		
		
		$split = explode(" ", standardizeText($inputText)); //split standardized string into words
		
		$pos = rand(0, strlen($inputText) - $substringLength); //get a random position
		$substring = implode(" ", array_slice($split,$pos,$substringLength)); 
		
		if($useRepository){
			while(inRepository($repositoryLocations, $substring)){ //while the chosen substring is found in the repository
				$pos = rand(0, strlen($inputText) - $substringLength); //get a random position
				$substring = implode(" ", array_slice($split,$pos,$substringLength)); //pull out the random substring
			}
		}
		
		return $substring;
		
	}
	
	
?>