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
	//Checks if post values contain text and updates if they do
	
	$process = false;
	$substr = $_POST["substr_length"];
	$snort_path = $_POST["snort_path"];
	
	if($substr != null) {
		if($snort_path != null){
			include("../includes/dbconnect.php");
			$substr = mysql_real_escape_string($substr);
			$snort_path = mysql_real_escape_string($snort_path);
			$query = "UPDATE config SET substr_length = $substr, snort_rules_path = '$snort_path' WHERE config_id = 1";
			mysql_query($query);
			include("../includes/dbclose.php");
			$process = true;
		}
	}	 
?>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>PigPen</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="style.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body>
	<div id="logo">
		<h1><a href="#">PigPen</a></h1>
		<p><em> an open source DLP solution utilizing Snort</em></p>
	</div>
	<hr />
	<!-- end #logo -->
	<?php 
		// header include
		include("../includes/header.php");
	?>
	<div id="page">
		<div id="content">
		  <div class="post">
				<h2 class="title">Configuration</a></h2>
				<div class="entry">
					<?php 
						if ($process == true) {
							echo "<b><font color=\"red\"><strong>Updated completed successfully</strong></b></font><br><br>";
						}
						include("../includes/dbconnect.php");
						$query = "SELECT substr_length, snort_rules_path FROM config WHERE config_id = 1";
						$result = mysql_query($query);
						$row = mysql_fetch_array($result);
					?>
					<form action="config.php" method="post">
						<b>Substring Length: </b><input type="text" id="substr_length" name="substr_length" 
							value="<?php echo $row['substr_length']; ?>"/><br><br>
						<b>Snort Rules Path: </b><input type="text" id="snort_path" name="snort_path" 
							value="<?php echo $row['snort_rules_path']; ?>"/><br><br>
						<input type="submit" id="upduate" value="Update" />
					</form>
					<?php include("../includes/dbclose.php"); ?>
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