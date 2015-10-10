<?php
/*
 * autocompletes names
 */

if(!isset($_REQUEST['q']))
  die();

require_once(dirname(__FILE__) . '/../config.inc.php'); // $ewconf
require_once(dirname(__FILE__) . '/../inc/db.inc.php'); // $db

$q = $db->real_escape_string($_REQUEST['q']);

if(strlen($q)<3)
  die('[]');

$query = 'SELECT CONCAT_WS(", ",LastName,FirstName) AS name FROM ew_person WHERE CONCAT_WS(", ",LOWER(LastName),LOWER(FirstName)) LIKE ("%'.strtolower($q).'%")';

// echo($query); // debug

$list = $db->queryTo1DArray($query);

// JSON-output
echo('["'.implode('","',$list).'"]');
?>