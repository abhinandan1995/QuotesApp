<?php

function getQuoteObject(){

$content= file_get_contents("http://quotesondesign.com/wp-json/posts?filter[orderby]=rand&filter[posts_per_page]=1");
$contentArray= json_decode($content);
return $contentArray[0];

}

?>