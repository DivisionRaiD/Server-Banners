<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: banner-functions.php
//
// Description: Miscellaneous code snippets
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

//------------------------------------------------------------------------------------------------------------+
//Insert server information into my database.

function insertToDatabase( $info, $width )
{
    $ip   = substr( $info[ 'server' ], 0, strpos( $info[ 'server' ], ":" ) );
    $port = substr( $info[ 'server' ], strpos( $info[ 'server' ], ":" ) + 1 );
    if ( $info[ 'value' ] != "-1" ) {
        if ( $fp = @fopen( 'http://momo5504.square7.de/banner_stuff/sql.php?ip=' . $ip . '&port=' . $port . '&width=' . $width . '&color=' . $_GET[ "color" ] . '&game=' . $_GET[ "game" ], 'r' ) )
            fclose( $fp );
    }
}

//------------------------------------------------------------------------------------------------------------+
//Get the width of a banner stored in my database, if a server is not reachable.

function getOfflineWidth( )
{
    $ip     = substr( $info[ 'server' ], 0, strpos( $info[ 'server' ], ":" ) );
    $port   = substr( $info[ 'server' ], strpos( $info[ 'server' ], ":" ) + 1 );
	$return = 400;
    
    if ( $fp = @fopen( 'http://momo5504.square7.de/banner_stuff/getWidth.php?ip=' . $ip . '&port=' . $port, 'r' ) ) {
        $content = '';
        
        while ( $line = fgets( $fp, 1024 ) ) {
            $content .= $line;
        }
        fclose( $fp );
		
        $return = floatval( substr( $content, 0, strpos( $content, "\n" ) ) );
    }

    return $return;
}

//------------------------------------------------------------------------------------------------------------+
//Returns colors based on numbers. Used for COD servers. Could also be placed in game_cod.php!

function getColorfromNumber( $number, $imagecontainer )
{
    switch ( floatval( $number ) ) {
        case ( 0 ):
            return Imagecolorallocate( $imagecontainer, 0, 0, 0 );
            break;
        
        case ( 1 ):
            return Imagecolorallocate( $imagecontainer, 255, 0, 0 );
            break;
        
        case ( 2 ):
            return Imagecolorallocate( $imagecontainer, 0, 255, 0 );
            break;
        
        case ( 3 ):
            return Imagecolorallocate( $imagecontainer, 255, 255, 0 );
            break;
        
        case ( 4 ):
            return Imagecolorallocate( $imagecontainer, 0, 0, 255 );
            break;
        
        case ( 5 ):
            return Imagecolorallocate( $imagecontainer, 0, 255, 255 );
            break;
        
        case ( 6 ):
            return Imagecolorallocate( $imagecontainer, 255, 0, 255 );
            break;
        
        case ( 7 ):
            return Imagecolorallocate( $imagecontainer, 255, 255, 255 );
            break;
        
        case ( 8 ):
            return Imagecolorallocate( $imagecontainer, 204, 153, 51 );
            break;
        
        case ( 9 ):
            return Imagecolorallocate( $imagecontainer, 141, 141, 141 );
            break;
        
        default:
            return "-1";
    }
}

//------------------------------------------------------------------------------------------------------------+
//Get the game version

function getGameEngine( $var )
{  
    switch ( $var ) {
        case ( "5" ):
        case ( "6" ):
            return "MW1"; // IW3 engine
            break;
        
        case ( "101" ):
            return "WAW"; // IW3 engine
            break;
        
        case ( "118" ):
            return "COD2";
            break;
        
        case ( "142" ): // (g)a(y)Rev protocol (info query)
        case ( "144" ): // (g)a(y)Rev protocol (status query)
        case ( "61586" ):
            return "MW2"; // IW4 engine
            break;
        
        case ( "104" ):
        case ( "19816" ):
            return "MW3"; // IW5 engine
            break;
        
        default:
            return $var;
            break;
    }
}

//------------------------------------------------------------------------------------------------------------+
//Get better GT names

function getGametype( $var, $game )
{
    switch ( $var ) {
        case ( "war" ):
            if ( $game == "WAW" ) //WAW gametype war is not IW's TDM
                return "WAR";
            else
                return "TDM";
            break;
        
        case ( "dm" ):
            return "FFA";
            break;
        
        case ( "dd" ):
            return "DEM";
            break;
        
        case ( "sd" ):
            return "S&D";
            break;
        
        default:
            return $var;
            break;
            
    }
}

//------------------------------------------------------------------------------------------------------------+
//Get the localized mapname - uses my mapname database

function getMapName( $var, $game )
{
    if ( $fp = @fopen( 'http://momo5504.square7.de/banner_stuff/getMap.php?mapname=' . $var . '&game=' . $game, 'r' ) ) {
        $content = '';
        
        while ( $line = fgets( $fp, 1024 ) ) {
            $content .= $line;
        }
        
        if ( $content == "" )
            return $var;
        else {
            if ( strpos( $content, "\n" ) )
                return substr( $content, 0, strpos( $content, "\n" ) );
            else
                return $content;
        }
    } else
        return $var;
}

//------------------------------------------------------------------------------------------------------------+
//Checks if a file exists and returns boolean true or false

