<?php
/*
 * Methods for Using the Data (in Database)
 */

require_once(dirname(__FILE__) . '/../config.inc.php'); // $ewconf
require_once(dirname(__FILE__) . '/../inc/basicfunc.inc.php'); // hashes, ...
require_once(dirname(__FILE__) . '/../inc/db.inc.php');

// Common EFaData Information:

// Lookup from eFa2-Types to Database Tables
$et2db = array( 'efa2boats' => $ewconf['dbpre'].'boat' ,
                'efa2logbook' => $ewconf['dbpre'].'logbook' ,
                'efa2persons' => $ewconf['dbpre'].'person' ,
                '' => $ewconf['dbpre'].'' );


// Class:
class EFaData
{
  private $dbi;
  private $et2db;
  
  public function __construct($dbcnx=NULL)
  {
    global $db;
    global $et2db;
    
    $this->et2db = $et2db;
    
    if(is_null($dbcnx))
      $this->dbi=$db;
    else
      $this->dbi=$dbcnx;
  }
  
  public function getListOptions($efa2Type,$addFilter=NULL)
  {
    $out = ''; // temp out var
    switch ($efa2Type)
    {
      case 'efa2boats':
        $query = 'SELECT Name,NameAffix,Owner,TypeSeats FROM '.$this->et2db[$efa2Type].' ORDER BY TypeSeats ASC, Name ASC';
        
        $boatArr = $this->dbi->queryToArray($query);
        
        foreach ($boatArr as $boatElem)
        {
          if($boatElem['NameAffix'])
            $affix = ' ('.$boatElem['NameAffix'].')';
          $out .= '  <option value="'.$boatElem['Name'].'">'.$boatElem['TypeSeats'].' | '.$boatElem['Name'].$affix.'</option>'."\r\n";
        }
        break;

      default:
        break;
    }
    return $out;
  }
  
}


?>