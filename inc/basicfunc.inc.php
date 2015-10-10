<?php
// Basic Functions for Cleanup, Hashing, etc...
require_once(dirname(__FILE__) . '/../config.inc.php'); // $ewconf

class EWBasic
{
  public static $alphabet = '1234567890abcdefghijklmnopqrstuvwxyz';
  public static $alphabetQR = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // $%*+-./: or just uppercase
  public static $alphabetEasy = '123456789abcdefghjkmpqrstuvwxyz'; // no l1i or 0o-confusion (mn...)
  
  /**
   * 
   * @return string|boolean Returns TRUE if Compatible, a string with Warnings if not.
   */
  public static function isCompatible()
  {
    $warnings = '';
    
    if (version_compare(PHP_VERSION, '5.3.7', '<'))
            $warnings.='Auth needs PHP Version >= 5.3.7'."\r\n";
    
    if(strlen($warnings)>0)
      return $warnings;
    return TRUE;
  }
  
  /**
   * Generates a random Hash with a defined Alphabet (36characters) (if given).
   * For this alphabet and a default length of 10, 
   * this gives us more than 3.65 quadrillion (dt. "billiarden") possibilities.
   * 6 characters would give 2.17 billion (dt. "milliarden") - enough for uniqueness
   * 5 = 60mio, 4 = 1.6mio - this could be extended with uppercase-letters.
   * So for an extended set of (62) characters:
   * lengths: 4 = 14.7mio, 5 = 916mio, 6 = 56.8billion (dt. "milliarden")
   * @param type $length
   * @param type $alphabet
   * @return type
   */
  public static function makeHash($length=10,$alphabet=NULL)
  {
    if(is_null($alphabet)) { $alphabet = EWBasic::$alphabet; }
    $newHash = '';
    for($i=0; $i<$length; $i++)
    {
      $newHash.=$alphabet[mt_rand(0,strlen($alphabet)-1)];
    }
    return $newHash;
    //$alen = strlen(EWBasic::$alphabet); // length of alphabet
    //$abitlen = intval(log($alen,2))+1; // number of bits needed for alphabet addressing
    //$bitsneeded = $alen * $abitlen;
    //echo('$alen='.$alen.' $abitlen='.$abitlen.' $bitsneeded='.$bitsneeded);
    //return openssl_random_pseudo_bytes($length);
  }
  
  /**
   * Adds a/the prefix to the table name if necessary.
   * Could also be used for other things than Tablenames.
   * @param string $tablename Plain table name
   * @return string Table name with prefix
   */
  public static function addPrefix($tablename,$customPrefix=NULL)
  {
    global $ewconf;
    $prefix = is_null($customPrefix)?$ewconf['dbpre']:$customPrefix;
    
    if(substr($tablename,0,strlen($prefix))!==$prefix)
    { // add table prefix if not already set.
      return $prefix.$tablename;
    }
    return $tablename;
  }
  
  
  /**
   * Escapes Json for PHP Versions < 5.2
   * @param $value a string which needs to be escaped.
   * @return mixed
   */
  public static function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
      $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
      $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
      $result = str_replace($escapers, $replacements, $value);
      return $result;
  }
  
  /**
   * Wrapper for PHP json_encode or manual method if php-version isn't sufficient.
   * TODO: check if boolean values are serialized correctly.
   * @param type $data The mixed value, which will be encoded.
   * @param type $options See http://php.net/manual/de/function.json-encode.php
   * @param type $depth See http://php.net/manual/de/function.json-encode.php
   */
  public static function jsonEncode($data,$options=0,$depth=512)
  {
    if (version_compare(PHP_VERSION, '5.5.0', '>='))
      return json_encode($data,$options,$depth);
    else if (version_compare(PHP_VERSION, '5.2.0', '>='))
      return json_encode($data,$options);
    else if (version_compare(PHP_VERSION, '5.2.0', '<'))
      return "JSON_ENCODE NOT SUPPORTED BY THIS PHP-VERSION!";
    return "JSON_ENCODE NOT SUPPORTED AT ALL";
  }
  

  /**
   * Returns the current Date/Time in MySQL-Format (as we use it everywhere)
   * @return string current date-time in mysql-fomat
   */
  public static function now()
  {
    return date('Y-m-d H:i:s');
  }
  
  /**
   * Checks if the input-date is valid
   * @param string $date
   * @param string $format
   * @return bool
   */
  public static function validateDate($date, $format = 'Y-m-d H:i:s')
  {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
  }
  
  /**
   * Reformats any datetime-string to Y-m-d H:i:s if not otherwise given.
   * @param string $date
   * @param string $targetformat
   * @return string
   */
  public static function reFormatDate($date, $targetformat = 'Y-m-d H:i:s')
  {
    $d = new DateTime($date);
    return $d->format($targetformat);
  }
  
  public static function efaTime2Time($efaTime)
  {
    // strip the last 3 digits and we've got a unixtimestamp
    return time('Y-m-d H:i:s',substr($efaTime,0,-3));
    // TODO: works only until unixtime intmax (2038)
  }
}

?>