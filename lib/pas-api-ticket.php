<?php

class PAS_Ticket extends PAS_API { 
    protected $ticket_slug;
    protected $member_id;
    protected $xml_obj;
    protected $errors;
    
    public function __construct($member_id, $ticket_slug = null) { 
        $this->member_id = $member_id;
        $this->ticket_slug = $ticket_slug;
        
        if($ticket_slug == null) { 
            // We're going to CREATE a New Ticket
            $this->xml_obj = new SimpleXMLElement('<ticket></ticket>');
        } else {
            $this->getTicketData();
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
        if($this->ticket_slug == null) { 
    	    $create = $this->sendRequest('/publisher_members/'.$this->member_id.'/tickets.xml', 'POST', $this->asXML());
    	    if(parent::hasErrors($create)) { 
                return false;
            } return true;
        } else { 
            return false;  // We can ONLY Save NEW Tickets!
        }
    }
    
    public function addReply($reply_body) { 
        if($this->ticket_slug == null) { return false; } // Can only reply to an existing ticket!
        
        $payload = '<ticket_reply><body>'.addslashes($reply_body).'</body></ticket_reply>';  
	    $reply = $this->sendRequest('/publisher_members/'.$this->member_id.'/tickets/'.$this->ticket_slug.'/reply.xml', 'POST', $payload);
	    if(parent::hasErrors($reply)) { 
            return false;
        } return true;
    }
    
    private function getTicketData() { 
	    $get = $this->sendRequest('/publisher_members/'.$this->member_id.'/tickets/'.$this->ticket_slug.'.xml', 'GET');
	    $this->xml_obj = $get;
    }
    
}

?>