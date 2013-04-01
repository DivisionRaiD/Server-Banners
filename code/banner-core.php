<?
    //------------------------------------------------------------------------------------------------------------+
    //
    // Name: banner-code.php
    //
    // Description: Core code/setup code for the banners.
    // Initial author: momo5502 <MauriceHeumann@googlemail.com>
    // Version: 1.0
    // Credit:
    //      -PHP.net as a helpful resource
    //
    //------------------------------------------------------------------------------------------------------------+
    
	//------------------------------------------------------------------------------------------------------------+
    //Setup a banner
	
	function banner( $root, $font )
	{     
		//Get basic information
		global $ip;
		global $port;
		
		if ( isset( $_GET[ 'address' ] ) && $_GET[ 'address' ] != "" ) {
			$address = $_GET[ "address" ];
			$ip      = substr( $address, 0, strpos( $address, ":" ) );
			$port    = substr( $address, strpos( $address, ":" ) + 1 );
       
		}
     
		else {
			$ip   = $_GET[ 'ip' ];
			$port = $_GET[ 'port' ];
		}
     
		if ( !isSet($_GET[ "game" ]))
			$info = queryCOD( $ip, $port );
		
		else
		{	
			switch( $_GET[ "game" ] )
			{
				case "COD":
					$info = queryCOD( $ip, $port );
					break;
				
				case "SAMP": 
					$info = querySAMP( $ip, $port );
					break;
				
				default:
					$info = getErr( $ip, $port);
					break;
			}
		}
		
		printimage( $info, $root, $font );
	}
	
	//------------------------------------------------------------------------------------------------------------+
?>
