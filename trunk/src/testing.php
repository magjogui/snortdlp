<?php 
include("includes/common.php");

$string = "this is a test this is a second test test"; //1872

$words = explode(" ", $string); //split our standardized input by spaces
$histogram = array_count_values($words); //return an arrray of occurances

print_r($histogram);
echo "<br>";
$histogram = scoreHistogram($histogram);
print_r($histogram);
echo "<br>";

//selectSubstringHistogram($histogram, $inputText, $substringLength)
$substring = selectSubstringHistogram($histogram, $string, 4);
echo "<br>$substring<br>";

$sql = array();
		
foreach($histogram as $word => $count){
	$sql[] = '("'.$word.'", '.$count.')';
}
$blah = array_merge(array_keys($histogram),array_values($histogram));
//echo implode(',', $histogram);
print_r($blah);

?>