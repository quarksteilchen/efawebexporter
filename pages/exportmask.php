<?php
require_once(dirname(__FILE__) . '/../inc/basicfunc.inc.php');
require_once(dirname(__FILE__) . '/../inc/db.inc.php');
require_once(dirname(__FILE__) . '/../inc/efadata.inc.php');
$info = NULL;
$err = NULL;

ini_set("display_errors", 1);

// required variables from include:
if (!isset($p))
{
  die('ERROR: Not all variables set (project). page:entryform');
}

if(isset($_GET['generate']) && $_GET['generate']=='newexport')
{
  $timestamp = time();
  // generate new export file
  $query = 'SELECT * FOM ew_logbook WHERE lbexportts IS NULL AND lbts<="'.$timestamp.'"';
  $lbArr = $db->query($query);
  
  $d = new EFaData($db);
  $export_content = $d->exportLB2Csv($lbArr);
  echo($optionStr);
  
  // mark the data in the tables as exported
  $query = 'UPDATE ew_logbook SET lbexportts="'.$timestamp.'" WHERE lbexportts IS NULL AND lbts<="'.$timestamp.'"';
}

// Default: Fetch Data
$query = 'SELECT * FROM ew_logbook WHERE lbts!="0000-00-00 00:00:00" ORDER BY lbts DESC LIMIT 100';
$resArr = $db->queryToArray($query);

// Fetch ts of last export

?>
<!DOCTYPE html>
<html>
  <head>
    <title>ISTER Nachtrag - Export</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <script language="JavaScript">
    </script>
  </head>
  <body>
    <div data-role="page">
      <div data-role="header">
        <h1>Nachträge exportieren</h1>
      </div><!-- /header -->
      <div data-role="content">
        <p><?= EWBasic::getErrHtml($err); ?><?= EWBasic::getInfoHtml($info); ?>
        Club: ISTER</p>
        
        <a href="?page=export&generate=newexport">= neuen Export mit aktuellen Daten generieren</a>

<table style="width:100%">
  <tr>
    <th>exportiert</th>
    <th>start</th> 
    <th>boat</th>
    <th>crew</th>
    <th>dist</th>
    <th>DL</th>
  </tr>
  <?php
  $exported_first = true;
  $new_first = true;
  for($i=0; $i<count($resArr); $i++)
  {
      $da = $resArr[$i];
      if(is_null($da['lbexportts']) && $exported_first==true)
      {
        $exported_first = false;
  ?>
  <tr>
    <td colspan="6">--- Noch nicht exportiert: ---</td>
  </tr>
  <?php } else if(!is_null($da['lbexportts']) && $new_first==true) { ?>
  <tr>
    <td colspan="6">--- Vergangene Exporte: ---</td>
  </tr>
  <?php } ?>
  <tr>
    <td><?= $da['lbexportts'] ?></td>
    <td><?= $da['Date'].' '.$da['StartTime'] ?></td>
    <td><?= $da['Boat'] ?></td>
    <td><?= $da['Cox'].' '.$da['Crew1'].' '.$da['Crew2'] ?></td>
    <td><?= $da['Distance'] ?></td>
    <td><a href="?page=export&req_file=abc.csv">Download</a></td>
  </tr>
  <?php
  }
  ?>
</table>
        
      </div><!-- /content -->

      <div data-role="footer">
        <h4>End of List</h4>
      </div><!-- /footer -->

    </div><!-- /page -->
  </body>
</html>