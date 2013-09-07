<?php
//------------------------------------------------------------------------------------------------------------+
//
// Name: COD.php
//
// Description: Code to parse COD servers (Quake3 based)
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

if ( !defined( "BANNER_CALL" ) ) {
	exit( "DIRECT ACCESS NOT ALLOWED" );
}

//------------------------------------------------------------------------------------------------------------+
//Query COD server - main function!

function query( $ip, $port )
{
	$cmd = "\xFF\xFF\xFF\xFFgetinfo\x00";
	
	return parseQueryData( getQueryData( $ip, $port, $cmd ), $ip, $port, $cmd );
}

//------------------------------------------------------------------------------------------------------------+
//Open socket connection, send and receive information, return it & close socket again

function getQueryData( $ip, $port, $send, $MW3 = true )
{
	$server  = "udp://" . $ip;
	$connect = @fsockopen( $server, $port, $errno, $errstr, 1 );

	if ( !$connect )
		return "-1";
	
	
	else {
		fwrite( $connect, $send );
		stream_set_timeout( $connect, 2 );
		$output = fread( $connect, 8192 );
		$info   = stream_get_meta_data( $connect );
		fclose( $connect );
		
		if ( !$output || !isset( $output ) || $output == "" || $info[ 'timed_out' ] ) {
			if ( $MW3 )
				return getMW3Port( $ip, $port, $send );
			else
				return "-1";
		}
		
		else
			return $output;
	}
}

//------------------------------------------------------------------------------------------------------------+
//MW3 servers use a different port system than other cods

function getMW3Port( $ip, $port, $cmd )
{
	if ( $port2 = str_replace( "\n", "", file_get_contents( "http://momo5504.square7.de/banner_stuff/MW3Port.php?ip=" . $ip . "&c_port=" . $port ) ) )
		if ( $port2 != "-1" )
			return getQueryData( $ip, $port2, $cmd, false );
	
	return "-1";
}

//------------------------------------------------------------------------------------------------------------+
//Parse the query data and return it as array

function parseQueryData( $input, $ip, $port, $cmd )
{
	if ( $input == "-1" )
		return getErr( $ip, $port );
	
	if ( !strpos( $input, "hostname" ) )
		$hostname = "Unknown Hostname";
	
	$data  = substr( $input, 18 );
	$data  = explode( "\\", $data );
	$_data = array( );
	
	for ( $i = 0; $i < count( $data ); $i++ ) {
		if ( $i % 2 == 1 ) {
			$_data[ $data[ $i - 1 ] ] = $data[ $i ];
		}
	}
	
	if ( isSet( $_data[ "clients" ] ) )
		$players = $_data[ "clients" ];
	
	else
		$players = getMissingPlayers( $ip, $port );
	
	$gametype   = $_data[ "gametype" ];
	$maxplayers = $_data[ "sv_maxclients" ];
	$mapname    = $_data[ "mapname" ];
	$protocol   = $_data[ "protocol" ];
	$hostname   = $_data[ "hostname" ];
	$isW2       = ( substr( $_data[ "shortversion" ], 0, 3 ) == "4.0" );
	
	$unclean = $hostname;
	
	StripColors( $hostname );
	StripColors( $gametype );
	
	//Put information into an array
	$data = array(
		 "value" => 1,
		"hostname" => $hostname,
		"gametype" => $gametype,
		"protocol" => $protocol,
		"clients" => $players,
		"maxclients" => $maxplayers,
		"mapname" => $mapname,
		"server" => $ip . ":" . $port,
		"unclean" => $unclean,
		"response" => $input,
		"isW2" => $isW2 
	);
	
	
	return $data;
}

//------------------------------------------------------------------------------------------------------------+
//Get the players - only for getstatus query

function getPlayers( $input )
{
	$data    = explode( "\n", $input );
	$players = array( );
	
	for ( $i = 1; $i < count( $data ) - 1; $i++ ) {
		$player     = array( );
		$score      = substr( $data[ $i ], 0, strpos( $data[ $i ], " " ) );
		$data[ $i ] = substr( $data[ $i ], strpos( $data[ $i ], " " ) + 1 );
		$ping       = substr( $data[ $i ], 0, strpos( $data[ $i ], " " ) );
		$data[ $i ] = substr( $data[ $i ], strpos( $data[ $i ], " " ) + 2 );
		$data[ $i ] = substr( $data[ $i ], 0, strlen( $data[ $i ] ) - 1 );
		
		$player[ 'name' ]  = $data[ $i ];
		$player[ 'score' ] = $score;
		$player[ 'ping' ]  = $ping;
		
		array_push( $players, $player );
	}
	
	return $players;
}

//------------------------------------------------------------------------------------------------------------+
//Remove color tags

function StripColors( &$var )
{
	for ( $i = 0; $i < 10; $i++ )
		$var = str_replace( "^{$i}", "", $var );
	
	$var = str_replace( "^:", "", $var );
	$var = str_replace( "^;", "", $var );
}

//------------------------------------------------------------------------------------------------------------+
//Gets the missing players from some cod4 servers

function getMissingPlayers( $ip, $port )
{
	$server  = "udp://" . $ip;
	$connect = @fsockopen( $server, $port, $errno, $errstr, 2 );
	
	if ( !$connect )
		return "-";
	
	
	else {
		$send = "\xFF\xFF\xFF\xFFgetstatus\x00";
		fwrite( $connect, $send );
		stream_set_timeout( $connect, 2 );
		$output = fread( $connect, 8192 );
		$info   = stream_get_meta_data( $connect );
		fclose( $connect );
		
		if ( !$output || !isset( $output ) || $output == "" || $info[ 'timed_out' ] )
			return "-";
		
		else {
			try {
				$players = count( getPlayers( $output ) );
				return $players;
			}
			
			catch ( Exception $e ) {
				return "-";
			}
		}
	}
}

//------------------------------------------------------------------------------------------------------------+
?>