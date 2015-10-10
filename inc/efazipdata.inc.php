<?php
/*
 * Methods for Handling eFa-Data
 */

require_once(dirname(__FILE__) . '/../config.inc.php'); // $ewconf
require_once(dirname(__FILE__) . '/../inc/basicfunc.inc.php'); // hashes, ...
require_once(dirname(__FILE__) . '/../inc/db.inc.php');
require_once(dirname(__FILE__) . '/../inc/efadata.inc.php');

class EFaZipData
{
  private $zipArchive;
  public $zipFileName;
  public $projectName;
  
  public function __construct($zipfilename=NULL)
  {
//    global $ewconf;
    $this->zipFileName = $zipfilename;
  }
  
  /**
   * Opens the (given) zip-File, which should be a efa-Exportfile
   * @param string $zipfilename
   */
  public function open($zipfilename=NULL)
  {
    if(!is_null($zipfilename))
    {
      $this->zipFileName = $zipfilename;
    }
    
    $this->zipArchive = $za = new ZipArchive();
    $za->open($this->zipFileName);
    
    print_r($za);
    var_dump($za);
    echo "numFiles: " . $za->numFiles . "\n";
    echo "status: " . $za->status  . "\n";
    echo "statusSys: " . $za->statusSys . "\n";
    echo "filename: " . $za->filename . "\n";
    echo "comment: " . $za->comment . "\n";
    for ($i=0; $i<$za->numFiles;$i++) {
      echo "index: $i\n";
      print_r($za->statIndex($i));
    }
    echo "numFile:" . $za->numFiles . "\n";
    
    /*echo('=================== Boat File ==================='."\n");
    
    $boatfilename = 'data\\Ister_LRV\\boats.efa2boats';
    $ind = $za->locateName($boatfilename, ZipArchive::FL_NOCASE|ZipArchive::FL_NODIR);
    if($ind===FALSE)
      die('FILE NOT FOUND');
    echo('-------- INDEX: '.$ind.' -------');
    $boatfilecontent = $za->getFromIndex($ind);*/
    
    //var_dump($boatfilecontent);
    
    /*echo('=================== Boat File Stat ==================='."\n");

    $boatfilestat = $za->statIndex($ind);
    print_r($boatfilestat);
    */
    $this->projectName = $this->getProjectName();
    
    echo("Project Name: ".$this->projectName);

  }
  
  public function getProjectName()
  {
    // the project name can be read from file "backup.meta"
    $boatfilename = 'backup.meta';
    $ind = $this->zipArchive->locateName($boatfilename, ZipArchive::FL_NOCASE|ZipArchive::FL_NODIR);
    if($ind===FALSE)
    {
      echo("Warning: getProjectName() backup.meta not found.");
      return '';
    }
    $filecontent = $this->zipArchive->getFromIndex($ind);
    $xC = new SimpleXMLElement($filecontent);
    $efa2ProjName = (string)$xC->ProjectName;
    return $efa2ProjName;
  }
  
  public function getFileContent($internalfilename)
  {
    $ind = $this->zipArchive->locateName($internalfilename, ZipArchive::FL_NOCASE|ZipArchive::FL_NODIR);
    if($ind===FALSE)
    {
      echo("Warning: getFileContent( $internalfilename ) not found.");
      return "";
    }

    $filecontent = $this->zipArchive->getFromIndex($ind);
    return $filecontent;
  }
  
  public function importAllData()
  {
    $this->storeXmlInDb($this->getFileContent('data\\Ister_LRV\\boats.efa2boats'));
    $this->storeXmlInDb($this->getFileContent('data\\Ister_LRV\\persons.efa2persons'));
    $this->storeXmlInDb($this->getFileContent('data\\Ister_LRV\\Ister_2014_bis_2100.efa2logbook'));
  }
  
  public function storeXmlInDb($xmlContent)
  {
    global $et2db, $db;
    
    // (1) Decide what to import
    $xC = new SimpleXMLElement($xmlContent);
    $efa2Type = (string)$xC->header->type;
    $tablename = $et2db[$efa2Type];
    
    // (2) loop through xml-records
    foreach ($xC->data->record as $oneObj)
    {
      $values = EWSql::xmlToSql($oneObj);
      $identstr = '';
      
      // (3) Query to find out if this entry is already in database.
      switch ($efa2Type)
      {
      case 'efa2boats':
        $query = 'SELECT * FROM '.$tablename.' WHERE Name="'.$oneObj->Name.'"';
        $identstr = $oneObj->Name;
        break;
      case 'efa2persons':
        $query = 'SELECT * FROM '.$tablename.' WHERE FirstName="'.$oneObj->FirstName.'" AND LastName="'.$oneObj->LastName.'" AND ValidFrom="'.$oneObj->ValidFrom.'"';
        $identstr = $oneObj->FirstName.' '.$oneObj->LastName.' '.$oneObj->ValidFrom;
        break;
      case 'efa2logbook':
        $query = 'SELECT * FROM '.$tablename.' WHERE EntryId="'.$oneObj->EntryId.'"';
        $identstr = $oneObj->Date.' '.$oneObj->EntryId;
        break;
      default:
        echo("Warning: Table Type not supported yet");
        return;
      }
      
      if(!$db->queryIfExists($query))
      {
        // (4b) Store in Database (INSERT)
        $query = 'INSERT INTO '.$tablename.' SET '.$values;
        $ret = $db->query($query);
        echo('['.$efa2Type.'] Added: '.$identstr.' ('.$ret.') ('.$query.')'."\r\n");
      }
      else
      {
        echo('['.$efa2Type.'] Info: '.$identstr.' is already in DB.'."\r\n");
        // TODO (4a) UPDATE in Database
      }
    }
    
    //var_dump($xmlContent);
    //var_dump($xC);
  }
  
}

?>