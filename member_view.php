<?php
    // Require the PAS API Library
    require('lib/pas-api-wrapper.php');
    
    // Create a PAS_Member Object for the Member you want to work on by instantiating the PAS_Member object with a Member_ID
    $existing_user = new PAS_Member(40403);
    
    // If we have errors show them!
    if($existing_user->getErrors() != false) { 
       print_r($existing_user->getErrors());
       exit();
    }
    
    // Print out the full raw data, from the SimpleXMLElement object, that we have on this user:
    var_dump($existing_user);

    // Retrieve an array of MemberTrackers that this Member has
    var_dump($existing_user->getMemberTrackers());

    // We can also retrieve single values like so:
    echo("Username: ".$existing_user->login."\n");
    echo("Email: ".$existing_user->email."\n");
    echo("Last Name: ".$existing_user->last_name."\n");

    // Note: Any variables that you can access or change are available for access via the API.  See the Docs for a full list of fields.
?>