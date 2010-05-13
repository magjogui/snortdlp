<?php
	
include("common.php");
	
	function processRepository($repositoryLocations){
		//crawl repository locations, adding each file to the database histogram

		if (count($repositoryLocations) == 0){
			echo "inRepository() ERRORZ: repository array empty\n";
			return;
		}
		
		foreach ($repositoryLocations as $location){
			processDirectory($location);
		}
	}
	
	function processDirectory($startingDirectory){
		/*
		 * Create a histogram for every file in the specified directory
		 * and insert the histogram into the global histogram in the database.
		 */
		
		if($dObj = dir($startingDirectory)) {
			while($thisEntry = $dObj->read()) { 
				if ($thisEntry != "." && $thisEntry != "..") {
					$path = "$startingDirectory/$thisEntry";
					$file = fopen($path, 'r');
					$inputText = fread($file, filesize($path));
					
					//Generate a histogram for the file and insert it into the database
					$histogram = genHistogram($inputText);
					insertHistogramInDatabase($histogram);
					
					// If the entry is a directory, recursively call our function on it
					if(($thisEntry != 0) && is_dir($startingDirectory/$thisEntry)){
						processDirectory("$startingDirectory/$thisEntry");
					} 
				} else { 
						//ignore "." and ".." to prevent an infinite loop
				}
			}
		}
		return False;
	}
?>