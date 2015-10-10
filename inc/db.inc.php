<?php

// Database Connection Functions

require_once(dirname(__FILE__) . '/../config.inc.php'); // $ewconf
include_once(dirname(__FILE__) . '/../inc/basicfunc.inc.php'); // hashes, ...
// Array of Tables used in the project (without prefix)
$ewtables = array('log', 'email');

class EWSql extends mysqli
{
  public $logQueries = FALSE;
  
  public function __construct()
  {
    global $ewconf;
    parent::__construct($ewconf['dbhost'], $ewconf['dbuser'], $ewconf['dbpass'], $ewconf['dbschema']);
    // persistence with 'p:'. since php 5.3

    if (mysqli_connect_error())
    {
      $errno = mysqli_connect_errno();

      if ($errno == 1049)
      {
        // Unknown database
        die('ERROR 1049: DB Installation Error (DB not found)');
      }
      if ($errno == 2005)
      {
        // Unknown database
        die('ERROR 2005: DB Installation Error (wrong DBHost)');
      }
      else
      {
        die('EW Database Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
      }
    }
    else
    {
      // === properly connected, now configure connection:
      parent::set_charset("utf8");
    }
  }
  
  /**
   * Queries the Database.
   * @global type $ewconf
   * @param type $query
   * @param type $resultmode
   * @return MYSQL_RESULT The Result-Object as a MYSQL_RESULT, or FALSE if an error occured
   */
  public function query($query, $resultmode = MYSQLI_STORE_RESULT,$logtype = 'QUERY')
  {    
    global $ewconf;
    // also log every query to logtable:
    $this->log($query.'; # '.$resultmode, $logtype, $ewconf['prodname'].' '.basename(__FILE__).' '.__METHOD__.'()', TRUE);
    return parent::query($query, $resultmode);
  }
  
  public function queryIfExists($query)
  {
    $res = parent::query($query);
    if(!$res)
      return FALSE;
    if($res->num_rows<=0)
      return FALSE;
    
    return TRUE;
  }
  
  /**
   * Shovels the Result of a EWSql->query to an associative array.
   * @param type $query The MySQL-Query-String like.. "SELECT * FROM ..."
   * @param type $resultmode
   * @return array An Result-Array. Returns an empty array if any error occured.
   * (So it's quite hidden, if any error occured)
   */
  public function queryToArray($query, $resultmode = MYSQLI_STORE_RESULT)
  {
    $tempresult = array();
    $result = $this->query($query,$resultmode,'QUERYTOARRAY');
    if(!$result)
    {
      $this->log('ERROR ('.$db->error.') on query: '.$query.'; # '.$resultmode,'ERROR');
      return $tempresult;
    }
    while($row = $result->fetch_assoc())
    {
      $tempresult[] = $row;
    }
    $result->free();
    return $tempresult;
  }
  
  public function queryTo1DArray($query, $resultmode = MYSQLI_STORE_RESULT)
  {
    global $db;
    $tempresult = array();
    $result = $this->query($query,$resultmode,'QUERYTOARRAY');
    
    if(!$result)
    {
      $this->log('ERROR ('.$db->error.') on query: '.$query.'; # '.$resultmode,'ERROR');
      return $tempresult;
    }
    while($row = $result->fetch_row())
    {
      $tempresult[] = $row[0];
    }
    $result->free();
    return $tempresult;
  }

  /**
   * Provides a basic logging mechanism to the database. Note, that the table *log has the storage-
   * engine "ARCHIVE". (Only INSERTs and SELECTs)
   * @param type $logtext The text to log.
   * @param type $logtype Usually one of DEBUG,INFO,WARNING,ERROR
   * @param type $logsource for more finegrained source setting. Should start with project name.
   * @param type $silent If silent is true, this method generates no output on errors during logging
   */
  public function log($logtext = '', $logtype = 'INFO', $logsource = 'EW', $silent = true)
  {
    if($this->logQueries===FALSE)
      return;
    
    global $ewconf;
    if($ewconf['dbquerylog']===true)
    {
      $query = 'INSERT INTO ' . $ewconf['dbpre'] . 'log SET logTimestamp=NOW(), logType="' . $logtype 
              .'", logSource="' . $this->escape_string($logsource) 
              .'", logText="' . $this->escape_string($logtext) . '"';
      $result = parent::query($query); // use original query method, to avoid infinite loop
      if ($result !== TRUE && $silent !== TRUE)
        echo('eFa Web Exporter Logging Error.');
    }
  }

  /**
   * Flushes the Log-Table from a specific datetime, if set.
   * (Necessary, because UPDATE and DELETE are not possible with ARCHIVE storage engine
   * @param type $from a MySQL Datetime-String (e.g. "2014-09-02 08:00:00")
   */
  public function flushlogtable($from = NULL)
  {
    global $ewconf;
    $where = is_null($from) ? '' : ' WHERE logTimestamp>="' . $from . '"';
    $query1 = 'CREATE TABLE ' . $ewconf['dbpre'] . 'log_new LIKE ' . $ewconf['dbpre'] . 'log';
    $query2 = 'INSERT ' . $ewconf['dbpre'] . 'log_new SELECT * FROM ' . $ewconf['dbpre'] . 'log' . $where;
    $query3 = 'DROP TABLE ' . $ewconf['dbpre'] . 'log';
    $query4 = 'RENAME TABLE ' . $ewconf['dbpre'] . 'log_new TO ' . $ewconf['dbpre'] . 'log';

    $this->autocommit(FALSE);
    parent::query($query1);
    parent::query($query2);
    parent::query($query3);
    parent::query($query4);
    $this->commit();
    $this->autocommit(FALSE);
    
    $this->log('Flushed Logtable starting from '. is_null($from)?'ALL':$from, 
            'INFO', $ewconf['prodname'].' '.basename(__FILE__).' '.__METHOD__, TRUE);
    
    return TRUE;
  }

  /**
   * Generates a new unique alphanumeric Hash for the given Table.
   * @global type $ewconf
   * @param type $table
   * @param type $field
   * @param type $length
   * @return mixed new Hash or FALSE on error
   */
  public function generateUniqueHash($table, $field = 'UniqueHash', $length = 10, $alphabet = NULL)
  {
    if (!isset($table))
    {
      return FALSE;
    }
    global $ewconf;
    $table = EWBasic::addPrefix($table);

    do
    {
      $newUniqueHash = EWBasic::makeHash($length, $alphabet);
      $query = 'SELECT ' . $field . ' FROM ' . $table . ' WHERE ' . $field . '="' . $newUniqueHash . '" LIMIT 1';
    } while ($this->query($query)->num_rows > 0);

    return $newUniqueHash;
  }
  
  
  public static function xmlToSql($xmlObj)
  {
    $oFieldarr = (array)$xmlObj;
    $qarr = self::arrToSqlArr($oFieldarr);
    $sqlValStr = implode(', ', $qarr); // << values in mysql-query
    return $sqlValStr;
  }
  
  public static function arrToSql($keyValuePairs)
  {
    return implode(', ', self::arrToSqlArr($keyValuePairs));
  }
  
  public static function arrToSqlArr($keyValuePairs)
  {
    global $db;
    $sqlValArr = array();
    foreach ($keyValuePairs as $key => $value)
    {
      $v = $db->real_escape_string($value);
      $sqlValArr[] = $key.'="'.$v.'"'; // << mysql format of each field
    }
    return $sqlValArr;
  }

}

// Global $db Object.
// Usage within functions: "require_once('db.inc.php'); global $db; $db->query(...);"
$db = new EWSql();

/*
  DEMO-Usage:
  global $db;
  $result = $db->query($querystring);
  // See /test/db_and_logging.php
 */
?>