<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: MC.php
//
// Description: Code to parse Minecraft servers
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

if ( !defined( "BANNER_CALL" ) ) {
	exit( "DIRECT ACCESS NOT ALLOWED" );
}

//------------------------------------------------------------------------------------------------------------+

include( 'xPaw/MinecraftQuery.class.php' );

//------------------------------------------------------------------------------------------------------------+
//Query minecraft servers

function query( $ip, $port )
{
	$Query = new MinecraftQuery();
	
	try {
		$Query->Connect( $ip, $port, 1 );
		$Info = $Query->GetInfo();
		
		if ( !$hostname = $Info[ 'HostName' ] )
			$hostname = "-";
		
		if ( !$gametype = $Info[ 'GameType' ] )
			$gametype = "-";
		
		if ( !$mapname = $Info[ 'Map' ] )
			$mapname = "-";
		
		if ( !$players = $Info[ 'Players' ] )
			$players = "-";
		
		if ( !$maxplayers = $Info[ 'MaxPlayers' ] )
			$maxplayers = "-";
		
		
		if ( $players == "-" ) {
			$newplayers = $Query->GetPlayers();
			
			if ( count( $newplayers ) )
				$players = count( $newplayers );
		}
		
		$data = array(
			 "value" => "1",
			"hostname" => cleanHostname( $hostname ),
			"gametype" => $gametype,
			"protocol" => "MC",
			"clients" => $players,
			"maxclients" => $maxplayers,
			"mapname" => $mapname,
			"server" => $ip . ":" . $port,
			"unclean" => $hostname 
		);
	}
	
	catch ( MinecraftQueryException $e ) {
		$data = getErr( $ip, $port );
	}
	
	return $data;
}

//------------------------------------------------------------------------------------------------------------+
//Clean hostname from color codes

function cleanHostname( $name )
{
	$hostname = $name;
	
	for ( $i = 0; $i < 10; $i++ )
		$hostname = str_replace( "&{$i}", "", $hostname );
	
	$hostname = str_replace( "&a", "", $hostname );
	$hostname = str_replace( "&b", "", $hostname );
	$hostname = str_replace( "&c", "", $hostname );
	$hostname = str_replace( "&d", "", $hostname );
	$hostname = str_replace( "&e", "", $hostname );
	$hostname = str_replace( "&f", "", $hostname );
	
	$hostname = str_replace( "&k", "", $hostname );
	$hostname = str_replace( "&l", "", $hostname );
	$hostname = str_replace( "&m", "", $hostname );
	$hostname = str_replace( "&n", "", $hostname );
	$hostname = str_replace( "&o", "", $hostname );
	
	$hostname = str_replace( "&r", "", $hostname );
	
	return $hostname;
}

//------------------------------------------------------------------------------------------------------------+
?>