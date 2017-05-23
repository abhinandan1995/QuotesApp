<?php

require_once('getquote.php');
require_once('image.php');

if(isset($_GET['userID'])){

	$user= (int)$_GET['userID'];
	$idArray= readUserFile($user);

	getNewQuote($user, $idArray);
}
else{
	echo "Forbidden. Invalid request";
	exit();
}

function readUserFile($user){

$file= "data/{$user}.txt";
if(file_exists($file)){
	$ids= explode(",",file_get_contents($file));

	$idArray= array();
	foreach ($ids as $id) {
		$id= trim($id);
		if($id)
		$idArray[$id]= $id;
	}
	return $idArray;
}
file_put_contents($file, "");
return array();
}

function getNewQuote($user, & $idArray){

$loopControl= 0;
$quote= "";

while($loopControl++ < 10){
	$quote= getQuoteObject();
	if(isNewQuote($idArray, $quote)){
		break;
	}
}
showImage($user, $quote);
updateIndexes($idArray, $user, $quote);
}

function isNewQuote(& $idArray, $quote){
	if(isset($idArray[$quote->ID])){
		return false;
	}
	return true;
}

function updateIndexes(& $idArray, $user, $quote){
	$idArray[$quote->ID]= $quote->ID;
	file_put_contents("data/{$user}.txt", "{$quote->ID},", FILE_APPEND);
}

function showImage($user, $quote){
	$path= createImage($user, $quote);
	echo "<img src='{$path}' />";
	echo $quote->content;
	echo "<br />".strlen($quote->content);
}

?>