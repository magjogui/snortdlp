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
<link href="style.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<script type="text/javascript">

//Quick function that replaces the file name with the name from the input file location box
function replaceAlertName() { 
	var path = document.getElementById('inputFile').value;
	var name = path.split("/");
	document.getElementById('alertName').value = name[name.length-1];
}

</script>

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
				<h2 class="title">Process Input File</a></h2>
				<div class="entry">
					<form action="displayResults.php" method="post">
						<b>Alert name: </b><input type="text" id="alertName" name="alertName" value="risk1.txt"/><br><br>
						<b>Input file location: <b><input type="text" id="inputFile" name="inputFile" onChange="replaceAlertName()" value="C:/tmp/risk1.txt"/><br><br>
						<b>Repository location: </b><input type="text" value="C:/tmp"/><br><br>
						<b>Substring length: </b><input type="text" name="substringLength" value="10" /><br><br>
						<b>Snort output file: </b><input type="text" name="snortFile"/><br><br>
						<b>Method of substring scoring: </b>
							<SELECT NAME="scoringMethod">
								<OPTION VALUE=histogram SELECTED>Histogram
								<OPTION VALUE=modifiedhist>Modified histogram
								<OPTION VALUE=multipleRandSamples>Multiple random samples
								<OPTION VALUE=Random>Random
							</SELECT>
						<br><br>
						<input type="submit" id="process" value="PROCESS" />
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
