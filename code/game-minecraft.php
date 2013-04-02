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
    $cmd      = "\0xFE\0xFD"; //What's the cmd. Idk :( 
    if ( !$connect )
        return getErr( $ip, $port );
    
    
    else {
        fwrite( $connect, $cmd );
        stream_set_timeout( $connect, 2 );
        $output = fread( $connect, 8192 );
        $info   = stream_get_meta_data( $connect );
        fclose( $connect );
        if ( $_GET[ 'debug' ] == "1" )
            echo "<big><u>Server response:</u></big><br><br>" . $output . "<br><br><big><u>PNG output:</u></big><br><br>";
        
        if ( !$output || !isset( $output ) || $output == "" || $info[ 'timed_out' ] ) {
                return getErr( $ip, $port );
        }
        
        return parseMCQueryData( $output, $ip, $port );
    }
}


//------------------------------------------------------------------------------------------------------------+
//Parse the query data and return it as array - How to parse something, if there is nothing to parse :(

function parseMCQueryData( $input, $ip, $port )
{
	echo $input;
    $server = $ip . ":" . $port;
    $err    = "-";
    
    if ( $input == "-1" )
        $data = getErr( $ip, $port );
    
    else {
	
        $maxplayers = "";
        $mapname = "";
        $hostname = "";
        $gametype = "";
        $protocol = "";
        
        $data = array(
             "value" => 1,
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