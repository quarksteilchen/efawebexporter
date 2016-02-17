<?php
/*
 * Main entry point of efawebexporter
 * 
 */

ini_set("display_errors", 1);
$info = array();
$error = array();

require_once(dirname(__FILE__) . '/inc/db.inc.php'); 
require_once(dirname(__FILE__) . '/inc/auth.inc.php'); 

$p = 1; // project, like internal id of <ProjectName>Ister_LRV</ProjectName>
$page = NULL;
if(isset($_REQUEST['page']))
{
  $page = $_REQUEST['page'];
}
switch ($page)
{  
  case 'login':
    require('pages/login.php');
    break;
  
  case 'logout':
    $l->doLogout();
    require('pages/login.php');
    break;
  
  case 'newentry':
    require('pages/entryform.php');
    break;
  
  case 'view':
    require('pages/viewmask.php');
    break;
  
  case 'export':
    require('pages/exportmask.php');
    break;
  
  case 'upload':
    require('pages/upload.php');
    break;
  
  case 'newuser':
    require('pages/newuser.php');
    break;
  
  case 'landing':
    require('pages/landing.php');
    break;
  
  case 'newuser':
    require('pages/newuser.php');
    break;

  default:
    require('pages/landing.php');
    break;
}


?>