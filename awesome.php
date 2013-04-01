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
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
// Version: 5.3
// License: Check the readme.md
// Credit:
//      -PHP.net as a helpful resource
//      -PixelDemon/alexdahlem for his amazing gdlib tut
//      -BlooDONE for his background watermark
//      -aG`Avail for the online generator (http://momo5504.square7.de/banner.html)
//      -icedream for the api
//      -Richard Perry for some LGSL images and his commenting system^^
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


$root = "images/"; //Folder where to get the images
$font = "fonts/font.ttf"; //Folder where to get the font

banner( $root, $font );

//------------------------------------------------------------------------------------------------------------+
?>