<?php
/**
 * Aleph ILS driver - Abstract class
 *
 * PHP version 5
 *
 * Copyright (C) UB/FU Berlin
 *
 * Last update: 2011-01-19
 * Tested with X-Server and REST server Aleph 20.0.
 *
 * This driver is a work in progress.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind
 * @package  ILS_Drivers
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */
require_once 'Interface.php';
require_once 'sys/VuFindDate.php';

/**
 * Aleph ILS driver
 * This is an abstract driver which can be instantiated either
 * by the RESTful or the XServer implementation
 * Currently consists of four categories of function:
 * 1. Login related
 * 2. Stubs for functions defined on wiki Driver but not yet implemented
 * 3. Functions wrapping those implemented by actual drivers (eg getStatuses(),
 * which calls getStatus() in the actual driver
 * 4. General utilities
 * @category VuFind
 * @package  ILS_Drivers
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */
abstract class Aleph
{
    protected $host; // host for Aleph and PDS (if used)
    protected $bib;
    protected $useradm;
    protected $admlib;
    protected $loanlib;
    protected $sublibadm;
    protected $wwwuser;
    protected $auth; // authentication method - PDS or Aleph native (default)
    protected $dateFormat; // Vufind date converter object
    protected $xml;
    
    /**
     * Constructor
     *
     * @access public
     */
    function __construct($configFile = false)
    {
      if ($configFile) {
            // Load Configuration passed in
            $this->config = parse_ini_file('conf/'.$configFile, true);
        } else {
            // Hard Coded Configuration
            $this->config = parse_ini_file('conf/Aleph.ini', true);
        }

        $this->host = $this->config['Catalog']['host'];
        $this->bib = $this->config['Catalog']['bib'];
        $this->useradm = $this->config['Catalog']['useradm'];
        $this->admlib = $this->config['Catalog']['admlib'];
        $this->loanlib = $this->config['Catalog']['loanlib'];
        $this->wwwuser = $this->config['Catalog']['wwwuser'];
        $this->wwwpasswd = $this->config['Catalog']['wwwpasswd'];
        $this->sublibadm = $this->config['sublibadm'];
        $this->auth = $this->config['Catalog']['authentication'];
        $this->dateFormat = new VuFindDate();
    }

/*******************************************************************
* FUNCTIONS DEFINED IN Interface OR CatalogConnection
* SOME ONLY FROM VUFIND 1.2
********************************************************************/

    /**
     * Find Reserves
     *
     * Obtain information on course reserves.
     *
     * @param string $course ID from getCourses (empty string to match all)
     * @param string $inst   ID from getInstructors (empty string to match all)
     * @param string $dept   ID from getDepartments (empty string to match all)
     *
     * @return mixed An array of associative arrays representing reserve items (or a PEAR_Error object if there is a problem)
     * @access public
     * Required by CatalogConnection, not used by Aleph
     */
    public function findReserves($course, $inst, $dept)
    {
        $recordList = array();
        return $recordList;
    }

    /**
    * getCancelHoldDetails
    *
    * This method returns a string to use as the input form value for cancelling each hold item. (optional, but required if you implement cancelHolds). Not supported prior to VuFind 1.2
    *
    * @param array holdDetails One of the individual item arrays returned by the getMyHolds method
    *
    * return string the input form value for cancelling each hold item. 
    * @access public
    */
    public function getCancelHoldDetails($holdDetails)
    {
        return $holdDetails['reqnum'];
    }

    /**
    * getConfig
    * This method returns driver configuration settings related to a particular function. It is primarily used to get the configuration settings for placing holds. (optional, but necessary if you want to implement hold functionality) Not supported prior to VuFind 1.2
    * @param string function A string corresponding with the function to check ("cancelHolds", "Holds", "Renewals")
    *
    * @return array An associative array of configuration settings or false on failure
    *   Array keys used for input of "cancelHolds"
    *        No required keys at this time
    *   Array keys used for input of "Holds"
    *       extraHoldFields (optional) - a colon-separated list of form fields to include in the place hold form; may include "comments", "requiredByDate" and "pickUpLocation". Note that all parameters activated here must be processed by the placeHold method.
    *       defaultRequiredDate - A colon-separated list used to set the default "not required after" date for holds in the format days:months:years (e.g. 0:1:0 will set a "not required after" date of 1 month from the current date). This only applies if extraHoldFields includes "requiredByDate."
    *   Array keys used for input of "Renewals"
    *       No required keys at this time
    * @access public
    */ 
    public function getConfig($function)
    {  
        if (isset($this->config[$function]) ) {
            $functionConfig = $this->config[$function];
            if ($function == "Holds") {
                $functionConfig["HMACKeys"] = "id:number";
                $functionConfig["extraHoldFields"] = "comments:requiredByDate:pickUpLocation";
            }
        } else {
            $functionConfig = false;
        }
        return $functionConfig; 
    }
    
