<?php
  // Be sure to first enter your API access key and secret here
	require('lib/pas-api-wrapper.php');

	$api = new PAS_API();
	
	// Fetch a list of ALL members
	$page = 1;
	do {
	  $members = $api->getMembers(null, $page);
	  echo "Found " . count($members) . " members\n";
	  $page += 1;
	} while (count($members) > 0);
?>
