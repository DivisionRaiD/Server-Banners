<?php
//------------------------------------------------------------------------------------------------------------+
//
// Name: print.php
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
	global $root, $font, $_game, $f_inf;
	
	$font_size = 15;
	$yoffset   = 28;
	$xoffset   = 165;
	
	setImageWidth( $image_width, $data, $font, $font_size, $xoffset );
	
	insertToDatabase( $data, $image_width );
	
	$isMC = ( $_game == "MC" );
	
	$image_height   = 100;
	$imagecontainer = imagecreatetruecolor( $image_width, $image_height );
	
	imagesavealpha( $imagecontainer, true );
	
	$game = getGameEngine( $data[ 'protocol' ] );
	
	$map = $data[ 'mapname' ];
	
	if ( $data[ 'value' ] != "-1" )
		$map = getMapName( $data[ 'mapname' ], $game );
	
	$gametype = getGametype( $data[ 'gametype' ], $game );
	$size     = 12;
	$mappath  = $root . "maps/" . $game . "/preview_" . $data[ 'mapname' ] . ".jpg";
	
	if ( $isMC )
		$mappath = $root . "maps/MC/preview_World.jpg";
	
	$bg_data = getBGInfo( $imagecontainer, $data, $mappath, $mapimage );
	
	imagefill( $imagecontainer, 0, 0, $bg_data );
	
	$pattern      = imagecreatefrompng( $root . "pattern.png" );
	$border_color = Imagecolorallocate( $imagecontainer, 97, 97, 97 );
	
	$xv = imagesx( $pattern );
	$yv = imagesy( $pattern );
	
	// Print background pattern
	for ( $x = 0; $x < ( $image_width / $xv ); $x++ ) {
		for ( $y = 0; $y < ( $image_height / $yv ); $y++ ) {
			imagecopyresampled( $imagecontainer, $pattern, $xv * $x, $yv * $y, 0, 0, $xv, $yv, $xv, $yv );
		}
	}
	
	$s_left   = imagecreatefrompng( $root . "shadow/left.png" );
	$s_right  = imagecreatefrompng( $root . "shadow/right.png" );
	$s_center = imagecreatefrompng( $root . "shadow/center.png" );
	$border   = $image_width - imagesx( $s_right );
	
	// Left and right shadow
	imagecopyresampled( $imagecontainer, $s_left, 0, 0, 0, 0, imagesx( $s_left ), 100, imagesx( $s_left ), imagesy( $s_left ) );
	imagecopyresampled( $imagecontainer, $s_right, $image_width - imagesx( $s_right ), 0, 0, 0, imagesx( $s_right ), 100, imagesx( $s_right ), imagesy( $s_right ) );
	
	// Middle shadow
	for ( $x = imagesx( $s_left ); $x < $border; $x++ ) {
		imagecopyresampled( $imagecontainer, $s_center, $x, 0, 0, 0, 1, 100, imagesx( $s_center ), imagesy( $s_center ) );
	}
	
	// Top and bottom border
	for ( $x = 0; $x < $image_width; $x++ ) {
		imagesetpixel( $imagecontainer, $x, 0, $border_color );
		imagesetpixel( $imagecontainer, $x, $image_height - 1, $border_color );
	}
	
	// Left and right border
	for ( $y = 0; $y < $image_height; $y++ ) {
		imagesetpixel( $imagecontainer, 0, $y, $border_color );
		imagesetpixel( $imagecontainer, $image_width - 1, $y, $border_color );
	}
	
	//Add preview to the container
	imagecopyresampled( $imagecontainer, $mapimage, 9, 9, 0, 0, 144, 82, imagesx( $mapimage ), imagesy( $mapimage ) );
	
	$overlay = imagecreatefrompng( $root . "overlay.png" );
	imagecopyresampled( $imagecontainer, $overlay, 0, 0, 0, 0, imagesx( $overlay ), imagesy( $overlay ), imagesx( $overlay ), imagesy( $overlay ) );
	
	$white = Imagecolorallocate( $imagecontainer, 255, 255, 255 );
	
	//Print this if the server is not reachable!
	if ( $data[ 'value' ] == "-1" ) {
		$text = "Server is offline!";
		
		imagettftext( $imagecontainer, $font_size, 0, $xoffset, $yoffset, Imagecolorallocate( $imagecontainer, 0, 165, 255 ), $font, $text );
		
		//I must add a little watermark :P
		$watermark = imagecreatefrompng( $root . "watermark.png" );
		imagecopyresampled( $imagecontainer, $watermark, $image_width - 75, 60, 0, 0, 63, 35, 320, 176 );
	}
	
	//Print this if it is!
	else {
		
		if ( $game == "MW2" && $data[ 'isW2' ] )
			$game = "W2";
		
		$gamepath  = $root . "games/" . $game . ".png";
		$cleanname = $data[ 'hostname' ];
		
		if ( thisFileExists( $gamepath ) ) {
			$gameimage = imagecreatefrompng( $gamepath );
			imagecopyresampled( $imagecontainer, $gameimage, $image_width - 75, 60, 0, 0, 63, 35, 320, 176 );
		}
		
		$length     = $xoffset;
		$color      = Imagecolorallocate( $imagecontainer, 255, 255, 255 );
		$maxlen     = strlen( $data[ 'unclean' ] );
		$isCOD      = ( $_game == "COD" );
		$namelength = getStringWidth( $data[ 'hostname' ], $font, $font_size );
		
		for ( $i = 0; $i <= $maxlen; $i++ ) {
			$print = false;
			
			if ( $data[ 'unclean' ][ $i ] == "^" && $isCOD ) {
				$tempcolor = getCODColor( $data[ 'unclean' ][ $i + 1 ], $imagecontainer );
				if ( $tempcolor == "-1" ) {
					$print = true;
				}
				
				else {
					$color = $tempcolor;
					$i++;
				}
			}
			
			else if ( $data[ 'unclean' ][ $i ] == "&" && $isMC ) {
				$tempcolor = getMCColor( $data[ 'unclean' ][ $i + 1 ], $imagecontainer, $color );
				if ( $tempcolor == "-1" ) {
					$print = true;
				}
				
				else {
					$color = $tempcolor;
					$i++;
				}
			}
			
			else
				$print = true;
			
			if( $print ) {
				$temp_l = $length + getStringWidth( $data[ 'unclean' ][ $i ], $font, $font_size );
				
				if( isSet( $_GET["width"] ) && $temp_l > $border )
				{
					break;
				}
				else
				{
					imagettftext( $imagecontainer, $font_size, 0, $length, $yoffset, $color, $font, $data[ 'unclean' ][ $i ] );
					$length = $temp_l;
				}
			}
		}
	}
	
	imagettftext( $imagecontainer, $size, 0, $xoffset, 45, $white, $f_inf, "IP: {$data[ 'server' ]}" );
	imagettftext( $imagecontainer, $size, 0, $xoffset, 60, $white, $f_inf, "Map: {$map}" );
	imagettftext( $imagecontainer, $size, 0, $xoffset, 75, $white, $f_inf, "Gametype: " . strtoupper( $gametype ) );
	imagettftext( $imagecontainer, $size, 0, $xoffset, 90, $white, $f_inf, "Players: {$data[ 'clients' ]}/{$data[ 'maxclients' ]}" );
	
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

