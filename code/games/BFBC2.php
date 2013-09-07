<?php
//------------------------------------------------------------------------------------------------------------+
//
// Name: BFBC2.php
//
// Description: Code to parse BFBC2 servers
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
	
	if( $_return["value"] == -1 && ($port . "") != "48888" )
		$_return = queryMain( $ip, 48888 );
		
	$_return["protocol"] = "BFBC2";
	
	return $_return;
}

//------------------------------------------------------------------------------------------------------------+
?>