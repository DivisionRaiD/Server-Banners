<?
//------------------------------------------------------------------------------------------------------------+
//
// Name: banner-print.php
//
// Description: Code to print the image
// Initial author: momo5502 <MauriceHeumann@googlemail.com>
//
//------------------------------------------------------------------------------------------------------------+

//------------------------------------------------------------------------------------------------------------+

include( 'banner-functions.php' );

//------------------------------------------------------------------------------------------------------------+
//Prints the banner

function printimage( $data )
{
    global $root, $font;
    
    $fontpath   = $font;
    $font_size  = 13;
    $char_width = 9.5;
    
    if ( isset( $_GET[ 'width' ] ) && $_GET[ 'width' ] != "" && $_GET[ 'width' ] != "no" )
        $image_width = $_GET[ 'width' ];
    else {
        if ( $data[ 'value' ] == "-1" )
            $image_width = 400;
        else
            $image_width = 167 + strlen( $data[ 'hostname' ] ) * $char_width;
    }
    if ( $image_width < 400 )
        $image_width = 400;
    
    $image_width = round( $image_width, 0 );
    
    insertToDatabase( $data, $image_width );
    
    $image_height   = 100;
    $imagecontainer = imagecreatetruecolor( $image_width, $image_height );
    
    imagesavealpha( $imagecontainer, true );
    
    $game     = getGameEngine( $data[ 'protocol' ] );
    $map      = getMapName( $data[ 'mapname' ], $game );
    $gametype = getGametype( $data[ 'gametype' ], $game );
    
    $mappath = $root . "maps/" . $game . "/preview_" . $data[ 'mapname' ] . ".jpg";
    
    if ( $data[ 'value' ] == "-1" )
        $mapimage = imagecreatefromjpeg( $root . "maps/no_response.jpg" );
    
    else if ( thisFileExists( $mappath ) )
        $mapimage = imagecreatefromjpeg( $mappath );
    
    else
        $mapimage = imagecreatefromjpeg( $root . "maps/no_image.jpg" );
    
    if ( ( !isSet( $_GET[ 'color' ] ) || $_GET[ 'color' ] == "no" ) && $data[ 'value' ] != "-1" )
        $bgcolor = AllocateAverageColor( $imagecontainer, $mapimage );
    
    else {
        $html_color = $_GET[ 'color' ];
        
        if ( !isset( $_GET[ 'color' ] ) || $_GET[ 'color' ] == "" ) {
            $html_color = "404040";
            
            if ( strpos( $html_color, "#" ) )
                $html_color = substr( $html_color, 1 );
            
        }
        
        $bgcolor = ImageColorAllocateFromHex( $imagecontainer, $html_color );
    }
    
    $bg = imagecreatefrompng( $root . "bg.png" );
    
    imagefill( $imagecontainer, 0, 0, $bgcolor );
    
    imagelayereffect( $imagecontainer, IMG_EFFECT_OVERLAY );
    
    imagecopyresampled( $imagecontainer, $bg, 0, 0, 0, 0, $image_width, $image_height, 100, 100 );
    
    imagelayereffect( $imagecontainer, IMG_EFFECT_NORMAL );
    
    //Some colors
    $white = Imagecolorallocate( $imagecontainer, 255, 255, 255 );
    $gray  = Imagecolorallocate( $imagecontainer, 127, 127, 127 );
    $red   = Imagecolorallocate( $imagecontainer, 255, 0, 0 );
    
    //Add preview to the container
    imagecopyresampled( $imagecontainer, $mapimage, 15, 15, 0, 0, 123, 70, imagesx( $mapimage ), imagesy( $mapimage ) );
    
    //Print this if the server is not reachable!
    if ( $data[ 'value' ] == "-1" ) {
        $text = "Server is offline!";
        
        imagettftext( $imagecontainer, $font_size, 0, 150, 30, $red, $fontpath, $text );
        
        //I must add a little watermark :P
        $watermark = imagecreatefrompng( $root . "engine/watermark.png" );
        imagecopyresampled( $imagecontainer, $watermark, $image_width - 75, 60, 0, 0, 63, 35, 320, 176 );
    }
    
    //Print this if it is!
    else {
        $gamepath  = $root . "engine/" . $game . ".PNG";
        $cleanname = $data[ 'hostname' ];
        
        //Print the information onto the picture
        if ( thisFileExists( $gamepath ) ) {
            $gameimage = imagecreatefrompng( $gamepath );
            imagecopyresampled( $imagecontainer, $gameimage, $image_width - 75, 60, 0, 0, 63, 35, 320, 176 );
        }
        
        //Colored hostname
        $length = 150;
        $color  = $white;
        $maxlen = strlen( $data[ 'unclean' ] );
        $dots   = false;
        
        if ( $_GET[ 'width' ] != "" && isset( $_GET[ 'width' ] ) && ( 167 + strlen( $data[ 'hostname' ] ) * $char_width ) > $_GET[ 'width' ] ) {
            $dots = true;
            $maxlen -= round( ( ( 195 + strlen( $data[ 'hostname' ] ) * $char_width ) - floatval( $_GET[ 'width' ] ) ) / $char_width, 0 ) + 3;
        }
        
        for ( $i = 0; $i <= $maxlen; $i++ ) {
            if ( $data[ 'unclean' ][ $i ] == "^" && ( $_GET[ "game" ] == "COD" || !isSet( $_GET[ "game" ] ) ) ) {
                $tempcolor = getColorfromNumber( $data[ 'unclean' ][ $i + 1 ], $imagecontainer );
                if ( $tempcolor == "-1" ) {
                    imagettftext( $imagecontainer, $font_size, 0, $length, 30, $color, $fontpath, $data[ 'unclean' ][ $i ] );
                    $length += $char_width;
                }
                
                else {
                    $color = $tempcolor;
                    $i++;
                }
            }
            
            else if ( $data[ 'unclean' ][ $i ] == "&" && ( isSet( $_GET[ "game" ] ) && $_GET[ "game" ] == "MC" ) ) {
                $tempcolor = getMCColor( $data[ 'unclean' ][ $i + 1 ], $imagecontainer, $color );
                if ( $tempcolor == "-1" ) {
                    imagettftext( $imagecontainer, $font_size, 0, $length, 30, $color, $fontpath, $data[ 'unclean' ][ $i ] );
                    $length += $char_width;
                }
                
                else {
                    $color = $tempcolor;
                    $i++;
                }
            }
            
            else {
                imagettftext( $imagecontainer, $font_size, 0, $length, 30, $color, $fontpath, $data[ 'unclean' ][ $i ] );
                $length += $char_width;
            }
        }
        
        if ( $dots )
            imagettftext( $imagecontainer, $font_size, 0, $length, 30, $white, $fontpath, "..." );
    }
    
    $mapshadow = imagecreatefrompng( $root . "maps/shadow.png" );
    imagecopyresampled( $imagecontainer, $mapshadow, 15, 15, 0, 0, 126, 73, 334, 194 );
    imagettftext( $imagecontainer, 10, 0, 150, 47, $white, $fontpath, "IP: {$data[ 'server' ]}\nMap: {$map}\nGametype: " . strtoupper( $gametype ) . "\nPlayers: {$data[ 'clients' ]}/{$data[ 'maxclients' ]}" );
    
    //Render the final picture
    imagepng( $imagecontainer );
    //imagejpeg( $imagecontainer );
    imagedestroy( $imagecontainer );
}

//------------------------------------------------------------------------------------------------------------+
?>
