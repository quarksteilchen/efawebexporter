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

// Default: Fetch Data
$query = 'SELECT * FROM ew_logbook WHERE lbts!="0000-00-00 00:00:00" ORDER BY lbts DESC LIMIT 100';

$resArr = $db->queryToArray($query);

// Fetch ts of last export

?>
<!DOCTYPE html>
<html>
  <head>
    <title>ISTER Nachtrag - Ansehen</title>
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
        <h1>Nachträge ansehen</h1>
      </div><!-- /header -->
      <div data-role="content">
        <p><?= EWBasic::getErrHtml($err); ?><?= EWBasic::getInfoHtml($info); ?>
        Club: ISTER</p>
        
        <a href="?page=export&generate=newexport">= neuen Export generieren</a>

<table style="width:100%">
  <tr>
    <th>entered</th>
    <th>start</th> 
    <th>boat</th>
    <th>crew</th>
    <th>dist</th>
  </tr>
  <?php
  for($i=0; $i<count($resArr); $i++)
  {
      $da = $resArr[$i];
  ?>
  <tr>
    <td><?= $da['lbts'] ?></td>
    <td><?= $da['Date'].' '.$da['StartTime'] ?></td>
    <td><?= $da['Boat'] ?></td>
    <td><?= $da['Cox'].' '.$da['Crew1'].' '.$da['Crew2'] ?></td>
    <td><?= $da['Distance'] ?></td>
  </tr>
  <?php
  }
  ?>
  <tr>
    <td colspan="5">--- ab hier bereits exportiert ---</td>
  </tr>
</table>
        
      </div><!-- /content -->

      <div data-role="footer">
        <h4>End of List</h4>
      </div><!-- /footer -->

    </div><!-- /page -->
  </body>
</html>