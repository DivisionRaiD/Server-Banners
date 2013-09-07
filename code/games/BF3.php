<?php
//------------------------------------------------------------------------------------------------------------+
//
// Name: BF3.php
//
// Description: Code to parse Battlefield 3 servers
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
// Note: Main algorithm by Richard Pery, copied from LGSL!
//
//------------------------------------------------------------------------------------------------------------+

if ( !defined( "BANNER_CALL" ) ) {
	exit( "DIRECT ACCESS NOT ALLOWED" );
}

//------------------------------------------------------------------------------------------------------------+
//Query BF3 server - main function!

function query( $ip, $port )
{
	include("dependencies/BF.php");
	
	$_return = queryMain( $ip, $port );
	
	if( $_return["value"] == -1)
		$_return = queryMain( $ip, intval($port) + 22000 );
		
	$_return["protocol"] = "BF3";
	
	return $_return;
}

//------------------------------------------------------------------------------------------------------------+
?>