    /**
    * getCourses
    *
    * This method queries the ILS for a list of courses to be used as input to the findReserves method (Optional)
    *
    * @return array An associative array with key = course ID, value = course nam
    * @access public
    * FIXME not yet implemented for Aleph
    */ 
    public function getCourses()
    {   
        return array();
    }

    /**
    *   getDefaultPickUpLocation
    *
    * This method returns the default pick up location code or id for use when placing holds. (optional) Not supported prior to VuFind 1.2
    *
    * @param array patrons Patron array returned by patronLogin method (optional)
    *
    * @return string A pick up location id or code (string)
    * @access public
    * FIXME not yet implemented for Aleph
    */
    public function getDefaultPickupLocation($patrons)
    {   
        return '';
    }


    /**
    * getDepartments
    *
    * This method queries the ILS for a list of departments to be used as input to the findReserves method (Optional)
    *
    * @return array An associative array with key = department ID, value = department name.
    * @access public
    * FIXME not yet implemented for Aleph
    */
    public function getDepartments()
    {   
        return array();
    }

    /**
    * getFunds
    *
    * Get a list of funds that can be used to limit the "new item" search. Note that "fund" may be a misnomer - if funds are not an appropriate way to limit your new item results, you can return a different set of values from this function. For example, you might just make this a wrapper for getDepartments(). The important thing is that whatever you return from this function, the IDs can be used as a limiter to the getNewItems() function, and the names are appropriate for display on the new item search screen. If you do not want or support such limits, just return an empty array here and the limit control on the new item search screen will disappear.
    *
    * @return array An associative array with key = fund ID, value = fund name.
    * @access public
    * FIXME not yet implemented for Aleph
    */
    public function getFunds()
    {   
        return array();
    }


    /**
     * Get Holding
     *
     * This is responsible for retrieving the holding information of a record
     *
     * @param string $id The record id to retrieve the holdings for
     * @param mixed patron id of patron, default false if none 
     * @return array an array of associative arrays, one for each item attached to the specified bibliographic record. Each associative array contains these keys:
     *  id - the RecordID that was passed in
     *  availability - boolean: is this item available for checkout?
     *  status - string describing the availability status of the item
     *  location - string describing the physical location of the item
     *  reserve - string indicating "on reserve" status - legal values: 'Y' or 'N'
     *  callnumber - the call number of this item
     *  duedate - string showing due date of checked out item (null if not checked out)
     *  returnDate - A string showing return date of an item (false if not recently returned)
     *  number - the copy number for this item (note: although called "number", this may actually be a string if individual items are named rather than numbered - the value will be used in displays to distinguish between copies within a list)
     *  requests_placed - The total number of holds and recalls placed on an item (optional)
     *  barcode - the barcode number for this item (important: even if you do not have access to real barcode numbers, you may want to include dummy values, since a missing barcode will prevent some other item information from displaying in the VuFind interface).
     *  note - an array of notes associated with holdings (optional)
     *  summary - an array of summary information strings associated with holdings (optional)
     *  is_holdable - boolean - whether or not ANY user can place a hold or recall on the item - allows system administrators to determine hold behaviour (optional)
     *  holdtype - the type of hold to be placed - of use for systems with multiple hold types such as "hold" or "recall". A value of "block" will inform the user that they cannot place a hold on the item due to account blocks (optional)
     *  addlink - whether not the CURRENT user can place a hold or recall on the item - for use with drivers which can determine hold logic based on patron data (optional)
     * @access public
     */
    public function getHolding($id, $patron='false')
    {
//        $id = $this->_parseIdMPG($id);
        if ($id)  
            return $this->getStatus($id);
        return array();
    }

    /**
     * Get Purchase History
     *
     * This is responsible for retrieving the acquisitions history data for the specific record (usually recently received issues of a serial).
     *
     * @param string $id The record id to retrieve the info for
     *
     * @return mixed     An array with the acquisitions data on success, PEAR_Error on failure
     * @access public
     * Required by Interface but not used by Aleph
     */
    public function getPurchaseHistory($id)
    {
        return array();
    }

