<?php
/**
 * Aleph RESTful ILS driver
 *
 * PHP version 5
 *
 * Tested with REST server Aleph 20.1.
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
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */

require_once 'Drivers/Aleph.php';
require_once 'sys/Proxy_Request.php';
require_once 'Interface.php';

/**
 * Aleph RESTful ILS driver
 *
 * Requires the Aleph JBOSS server to be running
 * @category VuFind
 * @package  ILS_Drivers
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */
class AlephRestful extends Aleph implements DriverInterface
{
    protected $dlfport = 1891;

    /**
     * Constructor
     *
     * @param string $configFile Name of configuration file to load (relative to
     * web/conf folder; defaults to AlephRestful.ini).
     *
     * @access public
     */
    public function __construct($configFile = 'AlephRestful.ini')
    {
        // Call the parent's constructor...
        parent::__construct($configFile);

        // Define Aleph Rest port
        $this->dlfport = $this->config['RestServer']['dlfport'];
        $this->booking_advance = $this->config['Bookings']['advance'];
        $this->pickUpLocations
            = (isset($this->config['pickUpLocations']))
            ? $this->config['pickUpLocations'] : false;
    }


    /**
    * Get booking slots for item relative to patron
    *
    * Returns the booking slots an item given a patron id.
    * @param string $patronId Id for patron
    * @param string $id Id for bibliographic record
    * @param string $itemid Id for item
    * @return array with slot information or Pear Error
    */
    public function getBookingSlotsForItem($patronId, $id, $itemId) {
        $resourceId = $this->bib.$this->_parseId($id);

        $hierarchy = array(
            "patron" => $patronId,
            "record" => $resourceId,
            "items"  => $this->useradm.$itemId,
            "shortLoan" => false
        );

        $params = array(
        );

        $xml = $this->_makeRequest($hierarchy, $params, "GET");
        
        if (PEAR::isError($xml))
            return $xml;

        $sl = $xml->{'short-loan'};
        $allowed = $sl->xpath('@allowed');
        if ($allowed == 'N'){
            $message = (string)$result->{'short-loan'}->note;
            return new PEAR_Error($message ."<br />$url" );
        }
        $xml_slots = $sl->slot;
        $slots = array();
        
        foreach($xml_slots as $xml_slot){
            $startDate = (string)$xml_slot->start->date;
            if ($startDate >= date('Ymd') + $this->booking_advance)
                break;
            $slot = array();
            $id = $xml_slot->xpath('@id');
            $slot['id'] = (string)$id[0];
            $slot['startDate'] = $this->_parseDate((string)$xml_slot->start->date);
            $slot['startHour'] = $this->_parseHour((string)$xml_slot->start->hour);
            $slot['endDate'] = $this->_parseDate((string)$xml_slot->end->date);
            $slot['endHour'] = $this->_parseHour((string)$xml_slot->end->hour);
            $slot['numItems'] = (int)$xml_slot->{'num-of-items'};
            $slot['numOccupied'] = (int)$xml_slot->{'num-of-occupied'};
            $slots[] = $slot;
        }
        return $slots;
    }

    /**
    * Get holding status of item relative to patron
    *
    * Returns the holding status of an item given a patron id.
    * FIXME does not correctly return status for some users
    * @param string $patronId Id for patron
    * @param string $id Id for bibliographic item
    * @return array with status information or Pear Error
    */
    public function getHoldingInfoForItem($patronId, $id) {
        $resource = $this->bib.$this->_parseId($id);

        $hierarchy = array(
            "patron" => $patronId,
            "record" => $resource
        );

        $params = array(
            "view" => "full"
        );

        $xml = $this->_makeRequest($hierarchy, $params, "GET");
        
        if (PEAR::isError($xml))
            return $xml;

        // find if any copies available to hold
        $holds = $xml->xpath('//hold');
        $can_hold = false;
        foreach($holds as $hold){
            if ((string)$hold['allowed']=='Y'){
                $can_hold = true;
                break;
            }
        }
        if (! $can_hold){
            return new PEAR_Error("Item or like copy is on shelf. Cannot place hold request.");
        } 
        // extract the group id for this copy   
        $group = $hold->xpath('//group');
        $group_href = (string)$group[0]['href'];
        preg_match('|/([^/]+)$|', $group_href, $matches);
        $group_id = $matches[1];
        $locations = array();
        $part = $hold->xpath('//pickup-locations');
        foreach ($part[0]->children() as $node) {
            $arr = $node->attributes();
            $code = (string) $arr['code'];
            $loc_name = (string) $node;
            $locations[$code] = $loc_name;
        }
        // FIXME either not working or need example to test
        $str = $hold->xpath('//item/queue/text()');
        $requests = 0;
        if (isset($str[0]))
            list($requests, $other) = split(' ', trim($str[0]));
        $date = $hold->xpath('//last_interest-date/text()');
        $date = $this->_tidyDate($date[0], '0000');
        $date = $date[0]; 
        return array('pickup-locations' => $locations, 'last-interest-date' => $date, 'order' => $requests + 1, 'group' => $group_id);
    }

