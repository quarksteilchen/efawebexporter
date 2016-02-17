<pre>
<?php

//error_reporting(E_ALL);
ini_set("display_errors", 1);

// Tests Distances 

require_once(dirname(__FILE__) . '/../inc/basicfunc.inc.php');

$disarr = array('100',
    '10000m',
    '1  km',
    '1 bkmb',
    'c1 bkmb',
    '34km',
    '34,5km',
    '10mi',
    '100yd',
    '5nm',
    '500fur');

foreach ($disarr as $value)
{
  echo(str_pad($value, 15).'='.str_pad(EWBasic::getDistance($value), 15, " ", STR_PAD_LEFT).' m');
  echo("\r\n");
}

?>
</pre>