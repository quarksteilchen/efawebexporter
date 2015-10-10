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
        })
      ;
/*
      $(document).on("pageshow", function(e) {
        console.log("Ready to bring the awesome.");

        $("#searchField").on("input", function(e) {
          var sugList = $("#suggestions");
          var text = $(this).val();
          if(text.length < 1) {
            sugList.html("");
            sugList.listview("refresh");
          } else {
            $.get("<?= $ewconf['baseurl']; ?>api/acnames.php", {q:text}, function(res,code) {
              var str = "";
              for(var i=0, len=res.length; i<len; i++) {
                str += '<li>'+res[i]+'</li>';
              }
              sugList.html(str);
              sugList.listview("refresh");
              console.dir(res);
            },"json");
          }
        });
      });
      
      $(document).on("click", ".suggestions li", function() {
        var selectedItem = $(this).html();
        $(this).parent().find('input').val(selectedItem);
        $('.suggestions').hide();
      });
*/
    $.mobile.document
    // "boat-menu" is the ID generated for the listview when it is created
    // by the custom selectmenu plugin. Upon creation of the listview widget we
    // want to prepend an input field to the list to be used for a filter.
    .on( "listviewcreate", "#boat-menu", function( e ) {
        var input,
            listbox = $( "#boat-listbox" ),
            form = listbox.jqmData( "filter-form" ),
            listview = $( e.target );
        // We store the generated form in a variable attached to the popup so we
        // avoid creating a second form/input field when the listview is
        // destroyed/rebuilt during a refresh.
        if ( !form ) {
            input = $( "<input data-type='search'></input>" );
            form = $( "<form></form>" ).append( input );
            input.textinput();
            $( "#boat-listbox" )
                .prepend( form )
                .jqmData( "filter-form", form );
        }
        // Instantiate a filterable widget on the newly created listview and
        // indicate that the generated input is to be used for the filtering.
        listview.filterable({ input: input });
    })
    // The custom select list may show up as either a popup or a dialog,
    // depending how much vertical room there is on the screen. If it shows up
    // as a dialog, then the form containing the filter input field must be
    // transferred to the dialog so that the user can continue to use it for
    // filtering list items.
    //
    // After the dialog is closed, the form containing the filter input is
    // transferred back into the popup.
    .on( "pagebeforeshow pagehide", "#boat-dialog", function( e ) {
        var form = $( "#boat-listbox" ).jqmData( "filter-form" ),
            placeInDialog = ( e.type === "pagebeforeshow" ),
            destination = placeInDialog ? $( e.target ).find( ".ui-content" ) : $( "#boat-listbox" );
        form
            .find( "input" )
            // Turn off the "inset" option when the filter input is inside a dialog
            // and turn it back on when it is placed back inside the popup, because
            // it looks better that way.
            .textinput( "option", "inset", !placeInDialog )
            .end()
            .prependTo( destination );
    });
    </script>
</head>
<body>
    <div data-role="page">
 
        <div data-role="header">
            <h1>Nachtrag</h1>
        </div><!-- /header -->
 
        <div data-role="content">
            <p>Hello world</p>
<div class="ui-field-contain">
<label for="date-from">Datum:</label>
<input type="date" name="date-from" id="date-from" value="<?= isset($_POST['date-from'])?$_POST['date-from']:date('Y-m-d'); ?>">
</div>

<div class="ui-field-contain">
<label for="date-to">Datum (bis):</label>
<input type="date" name="date-to" id="date-to" value="<?= isset($_POST['date-to'])?$_POST['date-to']:date('Y-m-d'); ?>">
</div>

<div class="ui-field-contain">
<label for="boat">Boot:</label>
<!--<input type="text" name="boat" id="boat" value="">-->

<select id="boat" data-native-menu="false">
<?php
  $d = new EFaData($db);
  $optionStr = $d->getListOptions('efa2boats');
  echo($optionStr);
?>
</select>

</div>

<div class="ui-field-contain">
<label for="cox">Steuermann:</label>
<!-- <input type="text" name="cox" id="cox" value=""> -->
<ul id="autocomplete" data-role="listview" data-inset="true" data-filter="true" data-filter-placeholder="Nachname, Vorname ..." data-filter-theme="a"></ul>
</div>
            
<div class="ui-field-contain">
<label for="coo">Steuermann:</label>
<input type="text" name="coo" id="searchField" placeholder="Search">
<ul class="suggestions" data-role="listview" data-inset="true" data-filter="true" data-filter-placeholder="Nachname, Vorname ..." data-filter-theme="a"></ul>
</div>
            
<div class="ui-field-contain">
<label for="co1">AC Steuermann:</label>
<input type="text" name="co1" id="accox">
</div>

<?php
for($i=1; $i<=8; $i++)
{
  $padded_num = str_pad($i, 2, "0", STR_PAD_LEFT);
?>
<div class="ui-field-contain">
  <label for="crew[<?= $i ?>]">Mannschaft <?= $i ?>:</label>
  <input type="text" name="crew[<?= $i ?>]" id="crew<?= $padded_num ?>" value="<?= $_POST['crew'][$i] ?>">
</div>
<?php 
}
?>

<label for="time-from">Abfahrt:</label>
<input type="text" name="time-from" id="time-from" value="<?= $_POST['time-from'] ?>">

<label for="time-to">Ankunft:</label>
<input type="text" name="time-to" id="time-to" value="<?= $_POST['time-to'] ?>">

<label for="track">Ziel/Strecke:</label>
<input type="text" name="track" id="track" value="<?= $_POST['track'] ?>">

<label for="notes">Bemerkungen:</label>
<input type="text" name="notes" id="notes" value="<?= $_POST['notes'] ?>">

<!-- Final source: types.efa2types -->
<label for="session" class="select">Fahrtart:</label>
<select name="session" id="session">
  <option value="NORMAL" selected="selected">normale Fahrt</option>
  <option value="ERG">Ergo</option>
  <option value="INSTRUCTION">Ausbildung</option>
  <option value="JUMREGATTA">JuM-Regatta</option>
  <option value="LATEENTRY">Kilometernachtrag</option>
  <option value="MOTORBOAT">Motorboot</option>
  <option value="REGATTA">Regatta</option>
  <option value="TOUR">Wanderfahrt</option>
  <option value="TRAINING">Training</option>
  <option value="TRAININGCAMP">Trainingslager</option>
</select>

<fieldset data-role="controlgroup">
    <legend>Single checkbox:</legend>
    <label for="anotherentry">Nach dem Senden weiteren Eintrag erstellen.</label>
    <input type="checkbox" name="anotherentry" id="anotherentry">
</fieldset>

<label for="comment">Kommentar:</label>
<textarea name="comment"></textarea>

<label for="submit">Send:</label>
<button class="ui-shadow ui-btn ui-corner-all" type="submit" id="submit">Senden</button>

        </div><!-- /content -->
 
        <div data-role="footer">
          <h4>My Footer</h4>
        </div><!-- /footer -->
 
    </div><!-- /page -->
</body>
</html>