    /**
     * Request a single MARC record from the RESTful server 
     *
     * @param string $id Id of the bibliographic record
     * @return string XML string, or PEAR_Error on error
     * @access public
     */
    public function getMarcXML($id){
        $hierarchy = array(
            "record" => $this->bib . $id
        );
        $params = array(
            "view" => "full"
        );
        $xml = $this->_makeRequest($hierarchy, $params, "GET");

        if (PEAR::isError($xml))
            return $xml;

        $root = $xml->xpath("//record");
        $xml = $root[0];
        // convert back to xml string
        return '<?xml version = "1.0" encoding = "UTF-8"?>' . $xml->asXML();
    }
    /**
     * Get Patron Fines
     *
     * This is responsible for retrieving all fines by a specific patron.
     *
     * @param array $user The patron array from patronLogin
     *
     * @return mixed     Array of the patron's fines on success, PEAR_Error
     * otherwise.
     * @access public
     */

    public function getMyFines($user)
    {
        $finesList = array();
        $finesListSort = array();

        $hierarchy = array(
            "patron" => $user['id'],
            "circulationActions" => "cash"
        );
        $params = array(
            "view" => "full"
        );
        $xml = $this->_makeRequest($hierarchy, $params, "GET");

        if (PEAR::isError($xml))
            return $xml;

        foreach ($xml->xpath('//cash') as $item) {
            $z31 = $item->z31;
            $z13 = $item->z13;
            $z30 = $item->z30;
            $delete = $item->xpath('@delete');
            $title = (string) $z13->{'z13-title'}; 
            $transactiondate = date('d-m-Y', strtotime((string) $z31->{'z31-date'}));
            $transactiontype = (string) $z31->{'z31-credit-debit'};
            $id = (string) $z13->{'z13-doc-number'};
            $barcode = (string) $z30->{'z30-barcode'};
            if($transactiontype=="Debit")
                $mult=-100;
            elseif($transactiontype=="Credit")
                $mult=100;
            $amount = (float)(preg_replace("/[\(\)]/", "", (string) $z31->{'z31-sum'}))*$mult;
            $cashref = (string) $z31->{'z31-sequence'};
            $cashdate = date('d-m-Y', strtotime((string) $z31->{'z31-date'}));
            $balance = 0;
            $finesListSort["$cashref"]  = array(
                    "title"   => $title,
                    "barcode" => $barcode,
                    "amount" => $amount,
                    "transactiondate" => $transactiondate,
                    "transactiontype" => $transactiontype,
                    "balance"  => $balance,
                    "id"  => $id
            ); 
        }
        ksort($finesListSort);
        foreach ($finesListSort as $key => $value){
            $title = $finesListSort[$key]["title"]; 
            $barcode = $finesListSort[$key]["barcode"]; 
            $amount = $finesListSort[$key]["amount"]; 
            $transactiondate = $finesListSort[$key]["transactiondate"]; 
            $transactiontype = $finesListSort[$key]["transactiontype"]; 
            $balance += $finesListSort[$key]["amount"];
            $id = $finesListSort[$key]["id"];
            $finesList[] = array(
                "title"   => $title,
                "barcode"  => $barcode,
                "amount"   => $amount,
                "transactiondate" => $transactiondate,
                "transactiontype" => $transactiontype,
                "balance"  => $balance,
                "id"  => $id
            ); 
        }
        return $finesList;
    }
    /**
    * cancelBookings
    * This method cancels a list of Bookings for a specific patron. (optional) 
    *
    * @param cancelDetails An associative array with two keys: patron (array returned by the driver's patronLogin method) and details (an array of strings returned by the driver's getCancelBookingDetails method)
    * @return array containing:
    *   count - The number of items successfully cancelled
    *   array - An array of associative arrays keyed by the item id including:
    *       success - Boolean true or false
    *       status - A status message from the language file (required - VuFind-specific message, subject to translation)
    *       sysMessage - A system supplied failure message (optional - useful for passing additional details from the ILS)
    * @access public
    */
    public function cancelBookings($cancelDetails)
    { 
        $patron = $cancelDetails['patron']['id'];
        $details = $cancelDetails['details'];
        $success = true; $sysMessage = ''; // defaults
        $params = array();

        foreach($details as $rec_key){
            $holdId = $this->useradm . $rec_key;
            $hierarchy = array(
                "patron" => $patron,
                "circulationActions" => "requests",
                "bookings" => $this->useradm . $rec_key
            );

            $result = $this->_makeRequest($hierarchy, $params, "DELETE");
            if (PEAR::isError($result))
                PEAR::raiseError($result);
            
            $reply = $result->{'reply-text'};
            if ($reply != 'ok'){
                $sysMessage = $reply;
                $success = false;
            }
            // carry on regardless - some might succeed
        }
        return array(
            'success' => $success,
            'sysMessage' => $sysMessage
        );
   } 
    /**
     * Get Patron Bookings
     *
     * This is responsible for retrieving all Short Loan bookings by a specific patron.
     * RHUL specific; may be merged back in to getMyHolds later //RHUL
     * @param array $user The patron array from patronLogin
     *
     * @return mixed      Array of the patron's bookings on success, PEAR_Error
     * otherwise.
     * @access public
     */
    public function getMyBookings($user)
    {
        $bookingList = array();
        $userId = $user['id'];

        $hierarchy = array(
            "patron" => $user['id'],
            "circulationActions" => "requests",
            "bookings" => false
        );
         $params = array(
            "view" => "full"
         );
        $xml = $this->_makeRequest($hierarchy, $params, "GET");

        if (PEAR::isError($xml))
            return $xml;

        foreach ($xml->xpath('//booking-request') as $item) {
           $z37 = $item->z37;
           $z13 = $item->z13;
           $z30 = $item->z30;
           $delete = $item->xpath('@delete');
           if ((string) $z37->{'z37-request-type'} == "Booking Request" || true) {
                $type = "hold";
                $docno = (string) $z37->{'z37-doc-number'};
                $itemseq = (string) $z37->{'z37-item-sequence'};
                $seq = (string) $z37->{'z37-sequence'};
                $location = (string) $z37->{'z37-pickup-location'};
                $reqnum = (string) $z37->{'z37-doc-number'} .
                    (string) $z37->{'z37-item-sequence'} . (string) $z37->{'z37-sequence'};
                $startdate = (string) $z37->{'z37-booking-start-date'};
                $starthour = (string) $z37->{'z37-booking-start-hour'};
                $slotid = $startdate . $starthour; // seems to be no other way to get it back
                $startdate = $this->_parseDate($startdate);
                $starthour = $this->_parseHour($starthour);
                $enddate = $this->_parseDate((string) $z37->{'z37-booking-end-date'});
                $endhour = $this->_parseHour((string) $z37->{'z37-booking-end-hour'});
                $title = (string) $z13->{'z13-title'};
                $author = (string) $z13->{'z13-author'};
                $isbn = (string) $z13->{'z13-isbn-issn'};
                $barcode = (string) $z30->{'z30-barcode'};
                $docno = (string) $z13->{'z13-doc-number'};
                $delete = ($delete[0] == "Y");
                $bookingList[] = array(
                    'type' => $type,
                    'location' => $location,
                    'reqnum' => $reqnum,
                    'bookingid' => $user['college'] . $docno . $itemseq . $seq,
                    'location' => $location,
                    'title' => $title,
                    'author' => $author,
                    'isbn' => array($isbn),
                    'barcode' => $barcode,
                    'id' => $docno, 
                    'delete' => $delete,
                    'startdate' => $startdate,
                    'starthour' => $starthour,
                    'enddate' => $enddate,
                    'endhour' => $endhour,
                    'slotid' => $slotid
                );
            }
        }
        return $bookingList;
    }

