<html>
<head>
<title>
Login page
</title>
</head>
<body>

<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '1372213786195464',
      cookie     : true,
      xfbml      : true,
      version    : 'v2.8'
    });
    FB.AppEvents.logPageView();  

    FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
}); 
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));





function statusChangeCallback(object){
	console.log(object);
	FB.login(function(response) {
   console.log(response);
}, {scope: 'email'});
}

</script>

<fb:login-button 
  scope="public_profile,email"
  onlogin="checkLoginState();">
</fb:login-button>

</body>
</html>