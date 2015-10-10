<?php
require_once(dirname(__FILE__) . '/../inc/basicfunc.inc.php');
require_once(dirname(__FILE__) . '/../inc/db.inc.php');
// all headers for "Fahrtenbuch": EntryId|Date|EndDate|Boat|BoatVariant|Cox|Crew1|Crew2|Crew3|Crew4|Crew5|Crew6|Crew7|Crew8|Crew9|Crew10|Crew11|Crew12|Crew13|Crew14|Crew15|Crew16|Crew17|Crew18|Crew19|Crew20|Crew21|Crew22|Crew23|Crew24|BoatCaptain|StartTime|EndTime|Destination|DestinationVariantName|WatersList|Distance|Comments|SessionType|SessionGroup|EfbSyncTime|Open|ChangeCount|LastModified

// (0) Preparation ...
if(isset($_GET['from']))
{
  if(EWBasic::validateDate($_GET['from'])===true)
    $from = $_GET['from'];
  else
    die('ERROR: no valid from-date.');
}
else
  $from = '0000-00-00 00:00:00';

if(isset($_GET['to']))
{
  if(EWBasic::validateDate($_GET['to'])===true)
    $to = $_GET['to'];
  else
    die('ERROR: no valid to-date');
}
else
  $to = EWBasic::now();

require_once('../inc/db.inc.php');
$db = new EWSql();

// (1) get data from DB
$query = 'SELECT * FROM ew_logbook WHERE p=1 AND eDate>="'.$from.'" AND eDate<="'.$to.'"';
if(!$result = $db->query($query))
{
  die('some mysql-error: '.$db->error);
}
while($row = $result->fetch_assoc())
{
  echo($row['Date'] . ' / ' . $row['Boat'] . '<br />'."\r\n");
}
$result->free();


// (2) write status info to DB
// e.g. exported file at y-m-d h:i:s, ...

// (3) put into right format
// with | inbetween...

// (4) archive exportfile
// zip, cp, ...


?>