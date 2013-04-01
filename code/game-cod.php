<?
    //------------------------------------------------------------------------------------------------------------+
    //
    // Name: game-cod.php
    //
    // Description: Code to parse COD servers (Quake3 based)
    // Initial author: momo5502 <MauriceHeumann@googlemail.com>
    // Version: 1.0
    // Credit:
    //      -PHP.net as a helpful resource
    //
    //------------------------------------------------------------------------------------------------------------+
	
	//------------------------------------------------------------------------------------------------------------+
    //Open socket connection, send and receive information, return it & close socket again
	
	function queryCOD( $ip, $port )
	{
		//$cmd = "\xFF\xFF\xFF\xFFgetstatus\x00";
		$cmd = "\xFF\xFF\xFF\xFFgetinfo\x00";
		
		return parseQueryData( getQueryData( $ip, $port, $cmd, true ), $ip, $port, $cmd );
	}
	
	//------------------------------------------------------------------------------------------------------------+
    //Open socket connection, send and receive information, return it & close socket again
	
    function getQueryData( $ip, $port, $send, $MW3 )
    {
        $server  = "udp://" . $ip;
        $connect = @fsockopen( $server, $port, $errno, $errstr, 2 );
       
        if ( !$connect )
            return "-1";
       
       
        else {
            fwrite( $connect, $send );
            stream_set_timeout( $connect, 2 );
            $output = fread( $connect, 8192 );
            $info   = stream_get_meta_data( $connect );
            fclose( $connect );
            if ( $_GET[ 'debug' ] == "1" )
                echo "<big><u>Server response:</u></big><br><br>" . substr( $output, 4 ) . "<br><br><big><u>PNG output:</u></big><br><br>";
           
            if ( !$output || !isset( $output ) || $output == "" || $info[ 'timed_out' ] ) {
                if ( $MW3 )
                    return getMW3Port( $ip, $port, $send );
                else
                    return "-1";
            }
           
            else
                return substr( $output, 4 );
        }
    }
     
	//------------------------------------------------------------------------------------------------------------+
    //MW3 servers use a different port system than other cods - unused as API is down :(
	
    function getMW3Port( $ip, $port, $cmd )
    {
        //It uses icedreams api, therefore it only works with 4D1's MW3 servers
        if ( $fp = @fopen( 'http://s.mufff.in/api/cache', 'r' ) ) {
            $content = '';
           
            while ( $line = fgets( $fp, 1024 ) ) {
                $content .= $line;
            }
           
            $data = json_decode( $content );
            $n    = count( $data->iw5m->ListedServers );
            $i    = 0;
            while ( $i < $n ) {
                if ( $ip == $data->iw5m->ListedServers[ $i ]->IPString && $port == $data->iw5m->ListedServers[ $i ]->Ports[ 1 ] ) {
                    return getQueryData( $ip, $data->iw5m->ListedServers[ $i ]->Ports[ 0 ], $cmd, false );
                }
               
                $i++;
            }
           
            return "-1";
           
        } else {
            return "-1";
        }
    }

    //------------------------------------------------------------------------------------------------------------+
    //Parse the query data and return it as array
	
    function parseQueryData( $input, $ip, $port, $cmd )
    {
        $server = $ip . ":" . $port;
        $err    = "-";
       
        if ( $input == "-1" )
            $data = array(
                 "value" => $input,
                "gametype" => $err,
                "protocol" => $err,
                "clients" => $err,
                "maxclients" => $err,
                "mapname" => $err,
                "server" => $server
            );
       
        else {
            //New segmentation code
           
            if ( $cmd == "\xFF\xFF\xFF\xFFgetstatus\x00" ) {
                $hostname = substr( $input, strpos( $input, "\\sv_hostname" ) + 13 );
                $players  = count( getPlayers( $input ) );
                $gametype = substr( $input, strpos( $input, "\\g_gametype" ) + 12 );
            }
           
            else {
                $hostname = substr( $input, strpos( $input, "\\hostname" ) + 10 );
               
                if ( strpos( $input, "\\clients" ) ) {
                    $players = substr( $input, strpos( $input, "\\clients" ) + 9 );
                    $players = cleanFromRest( $players );
                }
               
                else
                    $players = "-";
               
                $gametype = substr( $input, strpos( $input, "\\gametype" ) + 10 );
            }
           
            $maxplayers = substr( $input, strpos( $input, "\\sv_maxclients" ) + 15 );
            $maxplayers = cleanFromRest( $maxplayers );
           
            $mapname = substr( $input, strpos( $input, "\\mapname" ) + 9 );
            $mapname = cleanFromRest( $mapname );
           
            $hostname = cleanFromRest( $hostname );
           
            $gametype = cleanFromRest( $gametype );
           
            $protocol = substr( $input, strpos( $input, "\\protocol" ) + 10 );
            $protocol = cleanFromRest( $protocol );
           
            //Get a clean hostname without '^1's or '^5's
            $unclean = $hostname;
           
            for ( $i = 0; $i < 10; $i++ )
                $hostname = str_replace( "^{$i}", "", $hostname );
           
            $value = 1;
           
            //Put information into an array
            $data = array(
                 "value" => $value,
                "hostname" => $hostname,
                "gametype" => $gametype,
                "protocol" => $protocol,
                "clients" => $players,
                "maxclients" => $maxplayers,
                "mapname" => $mapname,
                "server" => $server,
                "unclean" => $unclean
            );
        }
       
        return $data;
    }
	
	//------------------------------------------------------------------------------------------------------------+
    //Clean input part from their rest behind
	
    function cleanFromRest( $self )
    {
        if ( strpos( $self, "\\" ) )
            return substr( $self, 0, strpos( $self, "\\" ) );
        else
            return $self;
    }
     
	//------------------------------------------------------------------------------------------------------------+
    //Get the players - only for getstatus query
	
    function getPlayers( $input )
    {
        $player_str = substr( $input, strpos( $input, "\n" ) + 1, strlen( $input ) );
        $player_str = substr( $player_str, strpos( $player_str, "\n" ) + 1, strlen( $player_str ) );
        $players    = array( );
        $ZOB        = substr_count( $player_str, "\n" );
        $tok        = strtok( $player_str, "\"" );
       
        for ( $k = 1; $k <= $ZOB; $k++ ) {
            $score = substr( $tok, 0, strpos( $tok, " " ) );
           
            if ( substr( $score, 0, 1 ) == " " || substr( $score, 0, 1 ) == "\n" )
                $score = substr( $score, 1, strlen( $score ) );
           
            $ping = substr( $tok, strpos( $tok, " " ) + 1, strlen( $input ) );
           
            if ( substr( $ping, 0, 1 ) == " " || substr( $ping, 0, 1 ) == "\n" )
                $ping = substr( $ping, 0, strlen( $ping ) - 1 );
           
            $tok  = strtok( "\"\n" );
            $name = $tok;
           
            $p_array = array(
                 name => $name,
                score => $score,
                ping => $ping
            );
           
            array_push( $players, $p_array );
           
            $tok = strtok( "\"" );
        }
        return $players;
    }
	
	//------------------------------------------------------------------------------------------------------------+
?>