    /**
     * Get Statuses
     *
     * This is responsible for retrieving the status information for a
     * collection of records.
     *
     * @param array $idList The array of record ids to retrieve the status for
     *
     * @return mixed        An array of getStatus() return values on success, a PEAR_Error object otherwise.
     * @access public
     */
    public function getStatuses($idList)
    {
        $holdings = array();
        foreach ($idList as $id) {
            $holding = $this->getStatus($id);
            if ($holding)
                $holdings[] = $holding;
        }
        return $holdings;
    }

    /*
    * getSuppressedRecords
    *
    * Return a list of suppressed records (used to remove non-visible items from VuFind's index).
    * @return array An array of bibliographic record IDs.
    * @access public
    * FIXME not implemented for Aleph 
    */
    public function getSuppressedRecords()
    {
        return array();
    }

/*******************************************************************
* AUTHENTICATION/LOGIN 
********************************************************************/

    /**
     * Patron Login
     *
     * This is responsible for authenticating a patron against the catalog or PDS, depending on configuration
     *
     * @param string $user The patron username/barcode
     * @param string $pw   The patron password/pin
     *
     * @return mixed          Associative array of patron info on successful login, null on unsuccessful login, PEAR_Error on error.
     * @access public
     */
    public function patronLogin($user, $pw)
    {
        if ($this->auth == 'pds'){
            return $this->_pdsLogin($user, $pw);
        } else {
            return $this->_xserverLogin($user, $pw);
       } 
    }

    /**
     * PDS Login
     *
     * This is responsible for authenticating a patron against PDS.
     *
     * @param string $user The patron username/barcode
     * @param string $pw   The patron password/pin
     *
     * @return mixed  Associative array of patron info on successful login, null on unsuccessful login, PEAR_Error on error.
     * @access protected
     */
    protected function _pdsLogin($user, $pw)
    {
        $request = "https://$this->host/pds?func=authenticate&institute=$this->useradm&bor_id=$user&bor_verification=$pw&calling-system=aleph";
//echo($request); exit;
        $xml = simplexml_load_file($request);
        if (isset($xml->error))
            return new PEAR_Error('Unable to log in');
        else{
            // X-server needs no login to query from this host
            $xml = $this->_doXRequest('bor-info', array('library' => $this->useradm, 'bor_id' => $user), true);
            if (PEAR::isError($xml))
                return $xml;
            return $this->_patronInfo($xml, $user, $pw);
        } 
    }

    /**
     * X-Server Login
     *
     * This is responsible for authenticating a patron against the catalogue using the X-Server
     *
     * @param string $user The patron barcode
     * @param string $pw   The patron pin
     *
     * @return mixed  Associative array of patron info on successful login, null on unsuccessful login, PEAR_Error on error.
     * @access protected
     */
    protected function _xserverLogin($user, $pw)
    {
        $xmlfile = "";
        $xml = $this->_doXRequest('bor-auth', array('library' => $this->useradm, 'bor_id' => $user, 'verification' => $pw), true);
        if ($xml->error != '' || PEAR::isError($xml)) {
            if ((string)$xml->error != "Error in Verification") {
                return new PEAR_Error($xml->error);
            }
        } else {
            return $this->_patronInfo($xml, $user, $pw);
        }
    }

    /**
     * Retrieve patron information 
     *
     * Parse the XML returned on login to extract patron information
     *
     * @param object $xml Z303 data in XML format
     * @param string $user  The patron username/barcode
     * @param string $pw   The patron password/pin
     *
     * @return mixed  Associative array of patron info on successful login, null on unsuccessful login, PEAR_Error on error.
     * @access protected
     * FIMXE need some clarity on use of username/pw and which type they should be
     */
    protected function _patronInfo($xml, $user, $pw)
    {
        $patron=array();
        $firstName = "";
        $lastName = "";
        // Assumes names stored in the format 'Surname, First names'  If this
        // isn't the case alter the regular expression and the two assignments.
        if (preg_match("/^(\w+)\s*,\s*(\w+)/", $xml->z303->{'z303_name'}, $matches))
        {
            $firstName = $matches[2];
            $lastName = $matches[1];
        }
        // This value was originally used in place of $barcode in generating the
        // $patron array below, but that approach failed with some Aleph
        // configurations; using $barcode instead of $username seems more
        // reliable:
        // reverted GS 20110209 - barcode not in Z303 and may signin with ldap
        // Should make choice of field configurable
        $username = (string)$xml->z303->{'z303_id'};
        $email_addr = $xml->z304->{'z304_email_address'};
        $home_lib = $xml->z303->{'z303_home_library'};
        // Default the college to the useradm library and overwrite it if the
        // home_lib exists
        $patron['college'] = $this->useradm;
        if (($home_lib != '') && (array_key_exists("$home_lib", $this->sublibadm))) {
            if ($this->sublibadm["$home_lib"] != '') {
                $patron['college'] = $this->sublibadm["$home_lib"];
            }
        }
        // reverted GS 20110209 - barcode not in Z303 and may signin with ldap
        //$patron['id'] = $barcode;
        $patron['id'] = $username;
        $patron['firstname'] = $firstName;
        $patron['lastname'] = $lastName;
        $patron['cat_username'] = $user;
        $patron['cat_password'] = $pw;
        $patron['email'] = "$email_addr";
        $patron['major'] = NULL;
        return $patron;
    }

/*******************************************************************
* RECORD FUNCTIONS
********************************************************************/

