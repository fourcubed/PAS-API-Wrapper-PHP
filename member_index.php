<?php
  // Be sure to first enter your API access key and secret here
	require('lib/pas-api-wrapper.php');

	$api = new PAS_API();
	
	// Fetch a list of ALL members for a given website
	$members = $api->getMembers(YOUR_WEBSITE_ID);
	
	print_r($members);	
?>