    /**
     * Get Patron Holds
     *
     * This is responsible for retrieving all holds by a specific patron.
     *
     * @param array $user The patron array from patronLogin
     *
     * @return mixed      Array of the patron's holds on success, PEAR_Error
     * otherwise.
     * @access public
     */
    public function getMyHolds($user)
    {
        $holdList = array();
        $userId = $user['id'];

        $hierarchy = array(
            "patron" => $user['id'],
            "circulationActions" => "requests",
            "holds" => false
        );
         $params = array(
            "view" => "full"
         );
        $xml = $this->_makeRequest($hierarchy, $params, "GET");

        if (PEAR::isError($xml))
            return $xml;

        foreach ($xml->xpath('//hold-request') as $item) {
           $z37 = $item->z37;
           $z13 = $item->z13;
           $z30 = $item->z30;
           $delete = $item->xpath('@delete');
           if ((string) $z37->{'z37-request-type'} == "Hold Request" || true) {
                $type = "hold";
                $docno = (string) $z37->{'z37-doc-number'};
                $itemseq = (string) $z37->{'z37-item-sequence'};
                $seq = (string) $z37->{'z37-sequence'};
                $location = (string) $z37->{'z37-pickup-location'};
                $reqnum = (string) $z37->{'z37-doc-number'} .
                    (string) $z37->{'z37-item-sequence'} . (string) $z37->{'z37-sequence'};
                $expire = (string) $z37->{'z37-end-request-date'};
                $create = (string) $z37->{'z37-open-date'};
                $holddate = (string) $z37->{'z37-hold-date'};
                $title = (string) $z13->{'z13-title'};
                $author = (string) $z13->{'z13-author'};
                $isbn = (string) $z13->{'z13-isbn-issn'};
                $bibId = (string) $z13->{'z13-doc-number'};
                $barcode = (string) $z30->{'z30-barcode'};
                if ($holddate == "00000000") {
                    $holddate = null;
                } else {
                    $holddate = $this->_parseDate($holddate);
                }
                $delete = ($delete[0] == "Y");
                $holdList[] = array(
                    'type' => $type,
                    'location' => $location,
                    'reqnum' => $reqnum,
                    'expire' => $expire,
                    'holdid' => $docno . $itemseq . $seq,
                    'location' => $location,
                    'title' => $title,
                    'author' => $author,
                    'isbn' => array($isbn),
                    'barcode' => $barcode,
                    'id' => $bibId, 
                    'expiredate' => $this->_parseDate($expire),
                    'holddate' => $holddate,
                    'delete' => $delete,
                    'createdate' => $this->_parseDate($create)
                );
            }
        }
        return $holdList;
    }

