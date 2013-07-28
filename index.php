<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: Server Banners
//
// Description: Server banner code for various types of games.
//
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

define( "BANNER_CALL", TRUE );

//------------------------------------------------------------------------------------------------------------+

include( 'code/core.php' );

startDebugLog();

if ( !isToDebug() )
	header( "Content-Type: image/png" );

$_game = NULL;
$root  = "images/"; //Folder where to get the images
$font  = "fonts/font.ttf"; //Font file for the text

banner();

endDebugLog();

//------------------------------------------------------------------------------------------------------------+
?>