<?php
//------------------------------------------------------------------------------------------------------------+
//
// Name: analyzeGD.php
//
// Description: Checks GD Library support
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

if ( !defined( "BANNER_CALL" ) ) {
	exit( "DIRECT ACCESS NOT ALLOWED" );
}

//------------------------------------------------------------------------------------------------------------+
//Analyzes GD Support

$errors = array();
	
if( !function_exists('gd_info') )
	$errors[ count( $errors ) ] = "GD Library is not installed!";
	
else
{
	$gddump = gd_info();
	
	if( !isSet( $gddump[ "JPEG Support" ] ) || !$gddump[ "JPEG Support" ] )
		$errors[ count( $errors ) ] = "JPEG image format is not supported by the installed GD Library";

	if( !isSet( $gddump[ "PNG Support" ] ) || !$gddump[ "PNG Support" ] )
		$errors[ count( $errors ) ] = "JPEG image format is not supported by the installed GD Library";
}
	
if( count( $errors ) > 0 )
{
	echo "<big><big>Error</big></big>\n<br>";
	
	foreach( $errors as $error )
		echo $error . "\n<br>";
			
	die("");
}

//------------------------------------------------------------------------------------------------------------+
?>