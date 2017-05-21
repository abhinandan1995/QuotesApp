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
showImage($quote);
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

function getImage($quote){

$url = 'http://localhost/quotes/image.php';
$data = array('content' => $quote->content, 'title' => $quote->title);
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { /* Handle error */ }
echo "<img src='{$result}' />";

var_dump($result);
}

function showImage($quote){
	$img= createImage($quote->content);
	$img = base64_encode($img);
	$uri = "data:image/png;base64," . $img;
echo "<img src=" . $uri /* URI goes here */ . "alt=\"the image\" />";
}

?>