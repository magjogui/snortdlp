<?php 
include("includes/common.php");

$string = "the this is a test the this is a test test the this is a test test test test";

$words = explode(" ", $string); //split our standardized input by spaces
$histogram = array_count_values($words); //return an arrray of occurances

//selectSubstringHistogram($histogram, $inputText, $substringLength)
print_r($histogram);
echo "<br>";
$substring = selectSubstringHistogram($histogram, $string, 7);
echo "$substring<br>";

$sql = array(); 
foreach( $histogram as $word => $count ) {
    $sql[] = '("'.$word.'", '.$count.')';
}

$query = 'INSERT INTO table (text, category) VALUES '.implode(',', $sql);
echo $query;


/*
$histogramScores = scoreHistogram($histogram);
print_r($histogramScores);

$repositoryScore = 0;
$words = explode(" ", $string);

foreach($words as $word){
	$repositoryScore += $histogramScores[$word];
}

echo "<br>repository score = $repositoryScore<br>";*/

?>