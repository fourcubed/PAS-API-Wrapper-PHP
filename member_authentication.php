<?php
  require_once('lib/pas-api-wrapper.php');
  
  if ($member = PAS_Member::getMemberByLogin('test@test.com')) {
    if ($authenticated = $member->authenticate('testing')) {
      echo("Valid password.\n");
      exit();
    }
  }
  
  die("Your login/password is incorrect.\n");