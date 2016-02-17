<?php
require_once(dirname(__FILE__) . '/../inc/basicfunc.inc.php');
require_once(dirname(__FILE__) . '/../inc/db.inc.php');
// all headers for "Fahrtenbuch": EntryId|Date|EndDate|Boat|BoatVariant|Cox|Crew1|Crew2|Crew3|Crew4|Crew5|Crew6|Crew7|Crew8|Crew9|Crew10|Crew11|Crew12|Crew13|Crew14|Crew15|Crew16|Crew17|Crew18|Crew19|Crew20|Crew21|Crew22|Crew23|Crew24|BoatCaptain|StartTime|EndTime|Destination|DestinationVariantName|WatersList|Distance|Comments|SessionType|SessionGroup|EfbSyncTime|Open|ChangeCount|LastModified

$trenn = '|';
$nl = "\r\n";

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
$query = 'SELECT * FROM ew_logbook WHERE p='.$p.' AND Source="NACHTRAG" AND eDate>="'.$from.'" AND eDate<="'.$to.'"';
$export_dbcontent = $db->queryToArray($query);

// (2) write status info to DB
// e.g. exported file at y-m-d h:i:s, ...
$query = 'UPDATE ew_logbook set lbexportts="'.$to.'" WHERE p='.$p.' AND Source="NACHTRAG" AND eDate>="'.$from.'" AND eDate<="'.$to.'"';
$db->query($query);

// (3) put into right format
// with | inbetween...
$export_content = array();
$export_content[] = 'Date|EndDate|Boat|BoatVariant|Cox|Crew1|Crew2|Crew3|Crew4|Crew5|Crew6|Crew7|Crew8|Crew9|Crew10|Crew11|Crew12|Crew13|Crew14|Crew15|Crew16|Crew17|Crew18|Crew19|Crew20|Crew21|Crew22|Crew23|Crew24|BoatCaptain|StartTime|EndTime|Destination|DestinationVariantName|WatersList|Distance|Comments|SessionType|SessionGroup|EfbSyncTime|Open|ChangeCount|LastModified'; // Header
foreach ($export_dbcontent as $eline)
{
  $tmp = $eline['Date'].$trenn;
  $tmp.= $eline['EndDate'].$trenn;
  $tmp.= $eline['Boat'].$trenn;
  $tmp.= $eline['BoatVariant'].$trenn;
  $tmp.= $eline['Cox'].$trenn;
  for($i=1;$i<=24;$i++)
  {
    $tmp.= $eline['Crew'.$i].$trenn;
  }
  $tmp.= $eline['BoatCaptain'].$trenn;
  $tmp.= $eline['StartTime'].$trenn;
  $tmp.= $eline['EndTime'].$trenn;
  $tmp.= $eline['Destination'].$trenn;
  $tmp.= $eline['DestinationVariantName'].$trenn;
  $tmp.= $eline['WatersList'].$trenn;
  $tmp.= $eline['Distance'].$trenn;
  $tmp.= $eline['Comments'].$trenn;
  $tmp.= $eline['SessionType'].$trenn;
  $tmp.= $eline['SessionGroup'].$trenn;
  $tmp.= $eline['EfbSyncTime'].$trenn;
  $tmp.= $eline['Open'].$trenn;
  $tmp.= $eline['ChangeCount'].$trenn;
  $tmp.= $eline['LastModified'].$trenn;
  $export_content[] = $tmp;
}
$export_filecontent = implode($nl, $export_content);

// (4) archive exportfile
// zip, cp, ...
$export_filename = 'file.csv';
file_put_contents($export_filename, $export_filecontent);

?>