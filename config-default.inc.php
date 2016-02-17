<?php
// Configuration for efawebexporter


$ewconf['prodname'] = 'efawebexporter';
$ewconf['version'] = '0.1 dev'; // X.Y [dev,beta,stable]
$ewconf['dbquerylog'] = false;

$ewconf['sessname'] = 'efawebexp';

$ewconf['dbhost'] = 'localhost';
$ewconf['dbuser'] = 'USERNAME';
$ewconf['dbpass'] = 'PASSWORT';
$ewconf['dbschema'] = 'DBNAME';
$ewconf['dbpre'] = 'ew_';

$ewconf['mailfrom'] = 'EMAILFROM';
$ewconf['mailfromname'] = 'eFa Web Exporter';
$ewconf['mailhost'] = 'SMTPHOST';
$ewconf['mailuser'] = 'SMTPUSERNAME';
$ewconf['mailpass'] = 'SMTPPASSWORD';

$ewconf['installdir'] = 'efawebexporter/';
$ewconf['baseurl'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$ewconf['installdir'];
$ewconf['basedir'] = $_SERVER['DOCUMENT_ROOT'].'/'.$ewconf['installdir'];
//$ewconf['basedir'] = 'http://URL.at/efawebexporter/'; // static configuration

//$ewconf['templatedir'] = $ewconf['basedir'].'templates/';

$ewconf['defaultlang'] = 'de';

$ewconf['indent'] = '  ';
$ewconf['linebreak'] = "\r\n";

$ewconf['basefilename'] = 'export'; // +datetime.csv

?>