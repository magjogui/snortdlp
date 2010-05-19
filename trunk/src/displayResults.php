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
	//include("includes/histogram.php");
	include("includes/sampling.php");
	
	$repositoryLocations  = array();
	$useRepository  = False;
	$inputText = "no text entered";
	$substringLength = 7;
	$alertName = "no name specified";
	$snortFile = "";
	$scoringMethod = "histogram";
	
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
	if (isset($_POST['alertName']) && !empty($_POST['alertName'])){
		$alertName = $_POST['alertName'];
	}
	if (isset($_POST['snortFile']) && !empty($_POST['snortFile'])){
		$snortFile = $_POST['snortFile'];
		if (! file_exists($snortFile) ){
			//if the snort output file doesn't already exist, write out the header information
			$header = "#\n#---------------------------\n# Data Loss Prevention rules\n#---------------------------\n";
			writeToFile($snortFile, $header);
		}
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
								$substring = selectSubstringHistogram($useRepository, $repositoryLocations, genHistogram($inputText), $inputText, $substringLength);
								break;
							case "modifiedhist":
								$substring = selectSubstringModifiedHistogram($useRepository, $repositoryLocations, genHistogram($inputText), $inputText, $substringLength);
								break;
							case "multipleRandSamples":
								$substring = "";
								break;
							case "random":
								$substring = selectSubstringRandom($useRepository, $repositoryLocations, $inputText, $substringLength);
								break;
							default:
								$substring = selectSubstringHistogram($useRepository, $repositoryLocations, genHistogram($inputText), $inputText, $substringLength);
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
						$rule = createSnortRule(getNextsid($snortFile), $alertName, $substring);
						echo $rule . "<br><br>";
						
						if ($snortFile != ""){
							//if snortFile was passed, write the rule out to the snort file
							writeToFile($snortFile, $rule);
							echo "Snort rule written to $snortFile<br><br>";
						}
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
