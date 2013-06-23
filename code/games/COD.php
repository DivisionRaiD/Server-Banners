<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: game-cod.php
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

function queryCOD( $ip, $port )
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
            return substr( $output, 4 );
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
    
    if ( strpos( $input, "\\clients" ) )
        $players = substr( $input, strpos( $input, "\\clients" ) + 9 );
    
    else
        $players = getMissingPlayers( $ip, $port );
    
    $gametype   = substr( $input, strpos( $input, "\\gametype" ) + 10 );
    $maxplayers = substr( $input, strpos( $input, "\\sv_maxclients" ) + 15 );
    $mapname    = substr( $input, strpos( $input, "\\mapname" ) + 9 );
    $protocol   = substr( $input, strpos( $input, "\\protocol" ) + 10 );
    $hostname   = substr( $input, strpos( $input, "\\hostname" ) + 10 );
    
    cleanFromRest( $mapname );
    cleanFromRest( $hostname );
    cleanFromRest( $gametype );
    cleanFromRest( $maxplayers );
    cleanFromRest( $protocol );
    cleanFromRest( $players );
    
    //Get a clean hostname without '^1's or '^5's
    $unclean = $hostname;
    
    for ( $i = 0; $i < 10; $i++ )
        $hostname = str_replace( "^{$i}", "", $hostname );
		
	$hostname = str_replace( "^:", "", $hostname );
	$hostname = str_replace( "^;", "", $hostname );
    
    $value = 1;
    
    //Put information into an array
    $data = array(
         "value" => $value,
        "hostname" => $hostname,
        "gametype" => $gametype,
        "protocol" => $protocol,
        "clients" => $players,
        "maxclients" => $maxplayers,
        "mapname" => $mapname,
        "server" => $ip . ":" . $port,
        "unclean" => $unclean,
        "response" => $input 
    );
    
    
    return $data;
}

//------------------------------------------------------------------------------------------------------------+
//Clean input part from their rest behind

function cleanFromRest( &$self )
{
    if ( strpos( $self, "\\" ) )
        $self = substr( $self, 0, strpos( $self, "\\" ) );
}

//------------------------------------------------------------------------------------------------------------+
//Get the players - only for getstatus query

function getPlayers( $input )
{
    $player_str = substr( $input, strpos( $input, "\n" ) + 1, strlen( $input ) );
    $player_str = substr( $player_str, strpos( $player_str, "\n" ) + 1, strlen( $player_str ) );
    $players    = array( );
    $ZOB        = substr_count( $player_str, "\n" );
    $tok        = strtok( $player_str, "\"" );
    
    for ( $k = 1; $k <= $ZOB; $k++ ) {
        $score = substr( $tok, 0, strpos( $tok, " " ) );
        
        if ( substr( $score, 0, 1 ) == " " || substr( $score, 0, 1 ) == "\n" )
            $score = substr( $score, 1, strlen( $score ) );
        
        $ping = substr( $tok, strpos( $tok, " " ) + 1, strlen( $input ) );
        
        if ( substr( $ping, 0, 1 ) == " " || substr( $ping, 0, 1 ) == "\n" )
            $ping = substr( $ping, 0, strlen( $ping ) - 1 );
        
        $tok  = strtok( "\"\n" );
        $name = $tok;
        
        $p_array = array(
             name => $name,
            score => $score,
            ping => $ping 
        );
        
        array_push( $players, $p_array );
        
        $tok = strtok( "\"" );
    }
    return $players;
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