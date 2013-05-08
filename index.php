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

//------------------------------------------------------------------------------------------------------------+

$Timer = MicroTime( true );

include( 'code/banner-core.php' );

if ( $_GET[ 'debug' ] != "1" )
    header( "Content-Type: image/png" );
//header( "Content-Type: image/jpeg" );


$root = "images/"; //Folder where to get the images
$font = "fonts/font.ttf"; //Folder where to get the font

banner();

if ( $_GET[ 'debug' ] == "1" )
    echo "\n\n<br><br>Generated in " . Number_Format( ( MicroTime( true ) - $Timer ), 4, '.', '' ) . "s";

//------------------------------------------------------------------------------------------------------------+
?>
