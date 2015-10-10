<?php
require_once(dirname(__FILE__) . '/../inc/basicfunc.inc.php');
require_once(dirname(__FILE__) . '/../inc/db.inc.php');
require_once(dirname(__FILE__) . '/../inc/efadata.inc.php');

  // required variables from include:
  if(!isset($p))
  {
    die('ERROR: Not all variables set (project). page:entryform');
  }
  
  // process POST-data:
  if(isset($_POST))
  {
    // (1) check if minimum is there
    if(isset($_POST['date-from'],$_POST['boat']))
    {
      // (2) prepare entries for database
      var_dump($_POST);
      // TODO: realescape; parse; check
      $pdfrom = $_POST['date-from'];
      $pdto = $_POST['date-to'];
      $pboat = $_POST['boat'];
      $pcox = $_POST['cox'];
      $pcrew = $_POST['crew']; // array
      $ptfrom = $_POST['time-from'];
      $ptto = $_POST['time-to'];
      $ptrack = $_POST['track'];
      $pdistance = 0;
      $pnotes = $_POST['notes'];
      $psession = $_POST['session']; // "fahrtart" .. training, ...
      $panother = $_POST['anotherentry'];
      $pnotes = $_POST['notes'];
      
      if($panother=='checked')
      {
        // after this, show infomessage and entry-form again.
      }
      
      // (2b) prepare some entries
      $crew = array();
      $crewidx = 1;
      foreach ($pcrew as $onecrew)
      {
        $crew[] = 'Crew'.$crewidx.'="'.trim($onecrew).'"'; // TODO: realescape, trim
        $crewidx++;
      }
      
      // (3) put entries into database
      $query = 'INSERT INTO ew_logbook SET '
              . 'lbts=NOW(), lbp='.$p.', '
              . 'Date="'.$pdfrom.'", EndDate="'.$pdto.'", '
              . 'Boat="'.$pboat.'", '
              . 'Cox="'.$pcox.'", '.implode(', ', $crew).', '
              . 'StartTime="'.$ptfrom.'", EndTime="'.$ptto.'", '
              . 'Destination="'.$ptrack.'", Distance="'.$pdistance.'", '
              . 'Comments="'.$pnotes.'"';
      $db->query($query);
    }
    else
    {
      // not enough information. print warning and fill form again
    }
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
    <script src="lib/jquery-textext-131/src/js/textext.core.js"></script>
    <script language="JavaScript">
      
      $(document).on("pageshow", function(e) {
        console.log("hey");
        
    $('#accox')
      .textext({
        plugins : 'autocomplete',
        prompt  : 'Type something...'
      })
      .bind('getSuggestions', function(e, data)
      {
            var list = [
                    'Basic',
                    'Closure',
                    'Cobol',
                    'Delphi',
                    'Erlang',
                    'Fortran',
                    'Go',
                    'Groovy',
                    'Haskel',
                    'Java',
                    'JavaScript',
                    'OCAML',
                    'PHP',
                    'Perl',
                    'Python',
                    'Ruby',
                    'Scala'
                ],
                textext = $(e.target).textext()[0],
                query = (data ? data.query : '') || ''
                ;

            $(this).trigger(
                'setSuggestions',
                { result : textext.itemManager().filter(list, query) }
            );
        });
      
  }); // end onPageshow

    </script>
</head>
<body>
    <div data-role="page">
 
        <div data-role="header">
            <h1>Nachtrag</h1>
        </div><!-- /header -->
 
        <div data-role="content">
            <p>Hello world</p>

            
            <p id="testid">testid</p>
<div class="ui-field-contain">
<label for="co1">AC Steuermann:</label>
<input type="text" name="co1" id="accox">
</div>

<label for="submit">Send:</label>
<button class="ui-shadow ui-btn ui-corner-all" type="submit" id="submit">Senden</button>

        </div><!-- /content -->
 
        <div data-role="footer">
          <h4>My Footer</h4>
        </div><!-- /footer -->
 
    </div><!-- /page -->
</body>
</html>