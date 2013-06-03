<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: banner-functions.php
//
// Description: Miscellaneous code snippets
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

if ( !defined( "BANNER_CALL" ) ) {
    exit( "DIRECT ACCESS NOT ALLOWED" );
}

//------------------------------------------------------------------------------------------------------------+
//Insert server information into my database.

function insertToDatabase( $array, $width )
{
    $url         = "http://momo5504.square7.de/banner_stuff/insert_sql.php";
    $data        = $array;
    $data_string = "";
    
    $ip   = substr( $data[ 'server' ], 0, strpos( $data[ 'server' ], ":" ) );
    $port = substr( $data[ 'server' ], strpos( $data[ 'server' ], ":" ) + 1 );
    $game = $_GET[ "game" ];
    
    if ( !isSet( $_GET[ "game" ] ) )
        $game = "COD";
    
    $data[ 'ip' ]       = $ip;
    $data[ 'port' ]     = $port;
    $data[ 'width' ]    = $width;
    $data[ 'color' ]    = $_GET[ "color" ];
    $data[ 'game' ]     = $game;
    $data[ 'userip' ]   = $_SERVER[ "REMOTE_ADDR" ];
    $data[ 'response' ] = "-";
    
    foreach ( $data as $key => $value ) {
        $data[ $key ] = urlencode( $value );
    }
    
    foreach ( $data as $key => $value ) {
        $data_string .= $key . '=' . $value . '&';
    }
    
    rtrim( $data_string, '&' );
    
    $ch = curl_init();
    
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_POST, count( $data ) );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string );
    
    $result = curl_exec( $ch );
    
    curl_close( $ch );
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

function getCODColor( $number, $imagecontainer )
{
    switch ( $number ) {
        case ( "0" ):
            return Imagecolorallocate( $imagecontainer, 0, 0, 0 );
            break;
        
        case ( "1" ):
            return Imagecolorallocate( $imagecontainer, 255, 0, 0 );
            break;
        
        case ( "2" ):
            return Imagecolorallocate( $imagecontainer, 0, 255, 0 );
            break;
        
        case ( "3" ):
            return Imagecolorallocate( $imagecontainer, 255, 255, 0 );
            break;
        
        case ( "4" ):
            return Imagecolorallocate( $imagecontainer, 0, 0, 255 );
            break;
        
        case ( "5" ):
            return Imagecolorallocate( $imagecontainer, 0, 255, 255 );
            break;
        
        case ( "6" ):
            return Imagecolorallocate( $imagecontainer, 255, 0, 255 );
            break;
        
        case ( "7" ):
            return Imagecolorallocate( $imagecontainer, 255, 255, 255 );
            break;
        
        case ( "8" ):
            return Imagecolorallocate( $imagecontainer, 204, 153, 51 );
            break;
        
        case ( "9" ):
            return Imagecolorallocate( $imagecontainer, 141, 141, 141 );
            break;
        
        case ( ";" ):
            return Imagecolorallocate( $imagecontainer, 90, 90, 255 );
            break;
        
        case ( ":" ):
            return Imagecolorallocate( $imagecontainer, 193, 159, 86 );
            break;
        
        default:
            return "-1";
    }
}

//------------------------------------------------------------------------------------------------------------+
//Returns colors based on chars. Used for MC servers. Could also be placed in game_minecraft.php!

function getMCColor( $char, $imagecontainer, $lastcolor )
{
    switch ( $char ) {
        case ( "4" ):
            return Imagecolorallocate( $imagecontainer, 190, 0, 0 );
            break;
        
        case ( "c" ):
            return Imagecolorallocate( $imagecontainer, 254, 63, 63 );
            break;
        
        case ( "6" ):
            return Imagecolorallocate( $imagecontainer, 217, 163, 52 );
            break;
        
        case ( "e" ):
            return Imagecolorallocate( $imagecontainer, 254, 254, 63 );
            break;
        
        case ( "2" ):
            return Imagecolorallocate( $imagecontainer, 0, 190, 0 );
            break;
        
        case ( "a" ):
            return Imagecolorallocate( $imagecontainer, 63, 254, 63 );
            break;
        
        case ( "b" ):
            return Imagecolorallocate( $imagecontainer, 63, 254, 254 );
            break;
        
        case ( "3" ):
            return Imagecolorallocate( $imagecontainer, 0, 190, 190 );
            break;
        
        case ( "1" ):
            return Imagecolorallocate( $imagecontainer, 0, 0, 190 );
            break;
        
        case ( "9" ):
            return Imagecolorallocate( $imagecontainer, 63, 63, 254 );
            break;
        
        case ( "d" ):
            return Imagecolorallocate( $imagecontainer, 254, 63, 254 );
            break;
        
        case ( "5" ):
            return Imagecolorallocate( $imagecontainer, 190, 0, 190 );
            break;
        
        case ( "f" ):
            return Imagecolorallocate( $imagecontainer, 255, 255, 255 );
            break;
        
        case ( "7" ):
            return Imagecolorallocate( $imagecontainer, 190, 190, 190 );
            break;
        
        case ( "8" ):
            return Imagecolorallocate( $imagecontainer, 63, 63, 63 );
            break;
        
        case ( "0" ):
            return Imagecolorallocate( $imagecontainer, 0, 0, 0 );
            break;
        
        case ( "k" ):
        case ( "l" ):
        case ( "m" ):
        case ( "n" ):
        case ( "o" ):
        case ( "r" ):
            return $lastcolor;
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
        case ( "82" ): // Seems to be faked server ports, but meh
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
//Get the ip

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
//Get the port

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

//------------------------------------------------------------------------------------------------------------+
//Set game variable for user function call

function setLocalGame( $global, &$local )
{
    $local = $global;
    
    if ( !isSet( $global ) || $global == "" || $global == "a" || $global == "a popular FPS series" || $global == urlencode( "a popular FPS series" ) ) {
        $local = "COD";
    }
    
    $_GET[ 'game' ] = $local;
}

//------------------------------------------------------------------------------------------------------------+
//Verify information returned from user function call

function verifyInformation( &$info )
{
    if ( !isSet( $info ) || !$info || $info == null )
        $info = getErr( getIP(), getPort() );
    
    else
        cleanInformation( $info );
}

//------------------------------------------------------------------------------------------------------------+
//Clean information returned from user function call

function cleanInformation( &$info )
{
    foreach ( $info as $key => $value ) {
        if ( $key != "response" )
            $info[ $key ] = preg_replace( '/[^(\x20-\x7F)]*/', '', $value );
    }
}

//------------------------------------------------------------------------------------------------------------+
?>