    /**
     * Get Patron Loans
     *
     * This is responsible for retrieving all loans
     * by a specific patron.
     *
     * @param array $user The patron array from patronLogin
     *
     * @return mixed      Array of the patron's transactions on success,
     * PEAR_Error otherwise.
     * @access public
     */
    public function getMyTransactions($user)
    {
        $transList = array();
        
        $hierarchy = array(
            "patron" => $user['id'],
            "circulationActions" => "loans"
         );
 
         $params = array(
            "view" => "full"
         );

        $xml = $this->_makeRequest($hierarchy, $params, "GET");

        if (PEAR::isError($xml))
            return $xml;

        foreach ($xml->xpath('//loan') as $loan) {
           if($loan['renew'][0] == 'Y') {
               $renewable = true;
           }
           else {
               $renewable = false;
           }
           $id = str_pad($loan->z13->{'z13-doc-number'}, 9, '0', STR_PAD_LEFT);
           $loanid = str_pad($loan->z36->{'z36-number'}, 9, '0', STR_PAD_LEFT);
           $duedate = $loan->z36->{'z36-due-date'};
           $duehour = $loan->z36->{'z36-due-hour'};
           $date = $this->_tidyDate($duedate, $duehour);
           $author = $loan->z13->{'z13-author'};
           $title = $loan->z13->{'z13-title'};
           $call_no = $loan->z13->{'z13-call-no'};
           $isbn = $loan->z13->{'z13-isbn-issn'};
           $transList[] = array('duedate' => $date[0],
                                 'dueTime' => $date[1],
                                 'author' => $author,
                                 'title' => $title,
                                 'call_no' => $call_no,
                                 'isbn' => $isbn,
                                 'loanid' => $loanid,
                                 'renewable' => $renewable,
                                 'id' => $id);
        }
        return($transList);
    }

    /**
     * Get Patron Profile
     *
     * This is responsible for retrieving the profile for a specific patron.
     *
     * @param array $user The patron array
     *
     * @return mixed      Array of the patron's profile data on success, PEAR_Error otherwise.
     * @access public
     */
    function getMyProfile($user)
    {
        $recordList=array();

        $hierarchy = array(
            "patron" => $user['id'],
            "patronInformation" => 'address'
         );
 
        $params = array(
        );
        
        $xml = $this->_makeRequest($hierarchy, $params, "GET");

        if (PEAR::isError($xml))
            PEAR::raiseError($xml);

        $address = $xml->xpath('//address-information');
        $address = $address[0];
        $address1 = (string)$address->{'z304-address-1'};
        $address2 = (string)$address->{'z304-address-2'};
        $address3 = (string)$address->{'z304-address-3'};
        $address4 = (string)$address->{'z304-address-4'};
        $address5 = (string)$address->{'z304-address-5'};
        $zip = (string)$address->{'z304-zip'};
        $phone = (string)$address->{'z304-telephone-1'};
        $email = (string)$address->{'z404-email-address'};
        $dateFrom = (string)$address->{'z304-date-from'};
        $dateTo = (string)$address->{'z304-date-to'};

        $recordList['firstname'] = $user['firstname'];
        $recordList['lastname'] = $user['lastname'];
        $recordList['address1'] = $address1;
        $recordList['address2'] = $address2;
        $recordList['address3'] = $address3;
        $recordList['address4'] = $address4;
        $recordList['address5'] = $address5;
        $recordList['zip'] = $zip;
        $recordList['phone'] = $phone;
        $recordList['email'] = $email;
        $recordList['dateFrom'] = $dateFrom;
        $recordList['dateTo'] = $dateTo;
        return $recordList;
    }

