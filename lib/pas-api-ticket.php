<?php

class PAS_Ticket extends PAS_API { 
    protected $ticket_slug;
    protected $member_id;
    protected $xml_obj;
    protected $errors;
    
    public function __construct($member_id = null, $ticket_slug = null) { 
        $this->member_id    = $member_id;
        $this->ticket_slug  = $ticket_slug;
        
        if ($ticket_slug) {
          $this->getTicketData();
        } else {
          // We're going to CREATE a New Ticket
          $this->xml_obj = new SimpleXMLElement('<ticket></ticket>');
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
    
    // We can ONLY Save NEW Tickets!
    public function save() { 
        if ($this->ticket_slug == null) {         
          if ($this->member_id) {
            $ticket = $this->sendRequest('/publisher_members/'.$this->member_id.'/tickets.xml', 'POST', $this->asXML());
          } else {
            $ticket = $this->sendRequest('/publisher_member_tickets/external.xml', 'POST', $this->asXML());
          }
          
          if (!parent::hasErrors($ticket)) {
            $this->ticket_slug = $ticket->id;
            $this->xml_obj = $ticket;
            return true;
          }
        } 
        
        return false;
    }
    
    public function addReply($reply_body) { 
        if ($this->member_id && $this->ticket_slug) {
          // Can only reply to an existing ticket!
          $payload = '<ticket_reply><body>'.addslashes($reply_body).'</body></ticket_reply>'; 
          $reply = $this->sendRequest('/publisher_members/'.$this->member_id.'/tickets/'.$this->ticket_slug.'/reply.xml', 'POST', $payload);
          if (!parent::hasErrors($reply)) return true;
        }
        
        return false;
    }
    
    private function getTicketData() { 
      if ($this->member_id) {
        $this->xml_obj = $this->sendRequest('/publisher_members/'.$this->member_id.'/tickets/'.$this->ticket_slug.'.xml', 'GET');
      }
    }
    
}