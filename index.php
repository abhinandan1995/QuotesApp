<?php

require_once('params.php');

session_start();

if(isset($_GET['userID'])){

	if(isset($_SESSION['userID'])){
		if($_SESSION['userID'] != $_GET['userID']){
			$_SESSION= array();
		}
	}
	$_SESSION['userID']= $_GET['userID'];
}

if(!isset($_GET['userID']) && !isset($_SESSION['userID'])){
	echo "Please get a valid access key to access the service!";
	exit();
}

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

		case 4:
			echo "Invalid access key!";
			break;

		case 5:
			echo "Unable to create the post!";
			break;

		case 6:
			echo "Please login again!";
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
	require_once('getphoto.php');
	postPhoto();
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

function postPhoto(){

	if(!CREATE_POST){
		echo "<p>Post formed successfully but denied permission by the admin. Please check the settings</p>";
		exit();
	}

	$cfile = new CURLFile(realpath($_SESSION["path_".$_SESSION['userID']]),'image/png','Quotes_photo.png'); 
	$ch = curl_init();
	$data = array('caption' => 'From Quotes app.', 
		'image' => $cfile,
		'access_token' => $_SESSION['access_token']);

	curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v2.9/".$_SESSION['fb_user']."/photos");
	curl_setopt($ch, CURLOPT_POST, 1);
	
	curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

	//SSL certificate to be obtained to disable using this. 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	$output = curl_exec($ch);   
	
	if(curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
	  header("Location: index.php?action=fail&errno=5&error=Unable to create the post");
	  exit();
	}
	curl_close($ch);

	$outClass= json_decode($output);
	if($outClass->id && $outClass->post_id){
		file_put_contents("data/posted/".$_SESSION['userID'].".txt", $outClass->id.",", FILE_APPEND);
		echo "<p>Photo posted successfully!</p>";
		exit();
	}
	else{
		header("Location: index.php?action=fail&errno=6&error=Please login again");
	  	exit();
	}
}

?>