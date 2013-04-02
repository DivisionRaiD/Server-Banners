<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: game-minecraft.php
//
// Description: Code to parse Minecraft servers
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

//------------------------------------------------------------------------------------------------------------+
//Query minecraft servers

function queryMC( $ip, $port )
{
    $server  = "tcp://" . $ip; //UDP or TCP. Fuck idk!
    $connect = @fsockopen( $server, $port, $errno, $errstr, 2 );
    $cmd      = "\xFE\xFD\x09\x01\x02\x03\x04"; //What's the cmd. Idk :( 
    if ( !$connect )
        return getErr( $ip, $port );
    
    
    else {
        fwrite( $connect, $cmd );
        stream_set_timeout( $connect, 2 );
        $output = fread( $connect, 8192 );
        $info   = stream_get_meta_data( $connect );
        if ( $_GET[ 'debug' ] == "1" )
            echo "<big><u>Server response:</u></big><br><br>" . $output . "<br><br><big><u>PNG output:</u></big><br><br>";
        
        if ( !$output || !isset( $output ) || $output == "" || $info[ 'timed_out' ] ) {
                return getErr( $ip, $port );
        }
        else
		{
		/*	$output = Pack( 'N', $output );
			fwrite($connect , "\xFE\xFD\x00" . $output . "\x01\x02\x03\x04\x01\x02\x03\x04"); //WTF? 
			$output = fread( $connect, 8192 ); */
			fclose( $connect );
		}
        return parseMCQueryData( $output, $ip, $port );
    }
}


//------------------------------------------------------------------------------------------------------------+
//Parse the query data and return it as array - How to parse something, if there is nothing to parse :(

function parseMCQueryData( $input, $ip, $port )
{
    $server = $ip . ":" . $port;
    $err    = "-";
    
    if ( $input == "-1" )
        $data = getErr( $ip, $port );
    
    else {
		$input      = str_replace( "\x00", "", $input );
		$hostname   = substr( $input, 2, strpos( $input, "\xA7")-1 );
		$input      = substr( $input, strpos( $input, "\xA7") + 1 );
		$players    = substr( $input, 0, strpos( $input, "\xA7") );
        $maxplayers = substr( $input, strpos( $input, "\xA7") + 1);
        $mapname    = "World";
        $gametype   = "SMP";
        $protocol   = "MC";
        $value      = 1;
		
        $data = array(
            "value" => $value,
            "hostname" => $hostname,
            "gametype" => $gametype,
            "protocol" => $protocol,
            "clients" => $players,
            "maxclients" => $maxplayers,
            "mapname" => $mapname,
            "server" => $server,
            "unclean" => $hostname
        );
    }
    
    return $data;
}
//------------------------------------------------------------------------------------------------------------+
?>