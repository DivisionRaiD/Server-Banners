<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: banner-print.php
//
// Description: Code to print the image
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

if ( !defined( "BANNER_CALL" ) ) {
    exit( "DIRECT ACCESS NOT ALLOWED" );
}

//------------------------------------------------------------------------------------------------------------+

include( 'functions.php' );

//------------------------------------------------------------------------------------------------------------+
//Prints the banner

function printimage( $data )
{
    global $root, $font;
    
    $font_size = 15;
    
    setImageWidth( $image_width, $data, $font, $font_size );
    
    insertToDatabase( $data, $image_width );
    
    $isMC = ( isSet( $_GET[ "game" ] ) && $_GET[ "game" ] == "MC" ) ? true : false;
    
    $image_height   = 100;
    $imagecontainer = imagecreatetruecolor( $image_width, $image_height );
    
    imagesavealpha( $imagecontainer, true );
    
    $game     = getGameEngine( $data[ 'protocol' ] );
    $map      = getMapName( $data[ 'mapname' ], $game );
    $gametype = getGametype( $data[ 'gametype' ], $game );
    
    $mappath = $root . "maps/" . $game . "/preview_" . $data[ 'mapname' ] . ".jpg";
    
    if ( $isMC )
        $mappath = $root . "maps/MC/preview_World.jpg";
    
    $bg_data = getBGInfo( $imagecontainer, $data, $mappath, $mapimage );
    
    imagefill( $imagecontainer, 0, 0, $bg_data );
    
    //Add preview to the container
    imagecopyresampled( $imagecontainer, $mapimage, 9, 9, 0, 0, 144, 82, imagesx( $mapimage ), imagesy( $mapimage ) );
    
    $overlay = imagecreatefrompng( $root . "overlay.png" );
    imagecopyresampled( $imagecontainer, $overlay, 0, 0, 0, 0, imagesx( $overlay ), imagesy( $overlay ), imagesx( $overlay ), imagesy( $overlay ) );
    
    $yoffset = 28;
    $xoffset = 165;
    
    //Print this if the server is not reachable!
    if ( $data[ 'value' ] == "-1" ) {
        $text = "Server is offline!";
        
        imagettftext( $imagecontainer, $font_size, 0, $xoffset, $yoffset, Imagecolorallocate( $imagecontainer, 0, 165, 255 ), $font, $text );
        
        //I must add a little watermark :P
        $watermark = imagecreatefrompng( $root . "engine/watermark.png" );
        imagecopyresampled( $imagecontainer, $watermark, $image_width - 75, 60, 0, 0, 63, 35, 320, 176 );
    }
    
    //Print this if it is!
    else {
        
        if ( $game == "MW2" && $data[ 'isW2' ] )
            $game = "W2";
        
        $gamepath  = $root . "engine/" . $game . ".PNG";
        $cleanname = $data[ 'hostname' ];
        
        if ( thisFileExists( $gamepath ) ) {
            $gameimage = imagecreatefrompng( $gamepath );
            imagecopyresampled( $imagecontainer, $gameimage, $image_width - 75, 60, 0, 0, 63, 35, 320, 176 );
        }
        
        $length = $xoffset;
        $color  = Imagecolorallocate( $imagecontainer, 255, 255, 255 );
        $maxlen = strlen( $data[ 'unclean' ] );
        $dots   = false;
        $isCOD  = ( $_GET[ "game" ] == "COD" || !isSet( $_GET[ "game" ] ) ) ? true : false;
        
        if ( $_GET[ 'width' ] != "" && isset( $_GET[ 'width' ] ) && ( 206 + getStringWidth( $data[ 'hostname' ], $font, $font_size ) ) > $_GET[ 'width' ] ) {
            $dots = true;
            $maxlen -= round( ( ( 195 + getStringWidth( $data[ 'hostname' ], $font, $font_size ) ) - intval( $_GET[ 'width' ] ) ) / ( getStringWidth( $data[ 'hostname' ], $font, $font_size ) / strlen( $data[ 'hostname' ] ) ), 0 ) + 3;
        }
        
        for ( $i = 0; $i <= $maxlen; $i++ ) {
            if ( strtolower( $data[ 'unclean' ][ $i ] ) == "v" ) // v is a weird letter^^
                $length += 3;
            
            if ( $data[ 'unclean' ][ $i ] == "^" && $isCOD ) {
                $tempcolor = getCODColor( $data[ 'unclean' ][ $i + 1 ], $imagecontainer );
                if ( $tempcolor == "-1" ) {
                    imagettftext( $imagecontainer, $font_size, 0, $length, $yoffset, $color, $font, $data[ 'unclean' ][ $i ] );
                    $length += getStringWidth( $data[ 'unclean' ][ $i ], $font, $font_size );
                }
                
                else {
                    $color = $tempcolor;
                    $i++;
                }
            }
            
            else if ( $data[ 'unclean' ][ $i ] == "&" && $isMC ) {
                $tempcolor = getMCColor( $data[ 'unclean' ][ $i + 1 ], $imagecontainer, $color );
                if ( $tempcolor == "-1" ) {
                    imagettftext( $imagecontainer, $font_size, 0, $length, $yoffset, $color, $font, $data[ 'unclean' ][ $i ] );
                    $length += getStringWidth( $data[ 'unclean' ][ $i ], $font, $font_size );
                }
                
                else {
                    $color = $tempcolor;
                    $i++;
                }
            }
            
            else {
                imagettftext( $imagecontainer, $font_size, 0, $length, $yoffset, $color, $font, $data[ 'unclean' ][ $i ] );
                $length += getStringWidth( $data[ 'unclean' ][ $i ], $font, $font_size );
            }
        }
        
        if ( $dots )
            imagettftext( $imagecontainer, $font_size, 0, $length, $yoffset, Imagecolorallocate( $imagecontainer, 255, 255, 255 ), $font, "..." );
        
    }
    
    imagettftext( $imagecontainer, 12, 0, $xoffset, 45, Imagecolorallocate( $imagecontainer, 255, 255, 255 ), $font, "IP: {$data[ 'server' ]}" );
    imagettftext( $imagecontainer, 12, 0, $xoffset, 60, Imagecolorallocate( $imagecontainer, 255, 255, 255 ), $font, "Map: {$map}" );
    imagettftext( $imagecontainer, 12, 0, $xoffset, 75, Imagecolorallocate( $imagecontainer, 255, 255, 255 ), $font, "Gametype: " . strtoupper( $gametype ) );
    imagettftext( $imagecontainer, 12, 0, $xoffset, 90, Imagecolorallocate( $imagecontainer, 255, 255, 255 ), $font, "Players: {$data[ 'clients' ]}/{$data[ 'maxclients' ]}" );
    
    if ( !isToDebug() ) {
        //Render the final picture
        imagepng( $imagecontainer );
    }
    
    //imagejpeg( $imagecontainer );
    imagedestroy( $imagecontainer );
    
    setDebugData( $data );
}

