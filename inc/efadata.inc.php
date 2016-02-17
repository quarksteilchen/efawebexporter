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
  
  public function exportLB2Csv($lbArr)
  {
    $nl = "\r\n";
    $sep = '|';
    $header = 'Distance|Date|Destination|Boat|Crew1|Crew2|Crew3|Crew4|Crew5|Crew6|Crew7|Crew8';
    $tmp = '';
    
    for($i=0; $i<count($lbArr); $i++)
    {
      $tmp.=$lbArr['Distance'].$sep;
      $tmp.=$lbArr['Date'].$sep;
      $tmp.=$lbArr['Destination'].$sep;
      $tmp.=$lbArr['Boat'].$sep;
      $tmp.=$lbArr['Crew1'].$sep;
      $tmp.=$lbArr['Crew2'].$sep;
      $tmp.=$lbArr['Crew3'].$sep;
      $tmp.=$lbArr['Crew4'].$sep;
      $tmp.=$lbArr['Crew5'].$sep;
      $tmp.=$lbArr['Crew6'].$sep;
      $tmp.=$lbArr['Crew7'].$sep;
      $tmp.=$lbArr['Crew8'].$nl;
    }
    
    return $header.$nl.$tmp;
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