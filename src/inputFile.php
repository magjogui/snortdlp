<?php include("includes/checklogin.php"); ?>
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
<body>
	<div id="logo">
		<h1><a href="#">PigPen</a></h1>
		<p><em> an open source DLP solution utilizing Snort</em></p>
	</div>
	<hr />
	<!-- end #logo -->
	<?php include("includes/header.php"); ?>
	<?php include("includes/common.php"); ?>
	<?php 
	
	/**
	 * checks if the fileName was past from the post method
	 */
	if (isset($_POST['fileName']) && !empty($_POST['fileName']) && isset($_POST['path']) && !empty($_POST['path'])){
		
		$fileName = $_POST['fileName'];
		$path = $_POST['path'];
		
	    // Checks to make sure the inputted path is valid and exists
		if($path{strlen($path)-1} != "/"){
			$path = $path . "/";
			if(realpath($path) == false){
				$path_error = true;
			}
		}
		
		$completeFile = $path. $fileName;
		
		$file = fopen($completeFile, 'r') or die("can't open $completeFile and $");
		$inputText = fread($file, filesize($completeFile));
		fclose($file);
	
		$config = getConfig();
		
		$snortFile = $config['snortFile'];
		$substringLength = $config['substringLength'];
		
		/*
		 * gets scoring method
		 */
		if (isset($_POST['scoringMethod']) && !empty($_POST['scoringMethod'])){
			$scoringMethod = $_POST['scoringMethod'];
		}
		
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
		
		$regex = createRegex($substring);
		
		$rule = createSnortRule(getNextsid($snortFile), $fileName, $substring);
		
		writeToFile($snortFile, $rule);
		
		include("includes/dbconnect.php");
		$path = mysql_real_escape_string($path);
		$fileName = mysql_real_escape_string($fileName);
		$rule = mysql_real_escape_string($rule);
		$regex = mysql_real_escape_string($regex);
		$query = "INSERT INTO rules (file_name, path, rule, regex, type, count) VALUES ('$fileName', '$path', '$rule', '$regex', 1, 1)";
		mysql_query($query);
		include("includes/dbclose.php"); 
	}
	
	?>
	<div id="page">
		<div id="content">
		  <div class="post">
				<h2 class="title">Process New File</a></h2>
				<div class="entry">
					<form action="inputFile.php" method="post">
						<table>
						<tr><td><b>Directory: </b><input type="text" id="path" name="path"/></td><td><b>File: </b><input type="text" id="fileName" name="fileName"/></td></tr>
						<tr><td><b>Method: </b>
							<SELECT NAME="scoringMethod">
								<OPTION VALUE=histogram SELECTED>Histogram
								<OPTION VALUE=modifiedhist>Modified histogram
								<OPTION VALUE=multipleRandSamples>Multiple random samples
								<OPTION VALUE=random>Random
							</SELECT></td>
							<td align="right"><input type="submit" id="create" value="Create" /></td></tr>
						</table>						
					</form>
					<br><br><br>
					<h2 class="title">Manage Protected Files</a></h2>
					<br>
					<table>
					<tr><td><b>Directory</b><td><b>File</b></td></td><td colspan="3" align="center"><b>Action</b></td></tr>
					<?php 
						include("includes/dbconnect.php");
						
						$query = "SELECT rule_id, file_name, path FROM rules WHERE type = 1";
						$result = mysql_query($query);
						
						while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
							echo "<tr><td>" . $row['path'] . "</td><td width=\"250\">" . $row['file_name'] . "</td><td><a href=\"display.php?type=file&id=" . $row['rule_id'] . "\">display</a> |</td><td><a href=\"includes/remove.php?type=file&id=" . $row['rule_id'] . "\">delete</a> |</td><td><a href=#>recalculate</a></td></tr>";
					
						}					
						include("includes/dbclose.php"); 
						
						
					?>
					</table>
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
