<?php
    // Require the PAS API Library
    require('lib/pas-api-wrapper.php');
    
    // The Member ID of the user we're going to "play" / test with...
    $member_id = 40403;
    
    //  We can get All Tickets for a Member from the PAS_Member Object
    $member = new PAS_Member($member_id);
    
    // If we have errors let's show them now...
    if($member->getErrors() != false) { 
       print_r($member->getErrors());
       exit();
    }
    
    // We now can fetch all of his tickets with this method.  This returns returns an array of PAS_Ticket Objects.
    $member_tickets = $member->getMemberTickets();
    
    // We can also just fetch a single ticket object like so:
    $ticket = new PAS_Ticket($member_id, 'FENO5364');
    
    // You also have the ability to add replies, on behalf of the Member, to a ticket.
    $ticket->addReply('test');
    
    // We could also open a new Ticket on behalf of this Member:
    $new_ticket = new PAS_Ticket($member_id);
    $new_ticket->subject = 'Testing Helpdesk API';
    $new_ticket->body = 'ABC123 w00tah!';
    
    // Save the new Ticket!
    $new_ticket->save();
?>