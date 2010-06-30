<?php
    // Require the PAS API Library
    require('lib/pas-api-wrapper.php');
    
    // Create a PAS_Member Object for the Member you want to work on by instantiating the PAS_Member object with a Member_ID
    $existing_user = new PAS_Member(40403);
    
    // If we have errors show them...
    if($existing_user->getErrors() != false) { 
       print_r($existing_user->getErrors());
       exit();
    }
    
    // We can see what his current data looks like by outputting the raw XML like this:
    echo($existing_user->asXML());
    
    /// NOTE: The Tracker-related data is shown for viewing purposes only.  You are unable to edit this data via the API.
        
    // Now let's change some data on this Member...
    $existing_user->email = 'newEmailAddress123@google.com';
    $existing_user->first_name = 'David';
    
    // Once we're finished making the changes to the data we just have to call the save() method...
    $save = $existing_user->save();

    // If save() returned false then we can see what the errors are via the getErrors() method    
    if($save == false) { 
        print_r($existing_user->getErrors());
    }
?>