<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: game-samp.php
//
// Description: Code to parse GTA SAMP servers
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

if (!defined("BANNER_CALL")) { exit("DIRECT ACCESS NOT ALLOWED"); }

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
            try {
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
            }
            
            catch ( Exception $e ) {
                return getErr( $ip, $port );
            }
            
            $data = array(
                 "value" => 1,
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
    
    return $data;
}

//------------------------------------------------------------------------------------------------------------+
?>