    /*
     * Get Status
     * This method returns information from items matching a 
     *
     * @param string id Bibliographic record ID
     *
     * return array Array of arrays with the following keys:
     *  id - The bibliographic record ID (same as input).
     *  status - String describing the status of the item.
     *  location - The location of the item (string).
     *  reserve - Is the item on course reserve? ("Y" for yes, "N" for no).
     *  callnumber - The item's call number.
     *  availability - Boolean: is the item available for checkout?
     * 
     * In addition to the standard keys above this function also returns
     * data with the following keys:
     *  collection (string, used for map generation)
     *  duedate (ISO format date, if on loan)
     *  number (item ID used by hold operation
     *  barcode 
     *  map_href href for map application
     *
     * @access public
     */
    public function getStatus($raw_id)
    {
/*        if(! $this->_checkIfMPGHolding($raw_id)) {
            $holding[] = array('id' => $raw_id,
                               'availability' => 1,
                               'duedate' => (string)$duedate,
                               'status' => (string) $status,
                               'location' => 'Internet',
                               'library' => (string) $library,
                               'collection' => (string) $collection,
                               'holdtype' => $holdtype,
                               'reserve' => $reserve,
                               'callnumber' => '-',
                               'number' => (string) $itemId,
                               'map_href' => $map_href,
                               'barcode' => (string) $barcode);
            return $holding;
        } */
/*        if (preg_match("/^" . $this->bib . "/", $raw_id)) {
            $id = $this->_parseIdMPG($raw_id);
        }
        else {
            $id = $raw_id;
        } */
        $id = $this->_parseIdMPG($raw_id);
        if(! $this->_checkIfMPGHolding($raw_id)) {
             return;
        }
        if (! $id)
            return new PEAR_Error("Unrecognised bib id $raw_id");
        $hierarchy = array(
            "record" => $this->bib . $id,
            "items" => false
         );
 
        $params = array(
           "view" => "full"
        );
        
        $xml = $this->_makeRequest($hierarchy, $params, "GET");
        if (PEAR::isError($xml))
            return $xml;

//        $map_href= 'http://libraryutils.rhul.ac.uk/map/target.php'; // FIXME get from config

        //var_dump($xml);
        $holding = array();
/*        if(! $xml->xpath('//item')) {
          $holding['reserves'] = false;
        } */
        foreach ($xml->xpath('//item') as $item) {
            $duedate = $holdtype = ''; // maybe be empty
            $href = (string)$item->attributes()->href;
            $itemId = substr($href, strripos($href, '/')+1); // chop off item ID
            $itemId = preg_replace('/'.$this->useradm.'/', '', $itemId); 

            $library = $item->z30->{'z30-sub-library'};
            $collection = $item->z30->{'z30-collection'};
            if ($collection != '')
                $location = $collection;
            else 
                $location = $library;
            if ($collection == 'Short Loan'){ 
                $holdtype = 'booking';
                $availability = true;
            }
            $barcode = $item->z30->{'z30-barcode'};
            $description = $item->z30->{'z30-description'};

            $reserve = '-'; // don't have them

            $callnumber = $item->z30->{'z30-call-no'};

            $loan_status = $item->status;

            if(array_search($item->status, $this->config["itemNotAvailable"]["status"]) === false) {
                $availability = true;
            }
            else {
                $availability = false;
            }
            $status = $item->z30->{'z30-item-status'};
            if ($status == 'On shelf') {
                $availability = true;
            } else if (preg_match('/\d\d\/\d\d\/\d\d/', $loan_status)){
                $holdtype = 'hold';
                $availability = false;
                $duedate = $loan_status;
                $status = "Not available";
                // convert to isodate to allow later reformatting
                $duedate = preg_replace('/(\d\d)\/(\d\d)\/(\d\d)/', '20$3$2$1', $duedate); 
            } else if ($status == 'Short Vacation') {
                $availability = true;
            } /* else if ($loan_status == 'Ordered') {
                $availability = false;
            } else { // unknown - don't allow holds or bookings (should not happen)
                $availability = false;
            } */
            $holding[] = array('id' => $raw_id,
                               'availability' => $availability,
                               'duedate' => (string)$duedate,
                               'status' => (string) $status,
                               'location' => (string) $location,
                               'library' => (string) $library,
                               'collection' => (string) $collection,
                               'holdtype' => $holdtype,
                               'reserve' => $reserve,
                               'callnumber' => (string) $callnumber,
                               'number' => (string) $itemId,
                               'map_href' => $map_href,
                               'barcode' => (string) $barcode,
                               'description' => $description);
        }
        return $holding;

    }
    /**
    * renewMyItems
    *
    * This method renews a list of items for a specific patron. Not supported prior to VuFind 1.2
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
         
    /**
     * Renew Patron Loans
     *
     * This is responsible for renewing loans
     * by a specific patron (either all loans or selected list)
     *
     * @param array $user The patron array from patronLogin
     * @param array $items Array of items to renew
     * @param bookean $all Array all or list
     *
     * @return mixed      Array of the patron's transactions on success,
     * PEAR_Error otherwise.
     * @access public
     */
    public function renewMyLoans($user, $items, $all)
    {
        $renewals = array();
        $renewals['blocks'] = false;
        $renewals['details'] = array();
        $renewals['ids'] = array();
        $userId = $user['id'];
        $hierarchy = array(
            "patron" => $user['id'],
            "circulationActions" => "loans"
         );
        if ($all){ // renew all loans for user, ids not provided
            $params = array(
                "institution" => $this->useradm
            );

            $result = $this->_makeRequest($hierarchy, $params, "POST");
        
            if (PEAR::isError($result))
                return $result;

            foreach ($result->xpath('//loan') as $loan) {
                $details = array();
                $recordid = (string)$loan->attributes()->id;
                $recordid = substr($recordid, strlen($this->useradm));
                $renewals['ids'][] = $recordid;
                if ($loan->{'reply-code'} == '0000')
                    $details['success'] = true;
                else
                    $details['success'] = false;
                $new_time = (string)$loan->{'new-due-hour'};
                $new_date = (string)$loan->{'new-due-date'};
                $date = $this->_tidyDate($new_date, $new_time);
                $details['new_date'] = $date[0];
                $details['new_time'] = $date[1];
                $details['item_id'] = $recordid;
                $details['sys_message'] = (string)$loan->{'status'};
                $renewals['details'][$recordid] = $details;
            }
        } else { // renew loans by specified ids
            $params = array();
            foreach($items as $item){
                $hierarchy[$this->useradm.$item] = false;

                $result = $this->_makeRequest($hierarchy, $params, "POST");

                if (PEAR::isError($result))
                    return $result;

                unset($hierarchy[$this->useradm.$item]);

                $loans = $result->xpath('//loan');
                $loan = $loans[0];
                $recordid = (string)$loan->attributes()->id;
                $recordid = substr($recordid, strlen($this->useradm));
                $renewals['ids'][] = $recordid;
                $details = array();
                //if ($loan->{'reply-code'} == '0000')
                if ($loan->{'status'} == 'Renew successful')
                    $details['success'] = true;
                else
                    $details['success'] = false;
                $new_time = (string)$loan->{'new-due-hour'};
                $new_date = (string)$loan->{'new-due-date'};
                $date = $this->_tidyDate($new_date, $new_time);
                $details['new_date'] = $date[0];
                $details['new_time'] = $date[1];
                $details['sysMessage'] = (string)$loan->{'status'};
                $details['item_id'] = $recordid;
                $renewals['details'][$recordid] = $details;
            }
        }
        return($renewals);
    }

