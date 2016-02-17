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
      <h1>eFa Nachtrag</h1>
    </div><!-- /header -->
 
    <div data-role="content">
      <p><?= EWBasic::getErrHtml($l->errors); ?><?= EWBasic::getInfoHtml($info); ?>
      <img src="img/logbook-icon-ister-192.png">
      <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
        Username: <input name="user_name" type="text"><br>
        Password: <input name="user_password" type="password"><br>
        <input type="submit" name="login" value="Login">
      </form>
      
    </div>
        
  </div>
</body>
</html>