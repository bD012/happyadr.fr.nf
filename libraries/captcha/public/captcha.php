<?php
session_start();

define('WIDTH', {{WIDTH}});
define('HEIGHT', {{HEIGHT}});
define('CODE_LENGTH', {{CODE_LENGTH}});
$chars = '0123456789';

$image = imagecreate(WIDTH, HEIGHT);
imagecolortransparent($image, imagecolorallocatealpha($image, 0, 0, 0, 0));

// Color palette
for($i = 0; $i < 256; $i++) {
	$colors[$i] = imagecolorallocate($image, $i, $i, $i);
}

// Code
$code = '';
for ($i = 0; $i < CODE_LENGTH; $i++) {
	$code .= $chars{ mt_rand( 0, strlen($chars) - 1 ) };
}

// Session
$_SESSION['CAPTCHA'] = md5($code);

// Backgound
$imax = (WIDTH * HEIGHT) * 0.66;
for($i=0; $i<$imax; $i++) {
	imagesetpixel($image, mt_rand(0, WIDTH), mt_rand(0, HEIGHT), $colors[mt_rand(155, 255)]);
} // for

// Captcha
$fontsize = HEIGHT/2.2;
$y = HEIGHT/1.6;

for($i = 0; $i < CODE_LENGTH; $i++) {
	$fontrotate = mt_rand(-40, 40);
	$x = ($y/2) + $i*( (WIDTH - $y) / CODE_LENGTH);

	imagettftext($image, $fontsize, $fontrotate, $x, $y, $colors[mt_rand(12,96)], 'FFF_Tusj.ttf', $code[$i]);
} // for

// Ouput
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);