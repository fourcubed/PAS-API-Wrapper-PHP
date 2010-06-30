<?php
    // Require the PAS API Library
    require('lib/pas-api-wrapper.php');
    
    // Create a PAS_Member Object for the Member you want to work on by instantiating the PAS_Member object with a Member_ID
    $m = new PAS_Member(40403);

    //////////////////////////////////////////////
    /////////////  STATS  ///////////////////////

    // This will return the Member's stats in a SimpleXMLElement Object
    $m->getMemberStats();
    // This method also can take a $start_date & $end_date ('YYYY-MM-DD' format)
    // The returned data will be grouped by Member Tracker
    
    // Fetching Referral Data can be done, too:
    $m->getMemberRAFStats('2010-04-01', '2010-05-21');      // The returned data is grouped by referred Member
    
    
    //////////////////////////////////////////////
    ///////////// PAYMENTS ///////////////////////
    
    // Payments History can be retrieved like this:
    $m->getMemberPayments('2001-01-01', '2010-10-01', 1);   // Takes:  $Start_date, $End_date, and $Page_Number
    
    // Fetch Member Cashout History
    $m->getMemberCashouts('2001-01-01', '2010-10-01');      // Takes:  $Start_date, $End_date
    
    // Get the Available Cashout Methods (returns a simple associative array of the methods)
    $available_methods = $m->getMemberCashoutMethods();
    
    // Get Member's balance data
    $balances = $m->getMemberBalances();

    // Initiate a Cashout on behalf of the Member
    $cashout_details = '(API TEST - IGNORE!) Please mail my money to me at 915 Washington Avenue!  Thanks!';
    $cashout_method_id = '5';
    
    // Boom --> Cash him out!
    $cashout = $m->newMemberCashout($cashout_details, $cashout_method_id);
    
    if($m->getErrors()) { 
        var_dump($cashout);
    } else { 
        // SUCCESS!  We cashed the User's Available Balance out!
    }
?>