function setImageWidth( &$image_width, $data, $font, $font_size, $xoffset )
{
	if ( isset( $_GET[ 'width' ] ) && $_GET[ 'width' ] != "" && $_GET[ 'width' ] != "no" )
		$image_width = $_GET[ 'width' ];
	else {
		if ( $data[ 'value' ] == "-1" )
			$image_width = 400;
		else
			$image_width = $xoffset + 15 + getStringWidth( $data[ 'hostname' ], $font, $font_size );
	}
	if ( $image_width < 400 )
		$image_width = 400;
	
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
	
	$box   = imagettfbbox( $size, $angle, $font, " " );
	$min_x = min( array(
		 $box[ 0 ],
		$box[ 2 ],
		$box[ 4 ],
		$box[ 6 ] 
	) );
	$max_x = max( array(
		 $box[ 0 ],
		$box[ 2 ],
		$box[ 4 ],
		$box[ 6 ] 
	) );
	$space = ( $max_x - $min_x );
	
	for ( $i = 0; $i < $strlen; $i++ ) {
		$str = " " . $string[ $i ] . " ";
		$box = imagettfbbox( $size, $angle, $font, $str );
		
		$min_x = min( array(
			 $box[ 0 ],
			$box[ 2 ],
			$box[ 4 ],
			$box[ 6 ] 
		) );
		$max_x = max( array(
			 $box[ 0 ],
			$box[ 2 ],
			$box[ 4 ],
			$box[ 6 ] 
		) );
		$width = ( $max_x - $min_x );
		
		$dim += $width - ( $space * 2 );
	}
	
	return $dim;
}

//------------------------------------------------------------------------------------------------------------+
?>
