<?php

ini_set("display_errors", 1);

require_once(dirname(__FILE__) . '/../inc/basicfunc.inc.php');
require_once(dirname(__FILE__) . '/../inc/db.inc.php');

require_once(dirname(__FILE__) . '/../inc/auth.inc.php'); 

require_once(dirname(__FILE__) . '/../lib/dDLogin/password_compatibility_library.php'); 

$r=Registration($db);
$r->registerNewUser();

?>
<!DOCTYPE html>
<html>
<head>
    <title>eFa Nachtrag</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
</head>
<body>
  <div data-role="page">
 
    <div data-role="header">
      <h1>New User</h1>
    </div><!-- /header -->
 
    <div data-role="content">
      <p><?= EWBasic::getErrHtml($r->errors); ?><?= EWBasic::getInfoHtml($info); ?>
      <form action="?page=newuser" method="POST">
        Username: <input name="user_name" type="text"><br>
        Password: <input name="user_password_new" type="password"><br>
        Retype Password: <input name="user_password_repeat" type="password"><br>
        E-Mail: <input name="user_email" type="text"><br>
        <input type="submit" name="register" value="Create new User">
      </form>
    </div>
        
  </div>
</body>
</html>