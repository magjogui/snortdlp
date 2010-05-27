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
	<?php include("includes/header.php") ?>
	<?php include("includes/common.php") ?>
	<?php 
	$noAlert = false;
	$noText = false;
	if (isset($_POST['alertName']) && !empty($_POST['alertName']) && isset($_POST['inputText']) && !empty($_POST['inputText'])){
		
		$alert = $_POST['alertName'];
		$input = $_POST['inputText'];
		$config = getConfig();
		$snortFile = $config['snortFile'];
		$substringLength = $config['substringLength'];
		
		/*
		 * gets scoring method
		 */
		if (isset($_POST['scoringMethod']) && !empty($_POST['scoringMethod'])){
			$scoringMethod = $_POST['scoringMethod'];
		}
		
		$sid = getNextSid($snortFile);
		$regex = createRegex($input);
		if ($regex !== "/()/is"){
			$rule = createSnortRule($sid, $alert, $input);
			writeToFile($snortFile, $rule);
			
			include("includes/dbconnect.php");
			$sid = mysql_real_escape_string($sid);
			$regex = mysql_real_escape_string($regex);
			$rule = mysql_real_escape_string($rule);
			$query = "INSERT INTO rules (rule, regex, count, sid, type) VALUES ('$rule', '$regex', 1, $sid, 3)";
			mysql_query($query);
			include("includes/dbclose.php");
		}
		
		
	} else if ((!isset($_POST['alertName']) || empty($_POST['alertName'])) && (isset($_POST['inputText']) && !empty($_POST['inputText']))) {
		$noAlert = true;
		$input = $_POST['inputText'];
	} else if ((isset($_POST['alertName']) && !empty($_POST['alertName'])) && (!isset($_POST['inputText']) || empty($_POST['inputText']))) {
		$noText = true;
		$alert = $_POST['alertName'];
	} else if (isset($_POST['process']) && !empty($_POST['process'])) {
		$noText = true;
		$noAlert = true;
	}

	?>
	<div id="page">
		<div id="content">
		  <div class="post">
				<h2 class="title">Process Free Text</a></h2>
				<div class="entry">
				<?php if($noAlert) echo "<b><font color=\"red\">Please choose an alert name.</font></b><br>"?>
				<?php if($noText) echo "<b><font color=\"red\">Please write sensitive text to be protected.</font></b><br>"?>
				<br>
					<form action="freeText.php" method="post">
						<b>Alert name: </b><input type="text" id="alertName" name="alertName" value="<?php if($noText) echo $alert ?>"/><br><br>
						<b>Input text: </b><br><TEXTAREA NAME=inputText ROWS=4 COLS=40 ><?php if($noAlert) echo $input ?></TEXTAREA><br><br>
						<b>Method of substring scoring: </b>
							<SELECT NAME="scoringMethod">
								<OPTION VALUE=histogram SELECTED>Histogram
								<OPTION VALUE=modifiedhist>Modified histogram
								<OPTION VALUE=multipleRandSamples>Multiple random samples
								<OPTION VALUE=random>Random
							</SELECT>
						<br><br>
						<input type="hidden" name="process" value="true">
						<input type="submit" id="submit" value="Create" />
					</form>
					<br><br><br>
					<h2 class="title">Manage Protected Text</a></h2>
					<br>
					<table>
					<tr><td><b>Alert</b></td><td colspan="3" align="center"><b>Action</b></td></tr>
					<?php 
						include("includes/dbconnect.php");
						
						$query = "SELECT rule_id, rule FROM rules WHERE type = 3";
						$result = mysql_query($query);
						
						while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
							
							$name = $row['rule'];
							$beg = strpos($name, "Possible detection of: ") + 23;
							$end = strpos($name, "\";", $beg + 2);
							$end = $end - $beg;
							$name = substr($name, $beg, $end);
							echo "<tr><td width=\"250\">" . $name . "</td><td><a href=\"display.php?type=free&id=" . $row['rule_id'] . "\">display</a> |</td><td><a href=\"includes/remove.php?type=free&id=" . $row['rule_id'] . "\">delete</a> |</td><td><a href=#>recalculate</a></td></tr>";
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
