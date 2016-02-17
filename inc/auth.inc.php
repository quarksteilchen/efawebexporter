<?php
// Handles Authentication for efaexporter.
// Always include this file.

require_once(dirname(__FILE__) . '/../config.inc.php'); // $ewconf
require_once(dirname(__FILE__) . '/../lib/dDLogin/Login.php');

$l = new Login($db);
if(!$l->isUserLoggedIn())
{
  require('pages/login.php');
  exit();
}
else
{
  
}

?>