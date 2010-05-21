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
				<h2 class="title">Templates</a></h2>
				<div class="entry">
					<form action="templates.php" method="post">
						<table>
						<tr><td><strong>Template</strong></td><td><strong>Use</strong></td></tr>
						<tr><td>Social Security Numbers:</td><td><input type="checkbox" name="SSN" value="SSN"  /></td></tr>
						<tr><td>Driver's License Numbers:</td><td><input type="checkbox" name="DLN" value="DLN"  /></td></tr>
						<tr><td>Credit Card Numbers:</td><td><input type="checkbox" name="CCN" value="CCN"  /></td></tr>
						<tr><td>Financial Data:</td><td><input type="checkbox" name="finance" value="finance"  /></td></tr>
						<tr><td>Source Code:</td><td><input type="checkbox" name="source" value="source"  /></td></tr>
						<tr><td>Medical Data:</td><td><input type="checkbox" name="medical" value="medical"  /></td></tr>
						</table>
						<br>
						<input type="submit" id="submit" value="submit" />
					</form>
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
