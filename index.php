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
//       Renamed to index.php uppon Storm's request.
//
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

define("BANNER_CALL", TRUE);

//------------------------------------------------------------------------------------------------------------+

include( 'code/core.php' );

startDebugLog();

if ( !isToDebug() )
    header( "Content-Type: image/png" );


$root  = "images/"; //Folder where to get the images
$font  = "fonts/font.ttf"; //Font file for the text

banner();

endDebugLog();

//------------------------------------------------------------------------------------------------------------+
?>
