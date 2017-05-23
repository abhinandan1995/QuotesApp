<?php

require_once('params.php');

session_start();

if(isset($_GET['action']) &&  $_GET['action']=='rerequest'  && isset($_GET['type'])){
	requestPermissions($_GET['type']);
	exit();
}

if(isset($_GET['action']) &&  $_GET['action']=='fail'  && isset($_GET['errno'])){
	displayError((int)$_GET['errno']);
	exit();
}

if(isset($_GET['action']) &&  $_GET['action']=='retry'  && isset($_GET['type'])){
	makeRequests($_GET['type']);
	exit();
}

if(isset($_GET['action']) &&  $_GET['action']=='success'){
	getTheImage();
	exit();
}

if(isset($_SESSION['is_valid']) && $_SESSION['is_valid'] && isset($_SESSION['access_token'])){
	getTheImage();
	exit();
}
else{
	makeRequests('login');
}

function requestPermissions($type){

	if($type=='login'){
		echo "User didn't accept the login request. Login to run the app.";
		retryLink(1);
		return;
	}
	if($type=='publish_actions'){
		echo "User needs to enable the app to post on his behalf. Provide the permissons asked.";
		retryLink(2);
		return;
	}
}

function displayError($errno){

	switch ($errno) {

		case 1:
			echo "Failed to confirm the identity of the user";
			break;
		
		case 2:
			echo "Unable to get the access token";
			break;

		case 3:
			echo "Invalid request";
			break;

		default:
			echo "Insufficient permissions to perform the task";
			break;
	}

	retryLink(1);
}

function retryLink($type){
	if($type==1){
		echo "<p><a href='index.php?action=retry&type=login'>Login</a></p>";
	}
	if($type==2){
		echo "<p><a href='index.php?action=retry&type=publish_actions'>Request permissons</a></p>";	
	}
}

function getTheImage(){
	echo "Succes. Start getting the image.";
}

function makeRequests($type){

	if($type=='login'){
		$query = http_build_query([
		'client_id'=> CLIENT_ID,
		'redirect_uri'=> HOME_URL."redirect.php"
		]);

		header("Location: https://www.facebook.com/v2.9/dialog/oauth?".$query);
		return;
	}

	if($type=='publish_actions'){
		$query = http_build_query([
		'client_id'=> CLIENT_ID,
		'redirect_uri'=> HOME_URL."redirect.php",
		'scope' => 'email,publish_actions'
		]);

		header("Location: https://www.facebook.com/v2.9/dialog/oauth?".$query);
		return;
	}
}

?>