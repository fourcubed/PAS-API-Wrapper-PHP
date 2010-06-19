<?php

class PAS_API { 

	const api_token     = 'YOUR_API_TOKEN';         // Your 'API Token' (provided by PAS) goes here.
	const api_secret    = 'YOUR_API_ACCESS_KEY';    // Your 'API Access Key' (provided by PAS) goes here.
	const url           = 'http://publisher.pokeraffiliatesolutions.com'; // Should not need to change this!

    // Method used to send ALL Requests.  Returns a SimpleXMLElement Object.
    public static function sendRequest($path, $method='GET', $payload=null) { 
        // If called by a Child Class remove any stored errors
        if(isset($this)) { $this->errors = null; }
        
        // Form the Full Request URL & Send the actual request!
        $xml_from_pas = curl::sendRequest( self::url.$path.self::signRequest($method, $path) , $payload, $method);

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

    public function getErrors() {
        return $this->errors;
    }

    public static function signRequest($method, $path) { 
        $timestamp = time();
        $signature = '?api_token='.self::api_token.'&timestamp='.$timestamp.'&signature=' . urlencode( base64_encode( hash_hmac('sha1', self::api_token . $method . $path . $timestamp , self::api_secret, true)));
        return $signature;
    }
}

class PAS_Member extends PAS_API { 
    protected $member_id = null;
    protected $xml_obj;
    protected $errors;
    
    public function __construct($member_id = null) { 
        $this->member_id = $member_id;
        
        // Are we Viewing an existing Member or Creating a new one?
        if($member_id != null) { 
            $this->getMemberData();
        } else { 
            // We're going to CREATE a New User
            $this->xml_obj = new SimpleXMLElement('<member></member>');
        }
    }
    
    public function __get($var) { 
        return $this->xml_obj->$var;
    }
    
    public function __set($var, $value) { 
        $this->xml_obj->$var = $value;
    }
    
    public function asXML() { 
        return $this->xml_obj->asXML();
    }
    
    public function save() { 
        if($this->member_id != null) { 
            return $this->updateMember();
        } else { 
            return $this->createMember();
        }
    }
    
    public function getMemberTrackers() { 
        foreach($this->xml_obj->member_trackers->member_tracker as $tracker_data) { 
            $x++;
            foreach($tracker_data as $k => $v) { $all_trackers[$x][$k] = (string) $v; }
        }
        return $all_trackers;
    }
    
	public function getRemoteAuthToken() { 
	    $get = $this->sendRequest('/remote_auth.xml', 'POST', '<member_id>'.$this->member_id.'</member_id>');
	    return (string) $get[0];
	}
    
    private function getMemberData() { 
	    $this->xml_obj = $this->sendRequest('/publisher_members/'.$this->member_id.'.xml');
        if(parent::hasErrors($this->xml_obj)) { 
            return false;
        } return true;
	}
	
	private function updateMember() { 
	    $update = $this->sendRequest('/publisher_members/'.$this->member_id.'.xml', 'PUT', $this->asXML());
        if(parent::hasErrors($update)) { 
            return false;
        } return true;
	}
	
	private function createMember() { 
	    //var_dump($this->asXML());
	    $create = $this->sendRequest('/publisher_members.xml', 'POST', $this->asXML());
	    if(parent::hasErrors($create)) { 
            return false;
        } return true;
	}    
}

?>