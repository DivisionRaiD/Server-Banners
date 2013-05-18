<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: game-bfbc2.php
//
// Description: Code to parse Battlefield Bad Company 2 servers
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

if ( !defined( "BANNER_CALL" ) ) {
    exit( "DIRECT ACCESS NOT ALLOWED" );
}

//------------------------------------------------------------------------------------------------------------+
//Query BFBC2 server - main function!

function queryBFBC2( $ip, $port, $alt_port = false )
{
    $server  = "tcp://" . $ip;
    $connect = @fsockopen( $server, $port, $errno, $errstr, 1 );
    
    if ( !$connect ) {
        if ( !$alt_port )
            return queryBFBC2( $ip, 48888, true );
        
        else
            return getErr( $ip, getPort() );
    }
    
    @fwrite( $connect, "\x00\x00\x00\x00\x1b\x00\x00\x00\x01\x00\x00\x00\x0a\x00\x00\x00serverInfo\x00" );
    stream_set_timeout( $connect, 2 );
    $buffer = @fread( $connect, 4096 );
    $info   = stream_get_meta_data( $connect );
    
    if ( !$buffer || $info[ 'timed_out' ] ) {
        if ( !$alt_port )
            return queryBFBC2( $ip, 48888, true );
        
        else
            return getErr( $ip, getPort() );
    }
    
    setSeparators( $buffer, $separators );
    
    $tok                  = array( );
    $tok[ count( $tok ) ] = strtok( $buffer, $buffer[ $separators[ 0 ] ] );
    
    for ( $i = 1; $i < count( $separators ); $i++ ) {
        $tok[ count( $tok ) ] = strtok( $buffer[ $separators[ $i ] ] );
    }
    
    for ( $i = 0; $i < count( $tok ); $i++ ) {
        $tok[ $i ] = preg_replace( '/[^(\x20-\x7F)]*/', '', $tok[ $i ] ); //Remove all non-ascii chars
    }
    
    //Clean hostname
    $tok[ 2 ] = substr( $tok[ 2 ], 3 );
    
    //Clean mapname and gametype
    if ( $pos = strpos( $tok[ 5 ], "Levels/" ) ) //Happens on 0x20 separator
        {
        $char = 0;
        
        if ( $tok[ 5 ][ $pos - 1 ] == "\x20" ) //0x20 is an acii char
            $char = 1;
        
        $tok[ 6 ] = substr( $tok[ 5 ], $pos );
        $tok[ 5 ] = substr( $tok[ 5 ], 0, $pos - $char );
    }
    
    $tok[ 6 ] = strtolower( str_replace( "Levels/", "", $tok[ 6 ] ) );
    
    cleanMapname( $tok[ 6 ] );
    
    $data = array(
         "value" => 1,
        "hostname" => $tok[ 2 ],
        "gametype" => $tok[ 5 ],
        "protocol" => "BFBC2",
        "clients" => $tok[ 3 ],
        "maxclients" => $tok[ 4 ],
        "mapname" => $tok[ 6 ],
        "server" => $ip . ":" . $port,
        "unclean" => $tok[ 2 ],
        "response" => $buffer 
    );
    
    return $data;
}

//------------------------------------------------------------------------------------------------------------+
//Sets the separators in the buffer

function setSeparators( $buffer, &$separators )
{
    $separator = array(
         "\x01",
        "\x02",
        "\x04",
        "\x05",
        "\x08",
        "\x0F",
        "\x13" 
    ); //Maybe there are missing ones! 0x20 is a very bad separator :(
    
    for ( $i = 0; $i < strlen( $buffer ); $i++ ) {
        foreach ( $separator as $sep ) {
            if ( $buffer[ $i ] == $sep ) {
                $separators[ count( $separators ) ] = $i;
            }
        }
    }
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
}

//------------------------------------------------------------------------------------------------------------+
?>