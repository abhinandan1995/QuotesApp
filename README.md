QuotesApp
===================

This webapp fetches a quote and posts it on the logged in user's facebook timeline. It fetches the quote as a text and creates the post as a photo.

# Supports :- 
* Getting the first time post creation permissions from the users easily. 
* Multiple backgrounds for quotes.
* Ensures for a particular user, same content is not posted again.
* Highly secure.
            
# Working:- 
First it tries to log the user in to facebook. If user grants the permission to create posts, then the app fetches the quote, 
transforms it to a picture and posts it! 
          
# Usage:- 
Clone the repo. Use an apache php server (Wamp/ XAMPP). Open the index.php page with a userID parameter and follow the instructions and if after a while you see the message "Photo posted successfully!". Means its working.

Eg. index.php?userID=xxxx
eg. userID=12345. Any 1 - 10 digit numeric value. 

Modify the home_url in params.php and secrets.php to host urls where your test site is running. 