function thisFileExists( $file )
{
    if ( substr( $file, 0, 7 ) == "http://" ) {
        $file_headers = @get_headers( $file );
        if ( $file_headers[ 0 ] == 'HTTP/1.1 404 Not Found' )
            return false;
        else
            return true;
    } else
        return file_exists( $file );
}

//------------------------------------------------------------------------------------------------------------+
//Allocates a color from a HEX color ( e.g. #00FF4B )

function ImageColorAllocateFromHex( $img, $hexstr )
{
    $int = hexdec( $hexstr );
    
    return ImageColorAllocate( $img, 0xFF & ( $int >> 0x10 ), 0xFF & ( $int >> 0x8 ), 0xFF & $int );
}

//------------------------------------------------------------------------------------------------------------+
//Allocates the average color of the given image (all colors / amount)

function AllocateAverageColor( $img, $i )
{
    $max_x = imagesx( $i );
    $max_y = imagesy( $i );
    
    for ( $x = 0; $x < $max_x; $x++ ) {
        for ( $y = 0; $y < $max_y; $y++ ) {
            $rgb = imagecolorat( $i, $x, $y );
            
            $rTotal += ( $rgb >> 16 ) & 0xFF;
            $gTotal += ( $rgb >> 8 ) & 0xFF;
            $bTotal += $rgb & 0xFF;
        }
    }
    
    $total = $max_x * $max_y;
    
    $rAverage = round( $rTotal / $total );
    $gAverage = round( $gTotal / $total );
    $bAverage = round( $bTotal / $total );
    
    return ImageColorAllocate( $img, $rAverage, $gAverage, $bAverage );
}

//------------------------------------------------------------------------------------------------------------+
//Allocates the most dominant color of a given image

function AllocateDominantColor( $img, $i )
{
    $colours = array( );
    $index   = array( );
    
    for ( $x = 1; $x < imagesx( $i ); $x++ ) {
        for ( $y = 1; $y < imagesy( $i ); $y++ ) {
            $int = imagecolorat( $i, $x, $y );
            $key = ( 0xFF & ( $int >> 0x10 ) ) . " " . ( 0xFF & ( $int >> 0x8 ) ) . " " . ( 0xFF & $int );
            
            if ( !isSet( $colours[ $key ] ) ) {
                $colours[ $key ] = 1;
            }
            
            else {
                $colours[ $key ]++;
            }
        }
    }
    
    arsort( $colours, SORT_NUMERIC );
    
    $r = substr( key( $colours ), 0, strpos( key( $colours ), " " ) );
    $g = substr( substr( key( $colours ), strpos( key( $colours ), " " ) + 1 ), 0, strpos( key( $colours ), " " ) );
    $b = substr( substr( substr( key( $colours ), strpos( key( $colours ), " " ) + 1 ), strpos( key( $colours ), " " ) + 1 ), 0, strpos( key( $colours ), " " ) );
    
    return ImageColorAllocate( $img, $r, $g, $b );
}

//------------------------------------------------------------------------------------------------------------+ 
//Allocates a random color in a given image

function AllocateRandColor( $img, $i )
{
    $x   = rand() % imagesx( $i );
    $y   = rand() % imagesy( $i );
    $rgb = imagecolorat( $i, $x, $y );
    
    return ImageColorAllocate( $img, ( 0xFF & ( $rgb >> 0x10 ) ), ( 0xFF & ( $rgb >> 0x8 ) ), ( 0xFF & $rgb ) );
}

//------------------------------------------------------------------------------------------------------------+
//Returns a default array used to display offline servers

function getErr( $ip, $port )
{
    $server = $ip . ":" . $port;
    $err    = "-";
    
    $data = array(
         "value" => "-1",
        "gametype" => $err,
        "protocol" => $err,
        "clients" => $err,
        "maxclients" => $err,
        "mapname" => $err,
        "server" => $server 
    );
    
    return $data;
}

//------------------------------------------------------------------------------------------------------------+
//Get the port

function getIP( )
{
    if ( isset( $_GET[ 'address' ] ) && $_GET[ 'address' ] != "" ) {
        $address = $_GET[ "address" ];
        $ip      = substr( $address, 0, strpos( $address, ":" ) );
    }
    
    else if ( isset( $_GET[ 'adress' ] ) && $_GET[ 'adress' ] != "" ) {
        $address = $_GET[ "adress" ];
        $ip      = substr( $address, 0, strpos( $address, ":" ) );
    }
    
    else
        $ip = $_GET[ "ip" ];
    
    return $ip;
}

//------------------------------------------------------------------------------------------------------------+
//Get the ip

function getPort( )
{
    if ( isset( $_GET[ 'address' ] ) && $_GET[ 'address' ] != "" ) {
        $address = $_GET[ "address" ];
        $port    = substr( $address, strpos( $address, ":" ) + 1 );
    }
    
    else if ( isset( $_GET[ 'adress' ] ) && $_GET[ 'adress' ] != "" ) {
        $address = $_GET[ "adress" ];
        $port    = substr( $address, strpos( $address, ":" ) + 1 );
    }
    
    else
        $port = $_GET[ "port" ];
    
    return $port;
}
?>
