<?php
    // Require the PAS API Library & the Curl Library that it utilizes for sending requests
    require('lib/pas-api-wrapper.php');
    
    // Get the Website Offers (this is where we get the $offer_id from!)
    $offers = PAS_API::getOffers(67);
    
    // Create a PAS_Member Object for the Member you want to work on by instantiating the PAS_Member object with a Member_ID
    $member = new PAS_Member(40403);
    
    // If we have errors show them...
    if($member->getErrors() != false) { 
       print_r($existing_user->getErrors());
       exit();
    }

    // Add the Member Tracker to the Member!
    $offer_id = 126389;  // This is the Offer_ID --> Can be found on the Offers XML Feed
    $tracker_identifier = 'test-from-api';
    
    // Add the tracker!
    $add_tracker = $member->addTracker($tracker_identifier, $offer_id);
?>