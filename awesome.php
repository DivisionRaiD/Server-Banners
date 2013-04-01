<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: Server Banners
//
// Description: Server banner code.
//              Default requirements can be obtained
//              from: http://momo.blackpulse.us/banner/
//
// Note: Formerly named banner.php, but it got recognized as ad. 
//       Renamed to awesome.php uppon Storm's request.
//
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

//------------------------------------------------------------------------------------------------------------+

include( 'code/game-cod.php' );
include( 'code/game-minecraft.php' );
include( 'code/game-samp.php' );
include( 'code/banner-print.php' );
include( 'code/banner-functions.php' );
include( 'code/banner-core.php' );

if ( $_GET[ 'debug' ] != "1" )
    header( "Content-Type: image/png" );


$root = "images/"; 			//Folder where to get the images
$font = "fonts/font.ttf";	//Folder where to get the font

banner();

//------------------------------------------------------------------------------------------------------------+
?>