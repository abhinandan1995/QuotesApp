<?php

require_once('secrets.php');

session_start();

if(isset($_GET['code'])){
	processCode($_GET['code']);
}
else if(isset($_GET['error']) && $_GET['error']=='access_denied'){
	header("Location: index.php?action=rerequest&type=login");
}
else{
	header("Location: index.php?action=fail&errno=0");	
}
exit();

function processCode($code){
	
	$response= getAccessToken($code);

	if($response){
		echo $response;
		$resp= inspectTokenRequest($response);	
     	if($resp){
     		print_r($resp);
     		validateToken($response, $resp);
     	}
     	else{
     		header("Location: index.php?action=fail&errno=1&error=Failed to confirm the identity of the user.");
			exit();
     	}
	}
	else{
		header("Location: index.php?action=fail&errno=2&error=Unable to get the access token.");
		exit();
	}
}

function getAccessToken($code){

	$query = http_build_query([
		'client_id'=> CLIENT_ID,
		'redirect_uri'=> HOME_URL."redirect.php",
		'client_secret'=> CLIENT_SECRET,
		'code' => $code
	]);

	$url = "https://graph.facebook.com/v2.9/oauth/access_token?".$query;

	return makeCurlRequest($url);
}

function inspectTokenRequest($response){

	$resArray= json_decode($response);
		
		$query = http_build_query([
		'input_token'=>$resArray->access_token,
		'access_token'=>APP_ACCESS_TOKEN
		]);

		$url= "https://graph.facebook.com/debug_token?".$query;
     	return makeCurlRequest($url);
}

function validateToken($response, $resp){

	$rArray= json_decode($resp);

	if(!$rArray->data->app_id==CLIENT_ID || !$rArray->data->is_valid){
		header("Location: index.php?action=fail&errno=3&error=Invalid Request");
		exit();
	}

	if(!in_array("publish_actions", $rArray->data->scopes)){
		header("Location: index.php?action=rerequest&type=publish_actions");
		exit();
	}

	$resArray= json_decode($response);
	$_SESSION['is_valid']= true;
	$_SESSION['access_token']= $resArray->access_token;

	header("Location: index.php?action=success");
	exit();
}

function makeCurlRequest($url, $return=1){

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url); 

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, $return); 

	//SSL certificate to be obtained to disable using this. 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	$output = curl_exec($ch);   
	
	if(curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
	  var_dump($output);
	  return;
	}

	curl_close($ch);
	return $output;
}

?>