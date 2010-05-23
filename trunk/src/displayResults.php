<?php include("includes/checklogin.php")?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--
Design by Free CSS Templates
http://www.freecsstemplates.org
Released for free under a Creative Commons Attribution 2.5 License

Name       : Milestone
Description: A two-column, fixed-width design for 1024x768 screen resolutions.
Version    : 1.0
Released   : 20100309

-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>PigPen</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="styles/style.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<?php 
	include("includes/sampling.php");

	$inputText = "no text entered";
	$substringLength = 7;
	$fileName = "no name specified";
	$snortFile = "";
	$scoringMethod = "histogram";
	
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
	include("includes/dbclose.php");
	
	if (isset($_POST['inputText']) && !empty($_POST['inputText'])){
		$inputText = $_POST['inputText'];
	}
	
	if (isset($_POST['fileName']) && !empty($_POST['fileName'])){
		$fileName = $_POST['fileName'];
	}
	
	if (isset($_POST['location']) && !empty($_POST['location'])){
		$path = $_POST['location'];
		
		// Checks to make sure the inputted path is valid and exists
		if($path{strlen($path)-1} != "/"){
			$path = $path . "/";
			if(realpath($path) == false){
				$path_error = true;
			}
		}
		
		$completeFile = $path . $fileName;
		
		$file = fopen($completeFile, 'r') or die("can't open $completeFile");
		$inputText = fread($file, filesize($completeFile));
		fclose($file);
	}
	
	if (isset($_POST['scoringMethod']) && !empty($_POST['scoringMethod'])){
		$scoringMethod = $_POST['scoringMethod'];
	}
?>
<body>
	<div id="logo">
		<h1><a href="#">PigPen</a></h1>
		<p><em> an open source DLP solution utilizing Snort</em></p>
	</div>
	<hr />
	<!-- end #logo -->
	<?php include("includes/header.php"); ?>
	<div id="page">
		<div id="content">
		  <div class="post">
				<h2 class="title">Substring scoring method:</a></h2>
				<div class="entry">
					<?php
						echo "$scoringMethod";
					?>
			</div>
		  </div>
		  <div class="post">
				<h2 class="title">Selected Substring</a></h2>
				<div class="entry">
					<?php
						//based on sampling method chosen, select the identifiable substring
						switch($scoringMethod){
							case "histogram":
								$substring = selectSubstringHistogram(genHistogram($inputText), $inputText, $substringLength);
								break;
							case "modifiedhist":
								$substring = selectSubstringModifiedHistogram(genHistogram($inputText), $inputText, $substringLength);
								break;
							case "multipleRandSamples":
								$substring = "";
								break;
							case "random":
								$substring = selectSubstringRandom($inputText, $substringLength);
								break;
							default:
								$substring = selectSubstringHistogram(genHistogram($inputText), $inputText, $substringLength);
						}
						echo "\"$substring\"";
					?>
			</div>
		  </div>
		  <div class="post">
				<h2 class="title">Regular Expression</a></h2>
				<div class="entry">
					<?php
						echo createRegex($substring) . "<br><br>";
					?>		
			</div>
		  </div>
		  <div class="post">
				<h2 class="title">Snort Rule</a></h2>
				<div class="entry">
					<?php
						$rule = createSnortRule(getNextsid($snortFile), $fileName, $substring);
						echo $rule . "<br><br>";
						
						if ($snortFile != ""){
							//if snortFile was passed, write the rule out to the snort file
							writeToFile($snortFile, $rule);
							echo "Snort rule written to $snortFile<br><br>";
						}
						//writes file to the database
						include("includes/dbconnect.php");
						$completeFile = mysql_real_escape_string($completeFile);
						$path = mysql_real_escape_string($path);
						$fileName = mysql_real_escape_string($fileName);
						$rule = mysql_real_escape_string($rule);
						$query = "INSERT INTO rules (file_name, rule, type, count) VALUES ('$completeFile', '$rule', 1, 1)";
						mysql_query($query);
						include("includes/dbclose.php"); 
						
					?>		
			</div>
		  </div>
		  
		  
		</div><!-- end #content -->
		<div id="sidebar">
			<ul>
				<li>
					<h2>About us:</h2>
					<p>Will Schroeder<br>Tyler Dean</p>
				</li>
			</ul>
		</div>
		<!-- end #sidebar -->
		<div style="clear: both;">&nbsp;</div>
	</div>
	<!-- end #page -->
	<div id="footer">
		<p>All rights reserved. Design by <a href="http://www.freecsstemplates.org/">Free CSS Templates</a>.</p>
	</div>
	<!-- end #footer -->
</body>
</html>
