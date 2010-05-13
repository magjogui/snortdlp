<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Basic Input Form</title>
</head>
<body>
<?php
	
	include("histogram.php");
	
	$repositoryLocations  = array();
	$useRepository  = False;
	$inputText = "no text entered";
	$substringLength = 7;
	$fileName = "no name specified";
	$snortFile = "";
	
	//should we put this in a $_SESSION[]?
	if (isset($_POST['repositoryLocations']) && !empty($_POST['repositoryLocations'])){
		$repositoryLocations = $_POST['repositoryLocations'];
		$useRepository = True;
	}
	if (isset($_POST['inputText']) && !empty($_POST['inputText'])){
		$inputText = $_POST['inputText'];
	}
	if (isset($_POST['inputFile']) && !empty($_POST['inputFile'])){
		$path = $_POST['inputFile'];
		$file = fopen($path, 'r') or die("can't open $path");
		$inputText = fread($file, filesize($path));
		fclose($file);
	}
	if (isset($_POST['substringLength']) && !empty($_POST['substringLength'])){
		$substringLength = $_POST['substringLength'];
	}
	if (isset($_POST['fileName']) && !empty($_POST['fileName'])){
		$fileName = $_POST['fileName'];
	}
	if (isset($_POST['snortFile']) && !empty($_POST['snortFile'])){
		$snortFile = $_POST['snortFile'];
		if (! file_exists($snortFile) ){
			//if the snort output file doesn't already exist, write out the header information
			$header = "#\n#---------------------------\n# Data Loss Prevention rules\n#---------------------------\n";
			writeToFile($snortFile, $header);
		}
	}
	
	/*
	print_r($repositoryLocations);
	echo "\$useRepository = $useRepository<br>";
	echo "\$inputText = $inputText<br>";
	echo "\$substringLength = $substringLength<br>";
	echo "\$fileName = $fileName<br>";
	echo "\$snortFile = $snortFile<br>";
	*/
	
	echo "<h2>Selected substring:</h2>";
	$substring = selectSubstring($useRepository, $repositoryLocations, genHistogram($inputText), $inputText, $substringLength);
	echo "\"$substring\"";
	
	echo "<h2>Regex:</h2>";
	echo createRegex($substring);
	
	echo "<h2>Snort rule:</h2>";
	$rule = createSnortRule(getNextsid($snortFile), $fileName, $substring);
	echo "$rule<br><br>";
	
	if ($snortFile != ""){
		//if snortFile was passed, write the rule out to the snort file
		writeToFile($snortFile, $rule);
		echo "Snort rule written to $snortFile<br><br>";
	}

?>
</body>
</html>