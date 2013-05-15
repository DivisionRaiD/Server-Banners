<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: banner-code.php
//
// Description: Core code/setup code for the banners.
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

//------------------------------------------------------------------------------------------------------------+

include( 'game-cod.php' );
include( 'game-minecraft.php' );
include( 'game-samp.php' );
include( 'game-bfbc2.php' );
include( 'banner-print.php' );
include( 'banner-debug.php' );

//------------------------------------------------------------------------------------------------------------+
//Setup a banner

function banner( )
{
    //Get basic information
    $ip   = getIP();
    $port = getPort();
    
    setLocalGame( $_GET[ 'game' ], $game );
    
    $info = @call_user_func( "query" . $game, $ip, $port );
    
    verifyInformation( $info );
    
    printimage( $info );
}

//------------------------------------------------------------------------------------------------------------+
?>
