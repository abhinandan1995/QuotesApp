<?php

require_once('getquote.php');
require_once('image.php');

if(isset($_SESSION['userID'])){

	$user= $_SESSION['userID'];
	$idArray= readUserFile($user);

	$path= getNewQuote($user, $idArray);
	$_SESSION["path_{$user}"]= $path;
}
else{
	header("Location: index.php?action=fail&errno=4&error=Invalid access key");
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
	updateIndexes($idArray, $user, $quote);
	return makeImage($user, $quote);
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

function makeImage($user, $quote){
	$path= createImage($user, $quote);
	return $path;
}