    /**
     * makeBooking
     *
    * This method makes a booking for a shortloan item for a specific patron.
    * 
    * @param array  $bookingDetails  
    * @return array Associative array containing:
    *   success - Boolean true or false
    *   sysMessage - A system supplied failure message (optional)
    * @access public
    */
    public function makeBooking($bookingDetails) 
    {
        $patronId = $bookingDetails['patron']['id'];
        $type = 'shortloan';
        $itemId = $bookingDetails['number'];
        $recordId = $bookingDetails['id'];
        $slotId = $bookingDetails['slotid'];
        $recordId = $this->bib . $this->_parseId($recordId); 
        $itemId = $this->useradm . $itemId;

        $hierarchy = array(
            "patron" => $patronId,
            "record" => $recordId,
            "items" => $itemId,
            $type => false // ie empty
        );

        $xmlParameter = "short-loan-parameters";
        $xml[$xmlParameter] = array(
            "request-slot" => $slotId
        );
        $requestXML = $this->_buildBasicXML($xml);

        // curl used here as only way to get the POST to work
        $url = "http://$this->host:$this->dlfport/rest-dlf/patron/$patronId/record/$recordId/items/$itemId/shortLoan";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, "post_xml=$requestXML");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $result = simplexml_load_string($output);
        if (PEAR::isError($result))
            return $result;

