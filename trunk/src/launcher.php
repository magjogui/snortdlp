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
	$substringLength = 5;
	$fileName = "no name specified";
			
	if (isset($_POST['repositoryLocations']) && !empty($_POST['repositoryLocations'])){
		$repositoryLocations = $_POST['repositoryLocations'];
		$useRepository = True;
	}
	
	if (isset($_POST['inputText']) && !empty($_POST['inputText'])){
		$inputText = $_POST['inputText'];
	}
	
	if (isset($_POST['inputFile']) && !empty($_POST['inputFile'])){
		$path = $_POST['inputFile'];
		$file = fopen($path, 'r');
		$inputText = fread($file, filesize($path));
	}
	
	if (isset($_POST['substringLength']) && !empty($_POST['substringLength'])){
		$substringLength = $_POST['substringLength'];
	}
	
	if (isset($_POST['fileName']) && !empty($_POST['fileName'])){
		$fileName = $_POST['fileName'];
	}
	
	echo "<h2>Selected substring:</h2>";
	$substring = selectSubstring($useRepository, $repositoryLocations, genHistogram($inputText), $inputText, $substringLength);
	echo "\"$substring\"";
	
	echo "<h2>Regex:</h2>";
	echo createRegex($substring);
	
	echo "<h2>Snort rule:</h2>";
	$rule = createSnortRule(1000000, $fileName, $substring);
	echo "$rule<br><br>";
	
	//code to test selectSubstring()
	/*$sampleText = "accomplished through an environmental adaptive strategy";
	$repositoryLocations = array("C:/tmp");

	$lowSubstring = selectSubstring(True, $locations, genHistogram($sampleText), $sampleText, 3);
	if ($lowSubstring == ""){
		echo "No unique substring found";
	}
	else{
		echo "Lowest substring: \"$lowSubstring\"\n";
	}*/
	
	/*test snort rule generation
	echo createSnortRule(1000000, "test", "this is a test")
	*/

?>
</body>
</html>