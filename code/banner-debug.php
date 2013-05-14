<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: banner-debug.php
//
// Description: Code for debugging view of the banners
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

//------------------------------------------------------------------------------------------------------------+
//Define global variables

$startTime = 0;
$genTime   = 0;
$genData   = array();

//------------------------------------------------------------------------------------------------------------+
//Returns boolean value if banners should be debugged

function isToDebug()
{
	if( isSet( $_GET['debug'] ) && $_GET['debug'] != "0" )
		return true;
		
	return false;
}

//------------------------------------------------------------------------------------------------------------+
//Start debug-logging

function startDebugLog()
{
	global $startTime;
	
	$startTime = MicroTime( true );
	
	if( isToDebug() )
		startHeaderCall();
}

//------------------------------------------------------------------------------------------------------------+
//End debug-logging

function endDebugLog()
{
	global $startTime, $genTime;
	
	$genTime = Number_Format( ( MicroTime( true ) - $startTime ), 4, '.', '' );
	
	startBodyCall();
}

//------------------------------------------------------------------------------------------------------------+
//Echo the debug header

function setDebugData( $data )
{
	global $genData;
	
	$genData = $data;
}

//------------------------------------------------------------------------------------------------------------+
//Echo the debug header

function startHeaderCall()
{
	echo "<html><head><link rel=\"stylesheet\" href=\"css/debugStyle.css\" /></head><body>";
	echo "<big><big>Generation errors:</big></big> \n<br>";
}

//------------------------------------------------------------------------------------------------------------+
//Echo the debug body

function startBodyCall()
{
	global $genData, $genTime;
		
	echo "<br><br><big><big>Debug Data:</big></big> \n<br><br>";
	echo "<table border\"1\"><tbody>";
	
	foreach ( $genData as $key => $value)
	{
		echo "<tr><td>{$key}</td><td>{$value}</td></tr>";
	}
	
	echo "<tr><td>Generation Time</td><td>{$genTime}</td></tr>";
	echo "</tbody></table>";
	echo "</body></html>";
}

//------------------------------------------------------------------------------------------------------------+
?>