    /**
     * Get New Items
     *
     * Retrieve the IDs of items recently added to the catalog.
     *
     * @param int $page    Page number of results to retrieve (counting starts at 1)
     * @param int $limit   The size of each page of results to retrieve
     * @param int $daysOld The maximum age of records to retrieve in days (max. 30)
     * @param int $fundId  optional fund ID to use for limiting results (use a value
     * returned by getFunds, or exclude for no limit); note that "fund" may be a
     * misnomer - if funds are not an appropriate way to limit your new item
     * results, you can return a different set of values from getFunds. The
     * important thing is that this parameter supports an ID returned by getFunds,
     * whatever that may mean.
     *
     * @return array       Associative array with 'count' and 'results' keys
     * @access public
     * FIXME currently unused, returns nothing
     */
    public function getNewItems($page, $limit, $daysOld, $fundId = null)
    {
        $items = array();
        return $items;
    }

/*******************************************************************
* PATRON FUNCTIONS
********************************************************************/

    /**
     * Check Account Blocks
     *
     * Checks if a user has any blocks against their account which may prevent them
     * performing certain operations
     *
     * @param string $patronId A Patron ID
     *
     * @return mixed           A boolean false if no blocks are in place and an array
     * of block reasons if blocks are in place
     * @access private
     * FIXME doesn't really check for blocks!
     */

    protected function _checkAccountBlocks($patronId)
    {
        $blockReason = false;
        // could return an array of blockReason values...
        //$blockReason = array("Please go to the library counter to discuss your account");
        return $blockReason;
    }

    /**
     * getRenewDetails
     * 
     * Returns a string to use as the input form value for renewing each hold item
     * @param array $checkOutDetails - a single item array returned by getMyTransactions
     * @return string Input form value for renewing each item, used as part of input to renewMyItems
     */
    public function getRenewDetails($checkOutDetails)
    {
        return ($checkOutDetails['loanid']); 
    }
    /**
    * renewMyItems
    *
    * This method renews a list of items for a specific patron. (optional - you may wish to implement getRenewLink instead if your ILS does not support direct renewals) Not supported prior to VuFind 1.2
    *
    * @param array $renewDetails  An associative array with two keys:
    *   patron - array returned by patronLogin method
    *   details - array of values returned by the getRenewDetails method identifying which items to renew 
    *
    * @return array An associative array with two keys:
    *   blocks - An array of strings specifying why a user is blocked from renewing (false if no blocks)
    *   details - Not set when blocks exist; otherwise, an array of associative arrays (keyed by item ID) with each subarray containing these keys:
    *   success - Boolean true or false
   *   new_date - string - A new due date
    *   new_time - string - A new due time
    *   item_id - The item id of the renewed item
    *   sys_message - A system supplied renewal message (optional)
    * @access public
    */
    public function renewMyItems($renewDetails)
    {

        // Get Account Blocks
        $finalResult['blocks'] = $this->_checkAccountBlocks($renewDetails['patron']['id']);
        if ($finalResult['blocks'] === false)
            return $this->renewMyLoans($renewDetails['patron'], $renewDetails['details'], false);
        else{
            $finalResult['details'] = array();
            return $finalResult;
        }
    }
/*******************************************************************
* UTILITY FUNCTIONS
********************************************************************/

