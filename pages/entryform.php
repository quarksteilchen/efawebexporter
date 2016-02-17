<?php
require_once(dirname(__FILE__) . '/../inc/basicfunc.inc.php');
require_once(dirname(__FILE__) . '/../inc/db.inc.php');
require_once(dirname(__FILE__) . '/../inc/efadata.inc.php');
$showform = true;
$info = NULL;
$err = NULL;

$session_dict = array(
    'NORMAL'=>'normale Fahrt',
    'ERG'=>'Ergo',
    'INSTRUCTION'=>'Ausbildung',
    'JUMREGATTA'=>'JuM-Regatte',
    'LATEENTRY'=>'Kilometernachtrag',
    'MOTORBOAT'=>'Motorboot',
    'REGATTA'=>'Regatta',
    'TOUR'=>'Wanderfahrt',
    'TRAINING'=>'Training',
    'TRAININGCAMP'=>'Trainingslager'
);

ini_set("display_errors", 1);

// required variables from include:
if (!isset($p))
{
  die('ERROR: Not all variables set (project). page:entryform');
}

// process POST-data:
$info[] = print_r($_POST,true);
if (isset($_POST) && count($_POST)>0)
{
  // (1) check if minimum is there
  if (isset($_POST['date-from'], $_POST['boat']))
  {
    // (2) prepare entries for database
    $pdfrom = EWBasic::reFormatDate($_POST['date-from']);
    $pdto = EWBasic::reFormatDate($_POST['date-to']);
    $pboat = $db->real_escape_string($_POST['boat']);
    $pcox = $_POST['cox'];
    $pcrew = $_POST['crew']; // array
    $ptfrom = EWBasic::reFormatTimeOfDay($_POST['time-from']);
    $ptto = EWBasic::reFormatTimeOfDay($_POST['time-to']);
    $ptrack = $db->real_escape_string($_POST['track']);
    $pdistance = EWBasic::getDistance($_POST['distance']);
    $pnotes = $db->real_escape_string($_POST['notes']);
    $psession = $db->real_escape_string($_POST['session']); // "fahrtart" .. training, ...
    $panother = $_POST['anotherentry'];

    // (2b) prepare some entries
    $crew = array();
    $crewidx = 1;
    foreach ($pcrew as $onecrew)
    {
      $crew[] = 'Crew' . $crewidx . '="' . trim($onecrew) . '"'; // TODO: realescape, trim
      $crewidx++;
    }

    // (3) put entries into database
    $query = 'INSERT INTO ew_logbook SET '
            . 'lbts=NOW(), lbp=' . $p . ', '
            . 'Date="' . $pdfrom . '", EndDate="' . $pdto . '", '
            . 'Boat="' . $pboat . '", '
            . 'Cox="' . $pcox . '", ' . implode(', ', $crew) . ', '
            . 'StartTime="' . $ptfrom . '", EndTime="' . $ptto . '", '
            . 'Destination="' . $ptrack . '", Distance="' . $pdistance . '", '
            . 'Comments="' . $pnotes . '", '
            . 'Source="NACHTRAG"';
    
    $info[] = $query;
    
    //$db->query($query);
    unset($_POST);

    $info[] = 'Added entry ' . $pdfrom . ' ' . $pboat . ' to database.';
    if ($panother == 'checked')
    {
      // after this, show infomessage and entry-form again.
    }
    else
    {
      $info[] = '<a href="?page=landing">Back to Landing page.</a>';
      $showform = false;
    }
  }
  else
  {
    // not enough information. print warning and fill form again
    $err[] = 'Not all necessary fields submitted.';
  }
}
else
{
  // not in POST, normal page access
  $info[] = 'Please enter your data.';
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
    <script language="JavaScript">
      $.mobile.document
              // "boat-menu" is the ID generated for the listview when it is created
              // by the custom selectmenu plugin. Upon creation of the listview widget we
              // want to prepend an input field to the list to be used for a filter.
              .on("listviewcreate", "#boat-menu", function (e) {
                var input,
                        listbox = $("#boat-listbox"),
                        form = listbox.jqmData("filter-form"),
                        listview = $(e.target);
                // We store the generated form in a variable attached to the popup so we
                // avoid creating a second form/input field when the listview is
                // destroyed/rebuilt during a refresh.
                if (!form) {
                  input = $("<input data-type='search'></input>");
                  form = $("<form></form>").append(input);
                  input.textinput();
                  $("#boat-listbox")
                          .prepend(form)
                          .jqmData("filter-form", form);
                }
                // Instantiate a filterable widget on the newly created listview and
                // indicate that the generated input is to be used for the filtering.
                listview.filterable({input: input});
              })
              // The custom select list may show up as either a popup or a dialog,
              // depending how much vertical room there is on the screen. If it shows up
              // as a dialog, then the form containing the filter input field must be
              // transferred to the dialog so that the user can continue to use it for
              // filtering list items.
              //
              // After the dialog is closed, the form containing the filter input is
              // transferred back into the popup.
              .on("pagebeforeshow pagehide", "#boat-dialog", function (e) {
                var form = $("#boat-listbox").jqmData("filter-form"),
                        placeInDialog = (e.type === "pagebeforeshow"),
                        destination = placeInDialog ? $(e.target).find(".ui-content") : $("#boat-listbox");

                form.find("input")
                        // Turn off the "inset" option when the filter input is inside a dialog
                        // and turn it back on when it is placed back inside the popup, because
                        // it looks better that way.
                        .textinput("option", "inset", !placeInDialog)
                        .end()
                        .prependTo(destination);
              });
    </script>
  </head>
  <body>
    <div data-role="page">
      <div data-role="header">
        <h1>Neuer Nachtrag</h1>
      </div><!-- /header -->
      <div data-role="content">
        <p><?= EWBasic::getErrHtml($err); ?><?= EWBasic::getInfoHtml($info); ?>
        Club: ISTER</p>

        <?php if ($showform == true)
        { ?>
        <form method="POST" id="entryform" action="?page=newentry">
            <div class="ui-field-contain">
              <label for="date-from">Datum:</label>
              <input type="date" name="date-from" id="date-from" value="<?= isset($_POST['date-from']) ? $_POST['date-from'] : date('Y-m-d'); ?>">
            </div>

            <div class="ui-field-contain">
              <label for="date-to">Datum (bis):</label>
              <input type="date" name="date-to" id="date-to" value="<?= isset($_POST['date-to']) ? $_POST['date-to'] : date('Y-m-d'); ?>">
            </div>

            <div class="ui-field-contain">
              <label for="boat">Boot:</label>
              <!--<input type="text" name="boat" id="boat" value="">-->

              <select name="boat" id="boat" data-native-menu="true">
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

            <!--<div class="ui-field-contain">
            <label for="co1">AC Steuermann:</label>
            <input type="text" name="co1" id="accox">
            </div>-->

            <?php
            for ($i = 1; $i <= 8; $i++)
            {
              $padded_num = str_pad($i, 2, "0", STR_PAD_LEFT);
              $curcrewname = '';
              if (isset($_POST['crew']) && isset($_POST['crew'][$i]))
              {
                $curcrewname = trim($_POST['crew'][$i]);
              }
              ?>
              <div class="ui-field-contain">
                <label for="crew[<?= $i ?>]">Mannschaft <?= $i ?>:</label>
                <input type="text" name="crew[<?= $i ?>]" id="crew<?= $padded_num ?>" value="<?= $curcrewname ?>">
              </div>
              <?php
            }
            ?>

            <label for="time-from">Abfahrt:</label>
            <input type="text" name="time-from" id="time-from" value="<?php if(isset($_POST['time-from'])){ echo $_POST['time-from']; } ?>">

            <label for="time-to">Ankunft:</label>
            <input type="text" name="time-to" id="time-to" value="<?php if(isset($_POST['time-to'])){ echo $_POST['time-to']; } ?>">

            <label for="track">Ziel/Strecke:</label>
            <input type="text" name="track" id="track" value="<?php if(isset($_POST['track'])){ echo $_POST['track']; } ?>">
            
            <label for="track">Distanz:</label>
            <input type="text" name="distance" id="distance" value="<?php if(isset($_POST['distance'])){ echo $_POST['distance']; } ?>">

            <label for="notes">Bemerkungen:</label>
            <input type="text" name="notes" id="notes" value="<?php if(isset($_POST['notes'])){ echo $_POST['notes']; } ?>">

            <!-- Final source: types.efa2types -->
            <label for="session" class="select">Fahrtart:</label>
            <select name="session" id="session">
        <?php
          foreach ($session_dict as $key=>$value) {
            if(isset($_POST['session']) && $_POST['session']==$key)
              $selected = ' selected="selected"';
            else
              $selected = '';
        ?>
              <option value="<?= $key ?>"<?= $selected ?>><?= $value ?></option>
        <?php } ?>
            </select>

            <fieldset data-role="controlgroup">
                  <label for="anotherentry">Nach dem Senden weiteren Eintrag erstellen.</label>
                  <input type="checkbox" name="anotherentry" id="anotherentry"<?php if(isset($_POST['anotherentry']) && $_POST['anotherentry']=='checked') { echo('checked="checked"');} ?>>
            </fieldset>

            <label for="comment">Kommentar:</label>
            <textarea name="comment"><?php if(isset($_POST['comment'])){ echo $_POST['comment']; } ?></textarea>

            <label for="submit">Send:</label>
            <button class="ui-shadow ui-btn ui-corner-all" type="submit" id="submit">Senden</button>
          </form>
<?php } // end showform  ?>
      </div><!-- /content -->

      <div data-role="footer">
        <h4>My Footer</h4>
      </div><!-- /footer -->

    </div><!-- /page -->
  </body>
</html>