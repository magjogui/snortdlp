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
<?php
$process = false; //will be true if there is an update to the database
$path_error = false; //will be true if there is a path error for the snort rules
$new = false; //will be true if the user has not configured the rules path yet
$substr = $_POST["substr_length"]; //length of the substring
$snort_path = $_POST["snort_path"]; //path of the snort rules

if($_GET["if"] == 1){ //if there is an invalid file
	$path_error = true;
}

if($_GET["new"] == 1){
	$new = true;
}

// Checks to make sure the inputted path is valid and exists
if($snort_path{strlen($snort_path)-1} != "/"){
	$snort_path = $snort_path . "/";
	if(realpath($snort_path) == false){
		$path_error = true;
	}
}

// checks to make sure none of the values are null and no path error was detected
if($substr != null AND $snort_path != null AND $path_error != true) {
	include("includes/dbconnect.php"); 
	
	//escapes values
	$substr = mysql_real_escape_string($substr);
	$snort_path = mysql_real_escape_string($snort_path);
	
	//checks if the file exists, if not, it creates it with a header
	$filename = $snort_path . "snortdlp.rules";
	if (!file_exists($filename)) {
		$file_handle = fopen($filename, 'w') or $path_error = true;
		if($path_error){
			header("location: config.php?if=1"); //redirects if path is invalid
			die();
		}
		fwrite($file_handle, "********************************************\n");
		fwrite($file_handle, "*              SnortDLP Rules              *\n");
		fwrite($file_handle, "********************************************\n");
		fclose($file_handle);
		chmod($filename, 0644);
	}
	
	//updates the db with the new values
	$query = "UPDATE config SET substr_length = $substr, snort_rules_path = '$filename' WHERE config_id = 1";
	mysql_query($query);
	include("includes/dbclose.php");
	$process = true; //sets the processed variable to true
}
?>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>PigPen</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="styles/style.css" rel="stylesheet" type="text/css"
	media="screen" />
</head>
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
<h2 class="title">Configuration</a></h2>
<div class="entry"><?php 
if ($process == true) {
	echo "<b><font color=\"red\"><strong>Updated completed successfully</strong></b></font><br><br>";
} else if ($path_error == true) {
	echo "<b><font color=\"red\"><strong>Error: Path does not exist! Please enter a valid path name.</strong></b></font><br><br>";
} else if ($new == true) {
	echo "<b><font color=\"red\"><strong>Please enter the Snort rules path before creating rules.</strong></b></font><br><br>";
}
include("includes/dbconnect.php");
$query = "SELECT substr_length, snort_rules_path FROM config WHERE config_id = 1";
$result = mysql_query($query);
$row = mysql_fetch_array($result);
$snort_path = $row['snort_rules_path'];
$snort_path = substr($snort_path, 0, strlen($snort_path)-14);//removes the filename from the path a bit ugly, but it'll do for now
?>
<form action="config.php" method="post">
<b>Substring Length: </b>
<input type="text" id="substr_length" name="substr_length"
	value="<?php echo $row['substr_length']; ?>" />
<br><br> <b>Snort Rules Path: </b><input type="text" id="snort_path"
	name="snort_path" value="<?php echo $snort_path ?>" /><br><br>
<input type="submit" id="upduate" value="Update" />

</form>
<?php include("includes/dbclose.php"); ?></div>
</div>
</div>
<!-- end #content -->
<div id="sidebar">
<ul>
	<li>
	<h2>About us:</h2>
	<p>Will Schroeder<br>Tyler Dean 
	
	</p>
	</li>
</ul>
</div>
<!-- end #sidebar -->
<div style="clear: both;">&nbsp;</div>
</div>
<!-- end #page -->
<div id="footer">
<p>All rights reserved. Design by <a
	href="http://www.freecsstemplates.org/">Free CSS Templates</a>.</p>
</div>
<!-- end #footer -->
</body>
</html>
