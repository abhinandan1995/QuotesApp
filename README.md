# QuotesApp

WebApp that fetches a quote and posts it on your facebook timeline. It fetches the quote as a text and creates the post as a photo. 

Supports :- getting the permissions from the users easily. 
            multiple backgrounds for quotes.
            ensures no user posts the same quote again.
            Highly secure.
            
Working:- First it tries to log the user in to facebook. If user grants the permission to create posts, then the app fetches the quote, 
          transforms it to a picture and posts it! 
          
Usage:- Clone the repo. Use an apache php server (Wamp/ XAMPP). Open the index.php page with a userID parameter and follow the instructions and if after a while you see the message "Photo posted successfully!". Means its working.

Eg. index.php?userID=xxxx
eg. userID=12345. Any 1- 10 digit numeric value. 

Modify the home_url in params.php and secrets.php to host urls where your test site is running. 
