<pre>
<?php

//error_reporting(E_ALL);
ini_set("display_errors", 1);

// Tests efa-Data - EFaData::

require_once(dirname(__FILE__) . '/../inc/efazipdata.inc.php');

$e = new EFaZipData('../import/efaBackup_20150609_225410.zip');
$e->open();
$e->importAllData();
?>
</pre>