<?php include("includes/checklogin.php")?>
<?php 
	$regex = $_POST["regex"];
	$string = $_POST["string"];
	$display = false;
	$match = false;
	$totalTime;
	if ($regex != "" AND $string != ""){
		$display = true;
		
		$time1 = time();
		sleep(10);
	
		if(preg_match($regex, $string)){
			$match = true;
		} else {
			$match = false;
		}
		$time2 = time();
		
		$totalTime = $time2 - $time1;
	}
	
	
	
	
	
?>
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
					<form action="testRegex.php" method="post">
						<b>Regex: </b> <input type="text" id="regex" name="regex" /><br>
						<b>String: </b> <input type="text" id="string" name="string" /><br>
						<input type="submit" id="submit" value="submit" />
					</form>
					<br>
					<br>
					<?php 
						if($display == true){
							echo "Total time to execute: <b>$totalTime</b><br>";
							echo "Found: $match";
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
