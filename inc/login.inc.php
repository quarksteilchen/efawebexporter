<?php
// EW User Classes - Handles "is logged in?" automatically on include.
require_once(dirname(__FILE__) . '/../config.inc.php'); // $ewconf

// checking for minimum PHP version (is also done on the EWBasic::isCompatible
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("User Auth does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../lib/dDLogin/password_compatibility_library.php");
}

// load the login class
require_once("../lib/dDLogin/Login.php");

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process. in consequence, you can simply ...
$login = new Login($db);

// ... ask if we are (not) logged in here:
if ($login->isUserLoggedIn() !== true)
{
  // Try to find out, if this was just an API-access, and return some error instead of 
  // redirecting to login
  if(stripos($_SERVER['REQUEST_URI'],'/api')!==FALSE)
  {
    echo('{"status":"error","error":"not logged in"}');
    exit();
  }
  
  // Turn to startpage of backend
  echo('Redirecting to <a href="">'.$ewconf['baseurl'].'backend/</a>...');
  header('Location: '.$ewconf['baseurl'].'backend/?login',TRUE,303); // redirect with 303: GET
}
else
{
  // user seems to be logged in. continue.
}

?>