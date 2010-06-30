<?php
    // Require the PAS API Library
    require('lib/pas-api-wrapper.php');
    
    // Create a new PAS_Member Object
    $existing_user = new PAS_Member(40403);
    
    // If we have errors show them...
    if($existing_user->getErrors() != false) { 
       print_r($existing_user->getErrors());
       exit();
    }
    
    // Grab a 'Remote Authentication Token' by using the getRemoteAuthToken() method:
    $remote_auth_login_token = $existing_user->getRemoteAuthToken();

    // Forward User to Rakeback Website (to set cookies), the user will then be auto-forwarded back to the $return_url.
    $return_url = 'http://www.YOUR_MAIN_WEBSITE_HERE.com/';
    
    // Send the User off to the Rakeback Website!
    header('Location: http://rakeback.YOUR_WEBSITE.com/?remote_auth_token='.$remote_auth_login_token.'&redirect_url='.urlencode($return_url));
    exit();
?>