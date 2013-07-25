<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: BF.php
//
// Description: Code to parse Battlefield servers
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
// Note: Main algorithm by Richard Pery, copied from LGSL!
//
//------------------------------------------------------------------------------------------------------------+

if ( !defined( "BANNER_CALL" ) ) {
    exit( "DIRECT ACCESS NOT ALLOWED" );
}

//------------------------------------------------------------------------------------------------------------+
//Query BF server - main function!

function query( $ip, $port, $alt_port = false )
{
    $server  = "tcp://" . $ip;
    $connect = @fsockopen( $server, $port, $errno, $errstr, 1 );
    
    if ( !$connect ) {
        if ( !$alt_port )
            return query( $ip, intval($port) + 22000, true );
        
        else
		{
			if( intval($port) != 48888 )
				return query( $ip, 48888, true );
			
			else
				return getErr( $ip, getPort() );
		}
    }
    
    @fwrite( $connect, "\x00\x00\x00\x00\x1b\x00\x00\x00\x01\x00\x00\x00\x0a\x00\x00\x00serverInfo\x00" );
    stream_set_timeout( $connect, 2 );
    $buffer = @fread( $connect, 4096 );
    $info   = stream_get_meta_data( $connect );
    
    if ( !$buffer || $info[ 'timed_out' ] ) {
        if ( !$alt_port )
            return query( $ip, 48888, true );
        
        else
            return getErr( $ip, getPort() );
    }
    
    $length = lgsl_unpack( substr( $buffer, 4, 4 ), "L" );
    
    while ( strlen( $buffer ) < $length ) {
        $packet = fread( $lgsl_fp, 4096 );
        
        if ( $packet ) {
            $buffer .= $packet;
        } else {
            break;
        }
    }
	
	$data = array(
		"protocol" => "BF",
        "value" => 1,
        "server" => $ip . ":" . $port,
        "response" => $buffer 
    );
    
    $buffer = substr( $buffer, 12 );
    
    $response_type = lgsl_cut_pascal( $buffer, 4, 0, 1 );
    
    if ( $response_type != "OK" ) {
        return getErr( $ip, getPort() );
    }
    
    $data[ "hostname" ]   = lgsl_cut_pascal( $buffer, 4, 0, 1 );
    $data[ "clients" ]    = lgsl_cut_pascal( $buffer, 4, 0, 1 );
    $data[ "maxclients" ] = lgsl_cut_pascal( $buffer, 4, 0, 1 );
    $data[ "gametype" ]   = lgsl_cut_pascal( $buffer, 4, 0, 1 );
    $data[ "mapname" ]    = strtolower(lgsl_cut_pascal( $buffer, 4, 0, 1 ));
	$data[ "protocol"]    = getPunkbusterProtocol( $buffer );
    $data[ "unclean" ]    = $data[ "hostname" ];
    
	$data[ "gametype" ] = substr( $data[ "gametype" ], 0, strlen( $data[ "gametype" ] ) - 1 );
    
    foreach ( $data as $key => $value )
        $data[ $key ] = preg_replace( '/[^(\x20-\x7F)]*/', '', $value ); //Remove all non-ascii chars
    
    $data[ "mapname" ] = strtolower( str_replace( "Levels/", "", $data[ "mapname" ] ) );
    
    cleanMapname( $data[ "mapname" ] );
    
    return $data;
}

//------------------------------------------------------------------------------------------------------------+
//Clean mapname ending

function cleanMapname( &$mapname )
{
    $endings = array(
         "gr",
        "cq",
        "sdm",
        "sr" 
    );
    
    foreach ( $endings as $ending ) {
        if ( strpos( $mapname, $ending ) == strlen( $mapname ) - strlen( $ending ) )
            $mapname = substr( $mapname, 0, strlen( $mapname ) - strlen( $ending ) );
    }
    
    if ( $mapname[ strlen( $mapname ) - 1 ] == "_" )
        $mapname = substr( $mapname, 0, strlen( $mapname ) - 1 );
		
	if( substr( $mapname, 0, 4 ) == "nam_" )
		$mapname = substr( $mapname, 4 );
}

//------------------------------------------------------------------------------------------------------------+
//Get Protocol from Bunkbuster http://www.punkbuster.com/

function getPunkbusterProtocol( &$buffer )
{
	for($i=0;$i<20;$i++)
	{
		$temp = lgsl_cut_pascal( $buffer, 4, 0, 1 );
		$temp = preg_replace( '/[^(\x20-\x7F)]*/', '', $temp );

		if( $temp[0] == "v" && $temp[2] == "." ) //version and separation dot
		{
			$temp = substr( $temp, 0, 6 );
			switch( $temp )
			{
				case "v1.867": //BF3 
					return "BF3";
					break;
		
				case "v1.826": //BFBC2 
					return "BFBC2";
					break;
		
				default:
					return "BF";
					break;
			}
		}
		
		else if( $temp == "BC2" )
			return "BFBC2";
			
	}
	
	return "BF";
}

//------------------------------------------------------------------------------------------------------------+
//LGSL code - thx to Richard Pery

function lgsl_cut_pascal( &$buffer, $start_byte = 1, $length_adjust = 0, $end_byte = 0 )
{
    $length = ord( substr( $buffer, 0, $start_byte ) ) + $length_adjust;
    $string = substr( $buffer, $start_byte, $length );
    $buffer = substr( $buffer, $start_byte + $length + $end_byte );
    
    return $string;
}

function lgsl_unpack( $string, $format )
{
    list( , $string ) = @unpack( $format, $string );
    
    return $string;
}
//------------------------------------------------------------------------------------------------------------+
?>