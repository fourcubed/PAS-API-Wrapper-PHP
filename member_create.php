<?php
    // Require the PAS API Library & the Curl Library that it utilizes for sending requests
    require('lib/pas-api-wrapper.php');
    require('lib/curl.php');
    
    // Create a new PAS_Member Object
    $new_user = new PAS_Member();
    
    // Put in all of the variables that you have / want to store (This list is *NOT* exhaustive, see the full documentation)
    $new_user->website_id = '67555';                    // Website ID   *Required
    $new_user->login = 'Arthur';                        // Username     *Required
    $new_user->password = 'ksjdi4nmndjfne';             // Password     *Required [it is recommended to set this to a long random string]
    $new_user->email = 'theemailman@yourdomain.com';    // Email        *Required
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //   Once we have all of the fields in place above we can call the save() method
    //       Save() returns TRUE on success, FALSE on failure.  
    //          If save() returns FALSE you can call the "getErrors()" method to retrieve an array of errors
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Save!
    $save = $new_user->save();

    // If save() returned false then we can see what the errors are via the getErrors() method    
    if($save == false) { 
        print_r($new_user->getErrors());
    }
?>