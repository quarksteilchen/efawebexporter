<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
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
      <h1>eFa Nachtrag</h1>
    </div><!-- /header -->
 
    <div data-role="content">
      <img src="img/logbook-icon-ister-192.png">
      <ul>
        <li><a href="?page=newentry">Neuer Nachtrag</a></li>
        <li><a href="?page=view">Nachträge ansehen</a></li>
        <li><a href="?page=export">Admin: Nachträge Exportieren</a></li>
        <li><a href="?page=upload">Admin: eFa-Daten upload</a></li>
        <li><a href="?page=newuser">Admin: Neuen Benutzer anlegen</a></li>
        <li><a href="?page=logout">Logout</a></li>
      </ul>
      
    </div>
        
  </div>
</body>
</html>