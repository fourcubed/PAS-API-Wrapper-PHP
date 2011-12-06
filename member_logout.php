<?php    
    // Forward User to Rakeback Website to unset his login cookies.  The user will then be immediately forwarded back to the $return_url.
    $return_url = 'www.YOUR_MAIN_WEBSITE_HERE.com/';
      // NOTE: the 'http://' is automatically added for you by PAS.  Do NOT include it yourself!
    
    // Send the User off to the Rakeback Website to be logged out!  The user gets forwarded back to $return_url immediately after being logged out.
    header('Location: http://rakeback.YOUR_WEBSITE.com/logout?forward='.urlencode($return_url));
    exit();
?>