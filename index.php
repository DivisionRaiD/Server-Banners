<?php
//------------------------------------------------------------------------------------------------------------+
//
// Name: Server Banners
//
// Description: Server banner code for various types of games.
//
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

error_reporting(E_ALL ^  E_NOTICE); 
define( "BANNER_CALL", TRUE );

//------------------------------------------------------------------------------------------------------------+

include( 'code/core.php' );

startDebugLog();

if ( !isToDebug() )
	header( "Content-Type: image/png" );

$_game = NULL;
$root  = "images/"; // Folder where to get the images
$font  = "fonts/font.ttf"; // Font file for the name
$f_inf = "fonts/contl.ttf"; // Font file for the info

$f_inf = $font; // Looks good on some servers, on others it doesn't :P

banner();

endDebugLog();

//------------------------------------------------------------------------------------------------------------+
?>