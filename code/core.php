<?php
//------------------------------------------------------------------------------------------------------------+
//
// Name: core.php
//
// Description: Core code/setup code for the banners.
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

if ( !defined( "BANNER_CALL" ) ) {
	exit( "DIRECT ACCESS NOT ALLOWED" );
}

//------------------------------------------------------------------------------------------------------------+

include( 'analyzeGD.php' );
include( 'print.php' );
include( 'debug.php' );

//------------------------------------------------------------------------------------------------------------+
//Setup a banner

function banner( )
{
	//Get basic information
	$ip   = getIP();
	$port = getPort();
	
	global $_game;
	
	setLocalGame( $_GET[ 'game' ], $_game );
	
	@include( 'games/' . $_game . '.php' );
	
	$info = @call_user_func( "query", $ip, $port );
	
	verifyInformation( $info );
	
	printimage( $info );
}

//------------------------------------------------------------------------------------------------------------+
?>
