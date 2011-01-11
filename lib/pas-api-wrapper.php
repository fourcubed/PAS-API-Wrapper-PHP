<?php

require_once('curl.php');
require_once('pas-api-member.php');
require_once('pas-api-ticket.php');

class PAS_API { 

	const api_token     = 'YOUR_API_TOKEN';         // Your 'API Token' (provided by PAS) goes here.
	const api_secret    = 'YOUR_API_ACCESS_KEY';    // Your 'API Access Key' (provided by PAS) goes here.
	const url           = 'https://publisher.pokeraffiliatesolutions.com'; // Should not need to change this!

    // Method used to send ALL Requests.  Returns a SimpleXMLElement Object.
    public static function sendRequest($path, $method='GET', $payload=null, $additional_get_params=null) { 
        // If called by a Child Class remove any stored errors
        if(isset($this)) { $this->errors = null; }
        
        if($additional_get_params == null) { $add_params = ''; } else { $add_params = $additional_get_params; }
        
        // Form the Full Request URL & Send the actual request!
        $xml_from_pas = curl::sendRequest( self::url.$path.self::signRequest($method, $path).$add_params , $payload, $method);

        try { 
            $obj = new SimpleXMLElement( $xml_from_pas );
         } catch(Exception $e) { $obj = new SimpleXMLElement('<errors><error>Fatal Exception LOCALLY while instantiating a new SimpleXMLElement object.</error></errors>'); }

        return $obj;
    }
    
    public function hasErrors(&$xml_object) { 
        foreach($xml_object->error as $error) { 
            $error_array[] = (string) $error;
        }
        if(count($error_array) > 0) {
            $this->errors = $error_array;
            return true;
        }
        return false;
    }
    
    // Returns a simple associative array of Website Offers that are active / can be added
    public static function getOffers($website_id) { 
        $get_offers = PAS_API::sendRequest('/website_offers.xml', 'GET', null, '&website_id='.$website_id);
        foreach($get_offers->offer as $offer) { 
            unset($array);
            foreach($offer as $k => $v) { 
                $array[(string) $k] = trim((string) $v);
            }
            $return_array[(integer)$offer->id] = $array;
        }
        return $return_array;
    }

    public function getErrors() {
        return $this->errors;
    }

    public static function signRequest($method, $path) { 
        $timestamp = time();
        $signature = '?api_token='.self::api_token.'&timestamp='.$timestamp.'&signature=' . urlencode( base64_encode( hash_hmac('sha1', self::api_token . $method . $path . $timestamp , self::api_secret, true)));
        return $signature;
    }

	// This really belongs in a 'Utilities' Class, but adding it here to save time!  :)
	public static function date2timestamp($date) {
		$date = str_replace('-','',$date);
		return mktime(1,1,1, substr($date,4,2), substr($date,6,2), substr($date,0,4));
	}
}

?>