    /**
     * Make a request to the Aleph X-server
     *
     * @param string $op The operation to execute 
     * @param array $params key value pairs of cgi parameters
     * @param boolean $auth true=requires authorization
     * @return mixed XML structure, or PEAR_Error on error
     * @access protected
     */
    protected function _doXRequest($op, $params, $auth)
    {
        $url = "http://$this->host/X?op=$op";
        foreach ($params as $key => $value) {
           $url.="&$key=$value";
        }
        if ($auth) {
           $url.="&user_name=$this->wwwuser&user_password=$this->wwwpasswd";
        }
        $answer = file($url);
        $xmlfile = '';
        foreach ($answer as $line) {
           // replace - with _. Should only be in tags but is also
           // in contents - to fix GS
            if (preg_match("|\<([a-z0-9])+\-|i", $line) || preg_match("|\</([a-z0-9])+\-|i", $line)){
                $line = preg_replace("/-/", "_", $line);
            }
           $xmlfile = $xmlfile . $line;
        }
        $xmlfile = str_replace('xmlns=', 'ns=', $xmlfile);
        $xml = simplexml_load_string($xmlfile);
        if (!$xml) { 
           return new PEAR_Error("XML is not valid, URL is '$url'.");
        }
        if ($xml->error) {
           return new PEAR_Error("XServer error: $xml->error.");
        }
        return $xml;
    }

    /**
    * Convert an ISO date to a human-legible one
    * @param string $date 8-digit string
    * @return string pretty date
    * @access protected
    */
    protected function _parseDate($date) {
       if (preg_match("/^[0-9]{8}$/", $date) === 1) {
           return substr($date, 6, 2) . "." .substr($date, 4, 2) . "." . substr($date, 0, 4);
        } else {
           list($day, $month, $year) = split("/", $date, 3);
           $translate_month = array ( 'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6,
              'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12);
           return $day . "." . $translate_month[strtolower($month)] . "." . $year;
        }
    }

    // insert colon in time
    protected function _parseHour($hour){
        if (preg_match('/^\d\d\d\d$/', $hour))
            return (substr($hour, 0, 2) . ':' . substr($hour, 2, 2));
        else
            return $hour;
    }

    /**
    * _toISODate
    * 
    * convert a date from a form to an ISO one
    */
    protected function _toISODate($date)
    {
        if (! preg_match('/^\d\d\-\d\d\-\d\d\d\d/', $date))
            return $date; // unchanged if other format

        $bits = split(' ', $date);
        $date = $bits[0];
        $bits = split('-', $date);
        return ($bits[2].$bits[1].$bits[0]);
    }

    /**
    * Strip the Summon prefix from an item Id
    * @param string $id Id of bibliographic item (digits only)
    * @return numeric id
    * @access protected
    */
    protected function _parseId($id){
        // just in case from Summon GS
        //$id = preg_replace('/FETCH-rhul_catalog_/', '', $id);
        // nasty hack since Summon decided to change ids randomly
        $id = preg_replace('/[^\d]*/', '', $id);
        return $id;
    }

    protected function _parseIdMPG($id) {
        if (preg_match("/^[A-Za-z]{3}/", $id)) {
            return substr($id, strlen($id)-9, 9);
        } else {
            $request['request'] = "(IDN=" . $id . ")";
            $request['base'] = $this->bib;
            $result = $this->_doXRequest("find", $request);
            $set = $result->set_number;
            $no_records = $result->no_records;
            if (empty($no_records)) { 
                return; 
            } else {
                $request = array();
                $request['base'] = $this->bib;
                $request['set-entry'] = "001-" . $no_records;
                $request['set_number'] = $set;
                $result = $this->_doXRequest("present", $request);
                $sysnum = $result->record->doc_number;
                return $sysnum;
            }
        }
    }

    protected function _checkIfMPGHolding($id) {
        if (substr($id, 0, 3) == "EBX") {
            return false;
        }
        else {
            return true;
        }
    }
  
    /** 
    * _tidyDate
    * create displayable date/time from Aleph form
    * @param string $day yyyymmdd
    * @param string $hour hhmm
    * @return array $day, $hour 
    * @access protected
    */
    protected function _tidyDate($day='19000101', $hour='0000')
    {
        $hour = substr($hour,0,2).':'.substr($hour,2,2);
        $day = substr($day,0,4).'-'.substr($day,4,2).'-'.substr($day,6,2);
        $date = $day . ' ' . $hour;
        $date = $this->dateFormat->convertToDisplayDate('Y-m-d H:i', $date);
        $day = substr($date, 0, strrpos($date, ' '));
        $hour = substr($date, strrpos($date, ' '));
        return array($day, $hour); 
    }
}

?>
