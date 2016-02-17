<?php
// Uploader-Page

require_once(dirname(__FILE__) . '/../inc/basicfunc.inc.php');
$msg = '';

if(!isset($p))
{
  die('ERROR: Not all variables set (project). page:entryform');
}

// process POST-data:
if(isset($_POST['check_ts']))// && isset($_POST['efazipfile']))
{
  if(isset($_FILES['efazipdata']))
  {
    // get file info
    if($_FILES['efazipdata']['error']!=UPLOAD_ERR_OK)
    {
      die('ERROR: Efa Zipfile Upload Error: '.$_FILES['efazipdata']['error']);
    }
    if($_FILES['efazipdata']['type']!='application/x-zip-compressed')
    {
      die('ERROR: Efa Zipfile is not a Zip-file.');
    }
    // choose final location of file
    $uploadfilename = $ewconf['basedir'].$ewconf['uploaddir'].$_FILES['efazipdata']['name'];
    if(!move_uploaded_file($_FILES['efazipdata']['tmp_name'], $uploadfilename))
    {
      die('ERROR: Uploaded file not movable to '.$uploadfilename.'. You should change access rights there.');
    } else {
      
    }
    
    $original_filename = $_FILES['efazipdata']['name'];

  }
  $msg = "POSTDATA<pre>";
  $msg .= var_export($_POST,true);
  $msg .= var_export($_FILES,true);
  $msg .= '</pre>';
  //die('done');
}
else
{
  $msg = "NO POST VISIBLE";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>ISTER Nachtrag</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    
</head>
<body>
  <div data-role="page">
    <div data-role="header">
      <h1>Upload</h1>
    </div><!-- /header -->
    <div data-role="content">
      <?= $msg; ?>
      <form data-ajax="false" enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF']; ?>?page=upload" method="POST">
          <!-- MAX_FILE_SIZE muss vor dem Dateiupload Input Feld stehen -->
          <input type="hidden" name="MAX_FILE_SIZE" value="400000" />
          <input type="hidden" name="check_ts" value="<?= EWBasic::now(); ?>" />
          <!-- Der Name des Input Felds bestimmt den Namen im $_FILES Array -->
          Diese Datei hochladen: <input name="efazipfile" type="file" />
          <input type="submit" value="Hochladen" />
      </form>
    </div>
  </div>
</body>