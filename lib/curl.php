<?php

class curl {

	public static function sendRequest($url, $payload=null, $method=null, $custom_header=false, $port=false) { 
		// Init curl & set options
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        
        // Set Header
	    $custom_header[] = 'Content-Type: text/xml';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $custom_header);

        if($payload != null) {
            if($method == 'PUT') { 
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');  
            } else { 
                //echo('POSTing to '.$url."\n\n" );
                curl_setopt($ch, CURLOPT_POST, 1);
            }
    	    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	    }
	    
	    // SSL Stuff
	    if($port != false) { 
			curl_setopt($ch, CURLOPT_PORT, $port);
		}

		// Send Request and return the result
		$data = curl_exec($ch); curl_close($ch);
		return $data;
	}
}

?>