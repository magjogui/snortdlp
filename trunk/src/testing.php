<?php

include("includes/common.php");

echo "score = " . repositoryScore('blah');

$string = "test test test";
if (inRepository($string)){
	echo "<br><br>$string in repository";
}

?>