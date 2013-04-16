<?php
/**
 * Proxy function for Aleph Screenscraping (where the REST-API fails...)
 * and RDG-specific stuff which doesn't fit anywhere else
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
 * Copyright (C) Max Planck Society 2012.
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
 * @package  Controller_AJAX
 * @author   Tuan Nguyen <tuan@yorku.ca>
 * @author   Daniel Zimmel <zimmel@coll.mpg.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */

require_once 'Action.php';

class RDG extends Action
{
    // define some status constants
    const STATUS_OK = 'OK';                  // good
    const STATUS_ERROR = 'ERROR';            // bad
    const STATUS_NEED_AUTH = 'NEED_AUTH';    // must login first

    /**
     * Constructor.
     *
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Process parameters and display the response.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        // Call the method specified by the 'method' parameter as long as it is
        // valid and will not result in an infinite loop!
        if ($_GET['method'] != 'launch'
            && $_GET['method'] != '__construct'
            && is_callable(array($this, $_GET['method']))
        ) {
            $this->$_GET['method']();
        } else {
            $this->output(translate('Invalid Method'), RDG::STATUS_ERROR);
        }
    }


    /**
     * Send output data and exit.
     *
     * @param mixed  $data   The response data
     * @param string $status Status of the request
     *
     * @return void
     * @access public
     */
    protected function output($data, $status)
    {
        header('Content-type: application/javascript');
        header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        $output = array('data'=>$data,'status'=>$status);
        echo json_encode($output);
        exit;
    }


    /**
     * RDG!
     *
     * Get ALEPH borrower information via screen scraping
     * Won't work if there are significant changes in the ALEPH OPAC!
     * This is very silly code but it works most of the time (please clean up)
     * REST does not provide the borrower information we need
     * Proxy function to avoid JS issues
     *
     * @return void
     * @access public
     * @author Daniel Zimmel <zimmel@coll.mpg.de>
     */

    public function getALEPHBorrower() {
    {
      require_once 'sys/Proxy_Request.php';
      $doc_number = $_GET['doc_number'];
      // test for valid input (very basic for the moment)
      //      $match = preg_match("/[A-Z][0-9]{1,3}$/",$code);
      //      if ($match) {
      $url = 'http://aleph.mpg.de/F?func=item-global&doc_library=RDG01&doc_number='.$doc_number.'&year=&volume=&sub_library=&local_base=rdg01';
      $client = new Proxy_Request();
      $client->setMethod(HTTP_REQUEST_METHOD_GET);
      $client->setURL($url);
      
      $result = $client->sendRequest();
      if (PEAR::isError($result)) {
        $this->output("ERROR", RDG::STATUS_OK);
      }
      
      $info = $client->getResponseBody();
    }

      if (!PEAR::isError($info)) {

        // parse html
        $DOM = new DOMDocument;
        $DOM->loadHTML($info);
        $admlink = $DOM->getElementById('admDocNo')->firstChild;
        $admdoc = $admlink->getAttribute('href');
        // get aleph id
        $admdoc = preg_match("/doc_number=([0-9]+)&/",$admdoc,$matches);
        $admdoc = $matches[1];
        if ($admdoc) {
        require_once 'sys/Proxy_Request.php';
        /* currently only checks for first item (item_sequence) -- this is very basic */
        $url2 = 'http://aleph.mpg.de/F?func=item-loan&adm_library=RDG50&doc_number='.$admdoc.'&item_sequence=000010&local_base=RDG01';
        $client2 = new Proxy_Request();
        $client2->setMethod(HTTP_REQUEST_METHOD_GET);
        $client2->setURL($url2);
        
        $result2 = $client2->sendRequest();
        if (PEAR::isError($result2)) {
          $this->output("ERROR", RDG::STATUS_OK);
        }
        
        $info2 = $client2->getResponseBody();
        }
        
        if (!PEAR::isError($info2)) {
         $DOM2 = new DOMDocument;
        $DOM2->loadHTML($info2);
        $admlink = $DOM2->getElementById('ajaxAusgeliehen');
        echo $admlink->nodeValue;
      }

      }

    }

    /**
     * RDG!
     *
     * Simple Proxy function for getting raw xml data from MPG vLib (we will parse it with jQuery)
     *
     * @return void
     * @access public
     * @author Daniel Zimmel <zimmel@coll.mpg.de>
     */

    public function getVlibSources() {
    {
      require_once 'sys/Proxy_Request.php';

      $myset = $_GET['myset'];
      switch ($myset) {
      case "ECON":
        $url = 'http://vlib.mpg.de/resource_info?predef_id=000007900&format=xml&user_group=MBRG';
        break;
      case "PSY": 
        $url = 'http://vlib.mpg.de/resource_info?predef_id=000007901&format=xml&user_group=MBRG'; 
        break;
      case "POL": 
        $url = 'http://vlib.mpg.de/resource_info?predef_id=000007902&format=xml&user_group=MBRG'; 
        break;
      case "LAW":
        $url = 'http://vlib.mpg.de/resource_info?predef_id=000007903&format=xml&user_group=MBRG';
        break;
      case "COLL": 
        $url = 'http://vlib.mpg.de/resource_info?predef_id=000007897&format=xml&user_group=MBRG'; 
        break;
      default:
       $url = 'http://vlib.mpg.de/resource_info?predef_id=000007987&format=xml&user_group=MBRG'; 
      }
      $client = new Proxy_Request();
      $client->setMethod(HTTP_REQUEST_METHOD_GET);
      $client->setURL($url);
      
      $result = $client->sendRequest();
      if (PEAR::isError($result)) {
        $this->output("ERROR", RDG::STATUS_ERROR);
      }
      
      $info = $client->getResponseBody();
    }

      if (!PEAR::isError($info)) {
        // print xml; parsing happens in jQuery
        print_r($info);
      }

    }

}
?>
