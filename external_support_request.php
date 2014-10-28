<?php
  require_once('lib/pas-api-wrapper.php');
  
  $ticket = new PAS_Ticket();
  $ticket->email = "test@test.com";
  $ticket->body = "Example external support request email (no authenticated member)";
  
  if ($ticket->save()) {
    die("Ticket #" . $ticket->id . " successfully submitted!\n");
  } else {
    var_dump($ticket);
  }