        $reply_code = $result->{'reply-code'};
        if ($reply_code != "0000") {
           $message = $result->{'create-short-loan'}->{'note'};
           if ($message == null) {
              $message = $result->{'create-short-loan'}->{'reply-text'};
           }
           return array(
                'success' => false,
                'sysMessage' => $message
            );
        } else {
           return array('success' => true);
        }
          
    }

    /**
    * cancelHolds
    * This method cancels a list of holds for a specific patron. (optional) 
    * Not supported prior to VuFind 1.2
    *
    * @param cancelDetails An associative array with two keys: patron (array returned by the driver's patronLogin method) and details (an array of strings returned by the driver's getCancelHoldDetails method)
    * @return array containing:
    *   count - The number of items successfully cancelled
    *   array - An array of associative arrays keyed by the item id including:
    *       success - Boolean true or false
    *       status - A status message from the language file (required - VuFind-specific message, subject to translation)
    *       sysMessage - A system supplied failure message (optional - useful for passing additional details from the ILS)
    * @access public
    */
    public function cancelHolds($cancelDetails)
    {  
        $patron = $cancelDetails['patron']['id'];
        $details = $cancelDetails['details'];
        $success = true; $sysMessage = ''; // defaults
        $params = array();

        foreach($details as $rec_key){
            $holdId = $this->useradm . $rec_key;
            $hierarchy = array(
                "patron" => $patron,
                "circulationActions" => "requests",
                "holds" => $this->useradm . $rec_key
            );

            $result = $this->_makeRequest($hierarchy, $params, "DELETE");
        
            if (PEAR::isError($result))
                PEAR::raiseError($result);
            
            $reply = $result->{'reply-text'};
            if ($reply != 'ok'){
                $sysMessage = $reply;
                $success = false;
            }
            // carry on regardless - some might succeed
        }
        return array(
            'success' => $success,
            'sysMessage' => $sysMessage
        );
    }
    /**
     * placeHold
     *
    * This method places a hold on a specific record for a specific patron.
    * 
    * @param array  $holdDetails  An associative array with several keys. 'patron' will always be defined to contain the array returned by patronLogin method; other fields may vary depending on the fields defined in the HMACKeys and extraHoldFields settings returned by the getConfig method. Some commonly used values:
    *   holdtype - type of hold (as provided by the getHolding method)
    *   pickUpLocation - user-selected pickup location
    *   item_id - item ID
    *   comment - user comment
    *   id - bibliographic ID
    *
    * @return array Associative array containing:
    *   success - Boolean true or false
    *   sysMessage - A system supplied failure message (optional)
    * @access public
    */
    function placeHold($holdDetails) 
    {
        $patronId = $holdDetails['patron']['id'];
        $type = $holdDetails['holdtype'];
        // Aleph requires pickup location even if not being used
        $pickUpLocation = isset($holdDetails['pickUpLocation'])?$holdDetails['pickUpLocation']:'VOID';
        $comment = isset($holdDetails['comment'])?$holdDetails['comment']:'';
        $itemId = $holdDetails['number'];
        $recordId = $holdDetails['id'];
        $last_interest_date = isset($holdDetails['requiredBy'])?$holdDetails['requiredBy']:'';

        $recordId = $this->bib . $this->_parseIdMPG($recordId); 
        $itemId = $this->useradm . $itemId;

        $hierarchy = array(
            "patron" => $patronId,
            "record" => $recordId,
            "items" => $itemId,
            $type => false // ie empty
        );

        $xmlParameter = ("recall" == $type)
            ? "recall-parameters" : "hold-request-parameters";

        $last_interest_date = $this->_toISODate($last_interest_date);
        
        $xml[$xmlParameter] = array(
            "pickup-location" => $pickUpLocation,
//            "last-interest-date" => $last_interest_date,
//            "start-interest-date" => '',
            "sub-author" => '',
            "sub-title" => '',
            "pages" => '',
            "note-1" => $comment,
            "note-2" => '',
            "rush" => 'N'
         );   

        $requestXML = $this->_buildBasicXML($xml);

        // curl used here as only way to get the POST to work
        $url = "http://$this->host:$this->dlfport/rest-dlf/patron/$patronId/record/$recordId/items/$itemId/hold";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, "post_xml=$requestXML");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $result = simplexml_load_string($output);


        // Fails - XML never sent. Hence replacement by Curl above.
        //$result = $this->_makeRequest($hierarchy, array(), "PUT", $requestXML);

        if (PEAR::isError($result))
            return $result;

        $reply_code = $result->{'reply-code'};
        if ($reply_code != "0000") {
           $message = $result->{'create-hold'}->{'note'};
           if ($message == null) {
              $message = $result->{'reply-text'};
           }
           return array(
                'success' => false,
                'sysMessage' => $message
            );
        } else {
           return array('success' => true);
        }
    }

     /**
     * Get Pick Up Locations
     *
     * This is responsible for gettting a list of valid library locations for
     * holds / recall retrieval
     *
     * @param array $patron Patron information returned by the patronLogin method.
     *
     * @return array        An keyed array where libray id => Library Display Name
     * @access public
     */
    public function getPickUpLocations($patron = false)
    {
        $pickResponse = array();
        if ($this->pickUpLocations) {
            foreach ($this->pickUpLocations as $code => $library) {
                $pickResponse[] = array(
                    'locationID' => $code,
                    'locationDisplay' => $library
                );
            }
        } 
        return $pickResponse;
    }

    public function getDefaultPickUpLocation($patron = false)
    {
        $pickup = '';
        $locations = $this->getPickUpLocations();
        if ($locations[0]['locationID']) {
            $pickup = $locations[0]['locationID'];
        }
        return $pickup;
    }