//------------------------------------------------------------------------------------------------------------+
//Set the width for the banner

function setImageWidth( &$image_width, $data, $font, $font_size )
{
    if ( isset( $_GET[ 'width' ] ) && $_GET[ 'width' ] != "" && $_GET[ 'width' ] != "no" )
        $image_width = $_GET[ 'width' ];
    else {
        if ( $data[ 'value' ] == "-1" )
            $image_width = 450;
        else
            $image_width = 206 + getStringWidth( $data[ 'hostname' ], $font, $font_size );
    }
    if ( $image_width < 450 )
        $image_width = 450;
    
    $image_width = round( $image_width, 0 );
}

//------------------------------------------------------------------------------------------------------------+
//Print the background image

function getBGInfo( &$imagecontainer, $data, $mappath, &$mapimage )
{
    global $root;
    
    if ( $data[ 'value' ] == "-1" )
        $mapimage = imagecreatefromjpeg( $root . "maps/no_response.jpg" );
    
    else if ( thisFileExists( $mappath ) )
        $mapimage = imagecreatefromjpeg( $mappath );
    
    else
        $mapimage = imagecreatefromjpeg( $root . "maps/no_image.jpg" );
    
    if ( ( !isSet( $_GET[ 'color' ] ) || $_GET[ 'color' ] == "no" ) && $data[ 'value' ] != "-1" ) {
        if ( !thisFileExists( $mappath ) )
            $bgcolor = ImageColorAllocateFromHex( $imagecontainer, "404040" );
        else
            $bgcolor = AllocateAverageColor( $imagecontainer, $mapimage );
    } else {
        $html_color = $_GET[ 'color' ];
        
        if ( !isset( $_GET[ 'color' ] ) || $_GET[ 'color' ] == "" ) {
            $html_color = "404040";
            
            if ( strpos( $html_color, "#" ) )
                $html_color = substr( $html_color, 1 );
            
        }
        
        $bgcolor = ImageColorAllocateFromHex( $imagecontainer, $html_color );
    }
    
    return $bgcolor;
}

//------------------------------------------------------------------------------------------------------------+
//Returns exact width of a string - required for non-fixed-width fonts

function getStringWidth( $string, $font, $size, $angle = 0 )
{
    $strlen = strlen( $string );
    $dim    = 0;
    
    $space = imagettfbbox( $size, $angle, $font, " " );
    $space = $space[ 2 ] - 1;
    
    for ( $i = 0; $i < $strlen; $i++ ) {
        $str        = $string[ $i ] . " ";
        $dimensions = imagettfbbox( $size, $angle, $font, $str );
        $dim += $dimensions[ 2 ] - $space;
    }
    
    return $dim;
}

//------------------------------------------------------------------------------------------------------------+
?>
