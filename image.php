<?php

function createImage($user, $quote){

$text= strip_tags($quote->content);
$font = 'fonts/times.ttf';
$font_size = 24;
$font_angle= 0;

$width= 600;
$height= 400;

$chars= $width*5/ (2 *($font_size+ 2*$font_size/3)) + 4;
$lines = explode('|', wordwrap($text, $chars, '|'));

$linesCount= sizeof($lines);
$reqY= ($font_size + 7)* $linesCount;

// Starting Y position
$y = ($height- $reqY)/2 + 15;

$im = imagecreatetruecolor($width, $height);

// Create some colors
$bcolors= array(array(150, 250, 150), array(100, 175, 250), array(225, 150, 75), array(200, 150, 100), array(174, 216, 230), array(75, 150, 175));
$index= mt_rand(0, sizeof($bcolors) - 1);

$color = imagecolorallocate($im, $bcolors[$index][0], $bcolors[$index][1], $bcolors[$index][2]);
$grey = imagecolorallocate($im, 128, 128, 128);
$black = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, $width-1, $height-1, $color);


// Loop through the lines and place them on the image
foreach ($lines as $line)
{
	$x= ($width - strlen($line)*($font_size/2 + 1))/2;
	imagettftext($im, $font_size, 0, $x, $y, $grey, $font, $line);
    imagettftext($im, $font_size, 0, $x, $y, $black, $font, $line);
    // Increment Y so the next line is below the previous line
    $y += ($font_size+7);
}
imagettftext($im, $font_size-($font_size/3), 0, ($width- strlen($quote->title)* $font_size * 2 /3), $y, $black, $font, "- ".$quote->title);

	imagepng($im, "temp/{$user}.png");
	imagedestroy($im);
	return "temp/{$user}.png";
}

?> 