/********************************************************************
* Local functions                                                   *
********************************************************************/

    /**
     * Build Basic XML
     *
     * Builds a simple xml string to send to the API
     *
     * @param array $xml A keyed array of xml node names and data
     *
     * @return string    An XML string
     * @access private
     */

    private function _buildBasicXML($xml)
    {
        $xmlString = "";

        foreach ($xml as $root => $nodes) {
            $xmlString .= "<" . $root . ">";

            foreach ($nodes as $nodeName => $nodeValue) {
                $xmlString .= "<" . $nodeName . ">";
                $xmlString .= htmlentities($nodeValue, ENT_COMPAT, "UTF-8");
                $xmlString .= "</" . $nodeName . ">";
            }

            $xmlString .= "</" . $root . ">";
        }

        $xmlComplete = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . $xmlString;

        return $xmlComplete;
    }

     /**
     * Make Request
     *
     * Makes a request to the Aleph Restful API
     *
     * @param array  $hierarchy Array of key-value pairs to embed in the URL path of
     * the request (set value to false to inject a non-paired value).
     * @param array  $params    A keyed array of query data
     * @param string $mode      The http request method to use (Default of GET)
     * @param string $xml       An optional XML string to send to the API
     *
     * @return obj  A Simple XML Object loaded with the xml data returned by the API
     * @access private
     */
    private function _makeRequest($hierarchy, $params = false, $mode = "GET",
        $xml = false
    ) {
        // Build Url Base
        $urlParams = "http://{$this->host}:{$this->dlfport}/rest-dlf";

        // Add Hierarchy
        foreach ($hierarchy as $key => $value) {
            $hierarchyString[] = ($value !== false) ? $key. "/" . $value : $key;
        }

        // Add Params
        foreach ($params as $key => $param) {
            $queryString[] = $key. "=" . urlencode($param);
        }

        // Build Hierarchy
        $urlParams .= "/" . implode("/", $hierarchyString);

        // Build Params
        if (isset($queryString))
            $urlParams .= "?" . implode("&", $queryString);
        // Create Proxy Request
        $client = new Proxy_Request($urlParams);
        // Select Method
        if ($mode == "POST") {
            $client->setMethod(HTTP_REQUEST_METHOD_POST);
        } else if ($mode == "PUT") {
            $client->setMethod(HTTP_REQUEST_METHOD_PUT);
            $client->addRawPostData($xml); //addRawPostData is deprecated
            //$client->setBody($xml);
        } else if ($mode == "DELETE") {
            $client->setMethod(HTTP_REQUEST_METHOD_DELETE);
        } else {
            $client->setMethod(HTTP_REQUEST_METHOD_GET);
        }

        // Send Request and Retrieve Response
        $client->sendRequest();
        $xmlResponse = $client->getResponseBody();
        if ($xmlResponse == "") {return new PEAR_Error('no response from REST server');}
        if (preg_match('/The following URL did not return any response/', $xmlResponse)){
           return new PEAR_Error('Error in REST Server: not available');
        }
        if (preg_match('/^<html>/i', $xmlResponse)){
           return new PEAR_Error("REST Server error at $urlParams (mode $mode)<br $xml");
        }
        $xmlResponse = str_replace('xmlns=', 'ns=', $xmlResponse);
        $xml = simplexml_load_string($xmlResponse) or print "error creating xml";
        if (!$xml) {
           return new PEAR_Error("XML is not valid, URL is '$request'.");
        }
//        $replycode = $xml->xpath("//reply_code");
//        if ($replycode[0] != '0000'){
//            $replytext = $xml->xpath("//reply_text");
//            return new PEAR_Error("REST Server error: ".$replytext[0]);
//        }
        return $xml;
    }

    public function makeDBSystemnumber($id) {
        return $this->config['Catalog']['bib'] . "000000000000" . $id;
    }

}
