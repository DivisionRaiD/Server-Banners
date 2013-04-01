<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: game-cod.php
//
// Description: Code to parse GTA SAMP servers
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
// Version: 1.0
// Credit:
//      -PHP.net as a helpful resource
//
//------------------------------------------------------------------------------------------------------------+

//------------------------------------------------------------------------------------------------------------+
//Query SAMP servers, parse them and return the array

function querySAMP( $ip, $port )
{
    
    $aIPAddr = explode( '.', $ip );
    $cmd     = "SAMP";
    $cmd .= chr( $aIPAddr[ 0 ] );
    $cmd .= chr( $aIPAddr[ 1 ] );
    $cmd .= chr( $aIPAddr[ 2 ] );
    $cmd .= chr( $aIPAddr[ 3 ] );
    $cmd .= chr( $port & 0xFF );
    $cmd .= chr( $port >> 8 & 0xFF );
    $cmd .= 'i';
    
    $server  = "udp://" . $ip;
    $connect = @fsockopen( $server, $port, $errno, $errstr, 2 );
    $fp      = $connect;
    $server  = $ip . ":" . $port;
    
    if ( !$connect || !$fp )
        $data = getErr( $ip, $port );
    
    
    else {
        
        fwrite( $fp, $cmd );
        stream_set_timeout( $connect, 2 );
        $output = fread( $fp, 11 );
        $info   = stream_get_meta_data( $connect );
        
        if ( !$output || !isset( $output ) || $output == "" || $info[ 'timed_out' ] )
            $data = getErr( $ip, $port );
        
        else {
            $is_passworded = ord( fread( $fp, 1 ) );
            $plr_count     = ord( fread( $fp, 2 ) );
            $firstval      = dechex( ord( fread( $fp, 1 ) ) );
            $maxplayers    = hexdec( dechex( ord( fread( $fp, 1 ) ) ) . $firstval );
            $max_plrs      = $maxplayers;
            $strlen        = ord( fread( $fp, 4 ) );
            $hostname      = fread( $fp, $strlen );
            $strlen        = ord( fread( $fp, 4 ) );
            $gamemode      = fread( $fp, $strlen );
            $strlen        = ord( fread( $fp, 4 ) );
            $mapname       = fread( $fp, $strlen );
            
            $data = array(
                 "value" => $output,
                "hostname" => $hostname,
                "gametype" => $gamemode,
                "protocol" => "SAMP",
                "clients" => $plr_count,
                "maxclients" => $max_plrs,
                "mapname" => $mapname,
                "server" => $server,
                "unclean" => $hostname 
            );
        }
    }
    if ( $connect )
        fclose( $connect );
    
    
    if ( $_GET[ 'debug' ] == "1" )
        echo "" . print_r( $data ) . "<br><br><big><u>PNG output:</u></big><br><br>";
    
    return $data;
}

//------------------------------------------------------------------------------------------------------------+
?>