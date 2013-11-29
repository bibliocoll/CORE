<?php
/**
 * MARC Record Driver
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
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
 * @package  RecordDrivers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/other_than_marc Wiki
 */
require_once 'RecordDrivers/IndexRecord.php';

/**
 * MARC Record Driver
 *
 * This class is designed to handle MARC records.  Much of its functionality
 * is inherited from the default index-based driver.
 *
 * @category VuFind
 * @package  RecordDrivers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/other_than_marc Wiki
 */
class MarcRecord extends IndexRecord
{
    protected $marcRecord;

    /**
     * Constructor.  We build the object using all the data retrieved 
     * from the (Solr) index (which also happens to include the 
     * 'fullrecord' field containing raw metadata).  Since we have to 
     * make a search call to find out which record driver to construct, 
     * we will already have this data available, so we might as well 
     * just pass it into the constructor.
     *
     * @param array $record All fields retrieved from the index.
     *
     * @access public
     */
    public function __construct($record)
    {
        // Call the parent's constructor...
        parent::__construct($record);

        // Also process the MARC record:
        $marc = trim($record['fullrecord']);

        // check if we are dealing with MARCXML
        $xmlHead = '<?xml version';
        if (strcasecmp(substr($marc, 0, strlen($xmlHead)), $xmlHead) === 0) {
            $marc = new File_MARCXML($marc, File_MARCXML::SOURCE_STRING);
        } else {
            $marc = preg_replace('/#31;/', "\x1F", $marc);
            $marc = preg_replace('/#30;/', "\x1E", $marc);
            $marc = new File_MARC($marc, File_MARC::SOURCE_STRING);
        }

        $this->marcRecord = $marc->next();
        if (!$this->marcRecord) {
            PEAR::raiseError(new PEAR_Error('Cannot Process MARC Record'));
        }
    }

    /**
     * Assign necessary Smarty variables and return a template name to 
     * load in order to export the record in the requested format.  For 
     * legal values, see getExportFormats().  Returns null if format is 
     * not supported.
     *
     * @param string $format Export format to display.
     *
     * @return string        Name of Smarty template file to display.
     * @access public
     */
    public function getExport($format)
    {
        global $interface;

        switch(strtolower($format)) {
        case 'endnote':
            // This makes use of core metadata fields in addition to the
            // assignment below:
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header('Content-type: application/x-endnote-refer');
            header("Content-Disposition: attachment; filename=\"vufind.enw\";");
            $interface->assign('marc', $this->marcRecord);
            return 'RecordDrivers/Marc/export-endnote.tpl';
        case 'marc':
            $interface->assign('rawMarc', $this->marcRecord->toRaw());
            return 'RecordDrivers/Marc/export-marc.tpl';
        case 'rdf':
            header("Content-type: application/rdf+xml");
            $interface->assign('rdf', $this->getRDFXML());
            return 'RecordDrivers/Marc/export-rdf.tpl';
        case 'refworks':
            // To export to RefWorks, we actually have to redirect to
            // another page.  We'll do that here when the user requests a
            // RefWorks export, then we'll call back to this module from
            // inside RefWorks using the "refworks_data" special export format
            // to get the actual data.
            $this->redirectToRefWorks();
            break;
        case 'refworks_data':
            // This makes use of core metadata fields in addition to the
            // assignment below:
            header('Content-type: text/plain; charset=utf-8');
            $interface->assign('marc', $this->marcRecord);
            return 'RecordDrivers/Marc/export-refworks.tpl';
            break;
        case 'bibtex':
            // This makes use of core metadata fields in addition to the
            // assignment below:
            header('Content-type: application/x-bibtex; charset=utf-8');
            $interface->assign('marc', $this->marcRecord);
            return 'RecordDrivers/Marc/export-bibtex.tpl';
            break;
        default:
            return null;
        }
    }

    /**
     * Get an array of strings representing formats in which this record's 
     * data may be exported (empty if none).  Legal values: "RefWorks", 
     * "EndNote", "MARC", "RDF".
     *
     * @return array Strings representing export formats.
     * @access public
     */
    public function getExportFormats()
    {
        // Get an array of legal export formats (from config array, or use defaults
        // if nothing in config array).
        global $configArray;
        $active = isset($configArray['Export']) ?
            $configArray['Export'] : array('RefWorks' => true, 'EndNote' => true);

        // These are the formats we can possibly support if they are turned on in
        // config.ini:
        $possible = array('RefWorks', 'EndNote', 'MARC', 'RDF', 'BibTeX');

        // Check which formats are currently active:
        $formats = array();
        foreach ($possible as $current) {
            if ($active[$current]) {
                $formats[] = $current;
            }
        }

        // Send back the results:
        return $formats;
    }

    /**
     * Get an XML RDF representation of the data in this record.
     *
     * @return mixed XML RDF data (false if unsupported or error).
     * @access public
     */
    public function getRDFXML()
    {
        // Get Record as MARCXML
        $xml = trim($this->marcRecord->toXML());

        // Load Stylesheet
        $style = new DOMDocument;
        //$style->load('services/Record/xsl/MARC21slim2RDFDC.xsl');
        $style->load('services/Record/xsl/record-rdf-mods.xsl');

        // Setup XSLT
        $xsl = new XSLTProcessor();
        $xsl->importStyleSheet($style);

        // Transform MARCXML
        $doc = new DOMDocument;
        if ($doc->loadXML($xml)) {
            return $xsl->transformToXML($doc);
        }

        // If we got this far, something went wrong.
        return false;
    }

    /**
     * Assign necessary Smarty variables and return a template name for the current
     * view to load in order to display a summary of the item suitable for use in
     * search results.
     *
     * @param string $view The current view.
     * 
     * @return string      Name of Smarty template file to display.
     * @access public
     */
    public function getSearchResult($view = 'list')
    {
        global $interface;

        // MARC results work just like index results, except that we want to
        // enable the AJAX status display since we assume that MARC records
        // come from the ILS:
        $template = parent::getSearchResult($view);
        $id = $this->getUniqueId();
        if (substr($id, 0, 3) == "RDG") {
            $interface->assign('summAjaxStatus', true);
        }
        else {
            $interface->assign('summAjaxStatus', false);
        }
        return $template;
    }

    /**
     * Assign necessary Smarty variables and return a template name to 
     * load in order to display the full record information on the Staff 
     * View tab of the record view page.
     *
     * @return string Name of Smarty template file to display.
     * @access public
     */
    public function getStaffView()
    {
        global $interface;

        // Get Record as MARCXML
        $xml = trim($this->marcRecord->toXML());

        // Prevent unprintable characters from interfering with the XSL transform:
        $xml = str_replace(array(chr(29), chr(30), chr(31)), ' ', $xml);

        // Transform MARCXML
        $style = new DOMDocument;
        $style->load('services/Record/xsl/record-marc.xsl');
        $xsl = new XSLTProcessor();
        $xsl->importStyleSheet($style);
        $doc = new DOMDocument;
        if ($doc->loadXML($xml)) {
            $html = $xsl->transformToXML($doc);
            $interface->assign('details', $html);
        }

        return 'RecordDrivers/Marc/staff.tpl';
    }

    /**
     * Assign necessary Smarty variables and return a template name to 
     * load in order to display the Table of Contents extracted from the 
     * record.  Returns null if no Table of Contents is available.
     *
     * @return string Name of Smarty template file to display.
     * @access public
     */
    public function getTOC()
    {
        global $interface;

        // Return null if we have no table of contents:
        $fields = $this->marcRecord->getFields('505');
        if (!$fields) {
            return null;
        }

        // If we got this far, we have a table -- collect it as a string:
        $toc = '';
        foreach ($fields as $field) {
            $subfields = $field->getSubfields();
            foreach ($subfields as $subfield) {
                $toc .= $subfield->getData();
            }
        }

        // Assign the appropriate variable and return the template name:
        $interface->assign('toc', $toc);
        return 'RecordDrivers/Marc/toc.tpl';
    }

    /**
     * Return an XML representation of the record using the specified format.
     * Return false if the format is unsupported.
     *
     * @param string $format Name of format to use (corresponds with OAI-PMH
     * metadataPrefix parameter).
     *
     * @return mixed         XML, or false if format unsupported.
     * @access public
     */
    public function getXML($format)
    {
        // Special case for MARC:
        if ($format == 'marc21') {
            $xml = $this->marcRecord->toXML();
            $xml = trim(str_replace(array(chr(29), chr(30), chr(31)), ' ', $xml));
            $xml = simplexml_load_string($xml);
            if (!$xml || !isset($xml->record)) {
                return false;
            }

            // Set up proper namespacing and extract just the <record> tag:
            $xml->record->addAttribute('xmlns', "http://www.loc.gov/MARC21/slim");
            $xml->record->addAttribute(
                'xsi:schemaLocation',
                'http://www.loc.gov/MARC21/slim ' .
                'http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd',
                'http://www.w3.org/2001/XMLSchema-instance'
            );
            $xml->record->addAttribute('type', 'Bibliographic');
            return $xml->record->asXML();
        }

        // Try the parent method:
        return parent::getXML($format);
    }

    /**
     * Does this record have a Table of Contents available?
     *
     * @return bool
     * @access public
     */
    public function hasTOC()
    {
        // Is there a table of contents in the MARC record?
        if ($this->marcRecord->getFields('505')) {
            return true;
        }
        return false;
    }

    /**
     * Does this record support an RDF representation?
     *
     * @return bool
     * @access public
     */
    public function hasRDF()
    {
        return true;
    }

    public function hasMPGdownLink()
    {
        $solr = ConnectionManager::connectToIndex();
        $link = null;
        if ($aleph_id = $this->marcRecord->getField("001")) {
            $id = $aleph_id->getData();
            $idlinks = $solr->search("ppnlink:" . $id);
            if ($idlinks['response']['numFound'] > 0) {
	      return true;
            }
        }
        return null;
    }

    /**
     * Get access restriction notes for the record.
     *
     * @return array
     * @access protected
     */
    protected function getAccessRestrictions()
    {
        return $this->_getFieldArray('506');
    }

    /**
     * Get all subject headings associated with this record.  Each heading is
     * returned as an array of chunks, increasing from least specific to most
     * specific.
     *
     * @return array
     * @access protected
     */
    protected function getAllSubjectHeadings()
    {
        // These are the fields that may contain subject headings:
        $fields = array('600', '610', '630', '650', '651', '653', '655', '648', '988');

        // This is all the collected data:
        $retval = array();

        // Try each MARC field one at a time:
        foreach ($fields as $field) {
            // Do we have any results for the current field?  If not, try the next.
            $results = $this->marcRecord->getFields($field);
            if (!$results) {
                continue;
            }

            // If we got here, we found results -- let's loop through them.
            foreach ($results as $result) {
                // Start an array for holding the chunks of the current heading:
                $current = array();

                // Get all the chunks and collect them together:
                $subfields = $result->getSubfields();
                if ($subfields) {
                    foreach ($subfields as $subfield) {
                        // Numeric subfields are for control purposes and should not
                        // be displayed:
                        if (!is_numeric($subfield->getCode())) {
                            $current[] = $subfield->getData();
                        }
                    }
                    // If we found at least one chunk, add a heading to our result:
                    if (!empty($current)) {
                        $retval[] = $current;
                    }
                }
            }
        }

        // Send back everything we collected:
        return $retval;
    }

    /**
     * Get award notes for the record.
     *
     * @return array
     * @access protected
     */
    protected function getAwards()
    {
        return $this->_getFieldArray('586');
    }

    /**
     * Get notes on bibliography content.
     *
     * @return array
     * @access protected
     */
    protected function getBibliographyNotes()
    {
        return $this->_getFieldArray('504');
    }

    /**
     * Get the main corporate author (if any) for the record.
     *
     * @return string
     * @access protected
     */
    protected function getCorporateAuthor()
    {
        return $this->_getFirstFieldValue('110', array('a', 'b'));
    }

    /**
     * Return an array of all values extracted from the specified field/subfield
     * combination.  If multiple subfields are specified and $concat is true, they
     * will be concatenated together in the order listed -- each entry in the array
     * will correspond with a single MARC field.  If $concat is false, the return
     * array will contain separate entries for separate subfields.
     *
     * @param string $field     The MARC field number to read
     * @param array  $subfields The MARC subfield codes to read
     * @param bool   $concat    Should we concatenate subfields?
     *
     * @return array
     * @access protected
     */
    private function _getFieldArray($field, $subfields = null, $concat = true)
    {
        // Default to subfield a if nothing is specified.
        if (!is_array($subfields)) {
            $subfields = array('a');
        }

        // Initialize return array
        $matches = array();

        // Try to look up the specified field, return empty array if it doesn't
        // exist.
        $fields = $this->marcRecord->getFields($field);
        if (!is_array($fields)) {
            return $matches;
        }

        // Extract all the requested subfields, if applicable.
        foreach ($fields as $currentField) {
            $next = $this->_getSubfieldArray($currentField, $subfields, $concat);
            $matches = array_merge($matches, $next);
        }

        return $matches;
    }

    /**
     * Get notes on finding aids related to the record.
     *
     * @return array
     * @access protected
     */
    protected function getFindingAids()
    {
        return $this->_getFieldArray('555');
    }

    /**
     * Get the first value matching the specified MARC field and subfields.
     * If multiple subfields are specified, they will be concatenated together.
     *
     * @param string $field     The MARC field to read
     * @param array  $subfields The MARC subfield codes to read
     *
     * @return string
     * @access private
     */
    private function _getFirstFieldValue($field, $subfields = null)
    {
        $matches = $this->_getFieldArray($field, $subfields);
        return (is_array($matches) && count($matches) > 0) ?
            $matches[0] : null;
    }

    /**
     * Get general notes on the record.
     *
     * @return array
     * @access protected
     */
    protected function getGeneralNotes()
    {
        return $this->_getFieldArray('500');
    }

    /**
     * Get the item's places of publication.
     *
     * @return array
     * @access protected
     */
    protected function getPlacesOfPublication()
    {
        return $this->_getFieldArray('260');
    }

    /**
     * Get an array of playing times for the record (if applicable).
     *
     * @return array
     * @access protected
     */
    protected function getPlayingTimes()
    {
        $times = $this->_getFieldArray('306', array('a'), false);

        // Format the times to include colons ("HH:MM:SS" format).
        for ($x = 0; $x < count($times); $x++) {
            $times[$x] = substr($times[$x], 0, 2) . ':' .
                substr($times[$x], 2, 2) . ':' .
                substr($times[$x], 4, 2);
        }

        return $times;
    }

    /**
     * Get credits of people involved in production of the item.
     *
     * @return array
     * @access protected
     */
    protected function getProductionCredits()
    {
        return $this->_getFieldArray('508');
    }

    /**
     * Get an array of publication frequency information.
     *
     * @return array
     * @access protected
     */
    protected function getPublicationFrequency()
    {
        return $this->_getFieldArray('310', array('a', 'b'));
    }

    /**
     * Get an array of information about record history, obtained in real-time
     * from the ILS.
     *
     * @return array
     * @access protected
     */
    protected function getRealTimeHistory()
    {
        // Get Acquisitions Data
        $id = $this->getUniqueID();
        $catalog = ConnectionManager::connectToCatalog();
        if ($catalog && $catalog->status) {
            $result = $catalog->getPurchaseHistory($id);
            if (PEAR::isError($result)) {
                PEAR::raiseError($result);
            }
            return $result;
        }
        return array();
    }

    /**
     * Get an array of information about record holdings, obtained in real-time
     * from the ILS.
     * 
     * @param array $patron An array of patron data
     *
     * @return array
     * @access protected
     */
    protected function getRealTimeHoldings($patron = false)
    {
        // Get ID and connect to catalog
        $id = $this->getUniqueID();
        $catalog = ConnectionManager::connectToCatalog();

        include_once 'sys/HoldLogic.php';
        $holdLogic = new HoldLogic($catalog);
        $holdings = $holdLogic->getHoldings($id, $patron);
        if(empty($holdings)) {
            $ids = $this->getGBVLink("773");
            foreach($ids as $id) {
                $holdings = $holdLogic->getHoldings($id, $patron);
                if(!empty($holdings)) break;
            }
        }
        return $holdings;

    }

    /**
     * Get an array of strings describing relationships to other items.
     *
     * @return array
     * @access protected
     */
    protected function getRelationshipNotes()
    {
        return $this->_getFieldArray('580');
    }

    /**
     * Get an array of all series names containing the record.  Array entries may
     * be either the name string, or an associative array with 'name' and 'number'
     * keys.
     *
     * @return array
     * @access protected
     */
    protected function getSeries()
    {
        $matches = array();

        // First check the 440, 800 and 830 fields for series information:
        $primaryFields = array(
            '440' => array('a', 'p'),
            '800' => array('a', 'b', 'c', 'd', 'f', 'p', 'q', 't'),
            '830' => array('a', 'p'));
        $matches = $this->_getSeriesFromMARC($primaryFields);
        if (!empty($matches)) {
            return $matches;
        }

        // Now check 490 and display it only if 440/800/830 were empty:
        $secondaryFields = array('490' => array('a'));
        $matches = $this->_getSeriesFromMARC($secondaryFields);
        if (!empty($matches)) {
            return $matches;
        }

        // Still no results found?  Resort to the Solr-based method just in case!
        return parent::getSeries();
    }

    /**
     * Support method for getSeries() -- given a field specification, look for
     * series information in the MARC record.
     *
     * @param array $fieldInfo Associative array of field => subfield information
     * (used to find series name)
     *
     * @return array
     * @access private
     */
    private function _getSeriesFromMARC($fieldInfo)
    {
        $matches = array();

        // Loop through the field specification....
        foreach ($fieldInfo as $field => $subfields) {
            // Did we find any matching fields?
            $series = $this->marcRecord->getFields($field);
            if (is_array($series)) {
                foreach ($series as $currentField) {
                    // Can we find a name using the specified subfield list?
                    $name = $this->_getSubfieldArray($currentField, $subfields);
                    if (isset($name[0])) {
                        $currentArray = array('name' => $name[0]);

                        // Can we find a number in subfield v?  (Note that number is
                        // always in subfield v regardless of whether we are dealing
                        // with 440, 490, 800 or 830 -- hence the hard-coded array
                        // rather than another parameter in $fieldInfo).
                        $number
                            = $this->_getSubfieldArray($currentField, array('v'));
                        if (isset($number[0])) {
                            $currentArray['number'] = $number[0];
                        }

                        // Save the current match:
                        $matches[] = $currentArray;
                    }
                }
            }
        }

        return $matches;
    }

    /**
     * Return an array of non-empty subfield values found in the provided MARC
     * field.  If $concat is true, the array will contain either zero or one
     * entries (empty array if no subfields found, subfield values concatenated
     * together in specified order if found).  If concat is false, the array
     * will contain a separate entry for each subfield value found.
     *
     * @param object $currentField Result from File_MARC::getFields.
     * @param array  $subfields    The MARC subfield codes to read
     * @param bool   $concat       Should we concatenate subfields?
     *
     * @return array
     * @access private
     */
    private function _getSubfieldArray($currentField, $subfields, $concat = true)
    {
        // Start building a line of text for the current field
        $matches = array();
        $currentLine = '';

        // Loop through all subfields, collecting results that match the whitelist;
        // note that it is important to retain the original MARC order here!
        $allSubfields = $currentField->getSubfields();
        if (count($allSubfields) > 0) {
            foreach ($allSubfields as $currentSubfield) {
                if (in_array($currentSubfield->getCode(), $subfields)) {
                    // Grab the current subfield value and act on it if it is
                    // non-empty:
                    $data = trim($currentSubfield->getData());
                    if (!empty($data)) {
                        // Are we concatenating fields or storing them separately?
                        if ($concat) {
                            $currentLine .= $data . ' ';
                        } else {
                            $matches[] = $data;
                        }
                    }
                }
            }
        }

        // If we're in concat mode and found data, it will be in $currentLine and
        // must be moved into the matches array.  If we're not in concat mode,
        // $currentLine will always be empty and this code will be ignored.
        if (!empty($currentLine)) {
            $matches[] = trim($currentLine);
        }

        // Send back our result array:
        return $matches;
    }

    /**
     * Get an array of summary strings for the record.
     *
     * @return array
     * @access protected
     */
    protected function getSummary()
    {
        return $this->_getFieldArray('520');
    }

    /**
     * Get an array of technical details on the item represented by the record.
     *
     * @return array
     * @access protected
     */
    protected function getSystemDetails()
    {
        return $this->_getFieldArray('538');
    }

    /**
     * Get an array of note about the record's target audience.
     *
     * @return array
     * @access protected
     */
    protected function getTargetAudienceNotes()
    {
        return $this->_getFieldArray('521');
    }

    /**
     * Get the text of the part/section portion of the title.
     *
     * @return string
     * @access protected
     */
    protected function getTitleSection()
    {
        return $this->_getFirstFieldValue('245', array('n', 'p'));
    }

    /**
     * Get the statement of responsibility that goes with the title (i.e. "by John
     * Smith").
     *
     * @return string
     * @access protected
     */
    protected function getTitleStatement()
    {
        return $this->_getFirstFieldValue('245', array('c'));
    }

    /**
     * Return an associative array of URLs associated with this record (key = URL,
     * value = description).
     *
     * @return array
     * @access protected
     */
    protected function getURLs()
    {
        global $configArray;

        $iln = $configArray['Site']['iln'];

        $retVal = array();

        if($this->fields['collection'][0] == "EZB") {
           $urls = $this->marcRecord->getFields('956');
           if ($urls) {
              foreach ($urls as $url) {
                  $address = $url->getSubfield('r');
                  if ($address) {
                      $address = $address->getData();
                      $retVal[$address] = $address;
                  }
              }
              return $retVal;
           }
        }

        $ezburls = $this->marcRecord->getFields('981');
        if ($ezburls) {
            foreach ($ezburls as $ezburl) {
                $f2 = $ezburl->getSubfield('2');
                if ($f2 != null) {
                    if ($f2->getData() == $iln) {
                        $addressf = $ezburl->getSubfield('r');
                        if ($addressf != null) {
                            $address = $addressf->getData();
                            if (preg_match('/www.bibliothek.uni-regensburg.de\/ezeit/',$address)) {
                                $retVal[$address] = $address;
                                return $retVal;
                            }
                        }
                    }
                }
            }
        }


        $urls = $this->marcRecord->getFields('856');
        if ($urls) {
            foreach ($urls as $url) {
                // Is there an address in the current field?
                $address = $url->getSubfield('u');
                if ($address) {
                    $address = $address->getData();

                    // Is there a description?  If not, just use the URL itself.
                    // $desc = $url->getSubfield('z');
                    $desc = ''; /* RDG: do not use 'z' in any case! (button logic) */
                    if ($desc) {
                        $desc = $desc->getData();
                    } elseif ($desc = $url->getSubfield('3')) {
                        $desc = $desc->getData();
                    } else {
                      //  $desc = $address;
                      $desc = "Online";
                    }

                    $retVal[$address] = $desc;
                }
            }
        }

        $urls = $this->marcRecord->getFields('995');
        if ($urls) {
            foreach ($urls as $url) {
                // Is there an address in the current field?
                $address = $url->getSubfield('u');
                if ($address) {
                    $address = $address->getData();

                    // Is there a description?  If not, just use the URL itself.
                    $desc = $url->getSubfield('3');
                    if ($desc) {
                        $desc = $desc->getData();
                    } else {
                        $desc = $address;
                    }

                    $retVal[$address] = $desc;
                }
            }
        }

        return $retVal;
    }

    /**
     * Redirect to the RefWorks site and then die -- support method for getExport().
     *
     * @return void
     * @access protected
     */
    protected function redirectToRefWorks()
    {
        global $configArray;

        // Build the URL to pass data to RefWorks:
        $exportUrl = $configArray['Site']['url'] . '/Record/' .
            urlencode($this->getUniqueID()) . '/Export?style=refworks_data';

        // Build up the RefWorks URL:
        $url = $configArray['RefWorks']['url'] . '/express/expressimport.asp';
        $url .= '?vendor=' . urlencode($configArray['RefWorks']['vendor']);
        $url .= '&filter=RefWorks%20Tagged%20Format&url=' . urlencode($exportUrl);

        header("Location: {$url}");
        die();
    }

    /**
     * Get all record links related to the current record. Each link is returned as
     * array.
     * Format:
     * array(
     *        array(
     *               'title' => label_for_title
     *               'value' => link_name
     *               'link'  => link_URI
     *        ),
     *        ...
     * )
     *
     * @return null|array
     * @access protected
     */
    protected function getAllRecordLinks()
    {
        global $configArray;

        $fieldsNames = isset($configArray['Record']['marc_links'])
            ? explode(',', $configArray['Record']['marc_links']) : array();
        $retVal = array();
        foreach ($fieldsNames as $value) {
            $value = trim($value);
            $fields = $this->marcRecord->getFields($value);
            if (!empty($fields)) {
                foreach ($fields as $field) {
                    $indicator = $field->getIndicator('2');
                    switch ($value) {
                    case '780':
                        if ($indicator == '0' || $indicator == '1'
                            || $indicator == '5'
                        ) {
                            $value .= '_' . $indicator;
                        }
                        break;
                    case '785':
                        if ($indicator == '0' || $indicator == '7') {
                            $value .= '_' . $indicator;
                        }
                        break;
                    }
                    $tmp = $this->_getFieldData($field, $value);
                    if (is_array($tmp)) {
                        $retVal[] = $tmp;
                    }
                }
            }
        }
        if (empty($retVal)) {
            $retVal = null;
        }
        return $retVal;
    }

    /**
     * Returns the array element for the 'getAllRecordLinks' method
     *
     * @param File_MARC_Data_Field $field Field to examine
     * @param string               $value Field name for use in label
     *
     * @access private
     * @return array|bool                 Array on success, boolean false if no
     * valid link could be found in the data.
     */
    private function _getFieldData($field, $value)
    {
        global $configArray;

        $labelPrfx   = 'note_';
        $baseURI     = $configArray['Site']['url'];

        // There are two possible ways we may want to link to a record -- either
        // we will have a raw bibliographic record in subfield w, or else we will
        // have an OCLC number prefixed by (OCoLC).  If we have both, we want to
        // favor the bib number over the OCLC number.  If we have an unrecognized
        // parenthetical prefix to the number, we should simply ignore it.
        $bib = $oclc = '';
        $linkFields = $field->getSubfields('w');
        foreach ($linkFields as $current) {
            $text = $current->getData();
            // Extract parenthetical prefixes:
            if (preg_match('/\(([^)]+)\)(.+)/', $text, $matches)) {
                // Is it an OCLC number?
                if ($matches[1] == 'OCoLC') {
                    $oclc = $baseURI . '/Search/Results?lookfor=' .
                        urlencode($matches[2]) . '&type=oclc_num&jumpto=1';
                }
            } else {
                // No parenthetical prefix found -- assume raw bib number:
                $bib = $baseURI . '/Record/' . $text;
            }
        }

        // Check which link type we found in the code above... and fail if we
        // found nothing!
        if (!empty($bib)) {
            $link = $bib;
        } else if (!empty($oclc)) {
            $link = $oclc;
        } else {
            return false;
        }

        return array(
            'title' => $labelPrfx.$value,
            'value' => $field->getSubfield('t')->getData(),
            'link'  => $link
        );
    }

    // MPG specific
    protected function getProduct()
    {
        if (!$this->isGBVRecord()) return $this->_getFirstFieldValue('981', array('a'));        
    }

    protected function getMPGfieldByIndicator($f, $sf, $ind)
    {
        $fields = $this->marcRecord->getFields($f);
        foreach ($fields as $field) {
            if ($field->getIndicator(2) == $ind) {
                if ($subfield = $field->getSubfield($sf)) {
                    return $subfield->getData();
                } /* else {
                    return null;
                } */
            }
        }
        return null;
    }

    protected function getMPGByIndicator($f, $ind)
    {
        $fields = $this->marcRecord->getFields($f);
        foreach ($fields as $field) {
            if ($field->getIndicator(2) == $ind) {
                return $field;
            }
        }
    }

    protected function get9843a()
    {
       return $this->getMPGfieldByIndicator('984','a','3');
    }

    protected function get9843b()
    {
       return $this->getMPGfieldByIndicator('984','b','3');
    }

    protected function get9843c()
    {
       return $this->getMPGfieldByIndicator('984','c','3');
    }

    protected function get9842n()
    {
       return $this->getMPGfieldByIndicator('984','n','2');
    }

    protected function get9842a()
    {
       return $this->getMPGfieldByIndicator('984','a','2');
    }

    protected function get9842b()
    {
       return $this->getMPGfieldByIndicator('984','b','2');
    }

    protected function get9842c()
    {
       return $this->getMPGfieldByIndicator('984','c','2');
    }

    protected function getMPGtitle()
    {
       global $language;
       $title = null;
       if ($f245n = $this->getMPGfieldByIndicator('245','n','0')) {
           if (is_numeric($f245n)) {
               if ($language == "de") {
                   $title = "Bd. ";
               } else {
                   $title = "Vol. ";
               }
           }
           $title = $title . $f245n;
           if ($this->_getFirstFieldValue('245', array('a'))) {
               $title = $title . ": ";
           }
       }
       if ($f245a = $this->_getFirstFieldValue('245', array('a'))) {
           $title = $title . $f245a;
       } 
       if ($f245b = $this->_getFirstFieldValue('245', array('b'))) {
           $title = $title . ": " . $f245b;
       }
       return $title;
    }

    protected function getMPGPrimaryAuthor()
    {
        $author = null;
        if ($field = $this->_getFirstFieldValue('100', array('a'))) {
            $author = $field . " " . $this->_getFirstFieldValue('100', array('e'));
        }
        else if ($field = $this->_getFirstFieldValue('700', array('a'))) {
            $author = $field . " " . $this->_getFirstFieldValue('700', array('e'));
        }
        return trim($author);
    }

    protected function getMPGtopPrimaryAuthor()
    {
        if (!$this->isGBVRecord()) {
            $author = null;
            if ($field = $this->_getFirstFieldValue('980', array('a'))) {
                $author = $field . " " . $this->getMPGfieldByIndicator('980','e','2');
            }
            return trim($author);
        }
        return null;
    }

    protected function getMPGSecondaryAuthors()
    {
        $authors = null;
        if ($fields = $this->marcRecord->getFields('700')) {
            foreach ($fields as $field) {
                if ($sf = $field->getSubfield('a')) {
                    $author = $sf->getData();
                    if ($sf = $field->getSubfield('e')) {
                        $author = $author . " " . $sf->getData();
                    }
                }
                $authors[] = trim($author);
            }
        }
        return $authors;
    }

    protected function getMPGtopSecondaryAuthors()
    {
        if (!$this->isGBVRecord()) {
            $authors = null;
            if ($fields = $this->marcRecord->getFields('980')) {
                 foreach ($fields as $field) {
                    if ($sf = $field->getSubfield('a')) {
                        $author = $sf->getData();
                        if ($sf = $field->getSubfield('e')) {
                            $author = $author . " " . $sf->getData();
                        }
                    }
                    $authors[] = trim($author);
                }
            }
            if ($fields = $this->marcRecord->getFields('981')) {
                foreach ($fields as $field) {
                    if ($sf = $field->getSubfield('a')) {
                        $author = $sf->getData();
                        if ($sf = $field->getSubfield('e')) {
                            $author = $author . " " . $sf->getData();
                        }
                    }
                    $authors[] = trim($author);
                }
            }
            return $authors;
        }
        return null;
    }

    protected function getMPGCorporate()
    {
        $author = null;
        if ($field = $this->_getFirstFieldValue('110', array('a'))) {
            $author = $field;
        }
        else if ((!$this->isGBVRecord()) && $field = $this->_getFirstFieldValue('982', array('a'))) {
            $author = $field;
        }
        return trim($author);
    }

    protected function getMPGSecondaryCorps()
    {
        $authors = null;
        if ($this->_getFirstFieldValue('110', array('a'))) {
            if ((!$this->isGBVRecord()) && $fields = $this->marcRecord->getFields('983')) {
                foreach ($fields as $field) {
                    if ($sf = $field->getSubfield('a')) {
                        $author = $sf->getData();
                        $authors[] = trim($author);
                    }
                }
            }
        }
        if ($fields = $this->marcRecord->getFields('710')) {
            foreach ($fields as $field) {
                if ($sf = $field->getSubfield('a')) {
                    $author = $sf->getData();
                    $authors[] = trim($author);
                }
            }
        }
        return $authors;
    }

    protected function getMPGPlacesOfPublication()
    {
        $published = null;
        $f9853a = $this->getMPGfieldByIndicator("985", "a", "3");
        $f9853b = $this->getMPGfieldByIndicator("985", "b", "3");
        $f9853c = $this->getMPGfieldByIndicator("985", "c", "3");
        $f9852a = $this->getMPGfieldByIndicator("985", "a", "2");
        $f9852b = $this->getMPGfieldByIndicator("985", "b", "2");
        $f9852c = $this->getMPGfieldByIndicator("985", "c", "2");
        $f260a = $this->_getFieldArray("260", array("a"));
        $f260b = $this->_getFieldArray("260", array("b"));
        $f260c = $this->_getFieldArray("260", array("c"));
        $place = $f260a[0] ? $f260a[0] : ($f9853a ? $f9853a : $f9852a);
        $publisher = $f260b[0] ? $f260b[0] : ($f9853b ? $f9853b : $f9852b);
        $year = $f260c[0] ? $f260c[0] : ($f9853c ? $f9853c : $f9852c);
        if (isset($place) || isset($publisher) || isset($year)) {
            return $place . " " . $publisher . " " . $year;
        } else {
            return;
        }
    }

    protected function getMPGEdition()
    {
        $f9863a = $this->getMPGfieldByIndicator("986", "a", "3");
        $f9862a = $this->getMPGfieldByIndicator("986", "a", "2");
        $f250a = $this->_getFieldArray("250", array("a"));
        return $f250a[0] ? $f250a[0] : ($f9863a ? $f9863a : $f9862a);
    }

    protected function getMPGSeries()
    {
        $matches = array();
        $secondaryFields = array('490' => array('a'));
        $matches = $this->_getSeriesFromMARC($secondaryFields);
        if (!empty($matches)) {
            return $matches;
        } else {
            return null;
        }
    }

    protected function getMPGHoldings()
    {
        return $this->_getFieldArray("866", array("a"));
    }

    protected function getMPGUpLink()
    { /* nur wenn results > 1, um Link bei einzelnen Bänden auszublenden! */
        $solr = ConnectionManager::connectToIndex();
        $link = null;
        if ($field = $this->marcRecord->getField("773")) {
            if ($subfield = $field->getSubfield("w")) {
                $link = $subfield->getData();
	        $result = $solr->search("ppnlink:" . $link);
		if ($result['response']['numFound'] > 1) {
                  return $link;
               }
            }
        }
        return null;
    }

    protected function getMPGdownLink()
    {
        $solr = ConnectionManager::connectToIndex();
        $link = null;
        if ($aleph_id = $this->marcRecord->getField("001")) {
            $id = $aleph_id->getData();
            $idlinks = $solr->search("ppnlink:" . $id);
            if ($idlinks['response']['numFound'] > 0) {
	      return $id;
            }
        }
        return null;
    }

    protected function getMPGdownLinkRecords()
    // Unsolved Sorting Issues: does not work if +1000 or date only in MARC 952 (sorting in array, not $solr->search).
    {
        $solr = ConnectionManager::connectToIndex();
        $link = null;
        if ($aleph_id = $this->marcRecord->getField("001")) {
          $id = $aleph_id->getData();
          // max. 1000 downlinks:
          $idlinks = $solr->search("ppnlink:" .$id, null, null, 0, 1000, null, '', null, null, 'author, title, id, publishDate',  HTTP_REQUEST_METHOD_POST , false, false);    
          if ($idlinks['response']['numFound'] > 0) {
            $linked_entries = array();

            foreach ($idlinks['response']['docs'] as $doc) {
              // checks for PHP notices
	      if (!empty($doc['author'])) { $author = $doc['author'];} else {$author= "";}
	      if (!empty($doc['title'])) { $title = $doc['title'];} else {$title= "";}
	      if (!empty($doc['publishDate'][0])) { $publishDate = $doc['publishDate'][0];} else {$publishDate= "";}
              $linked_entries[] = array(
                                        "author" => $author,
                                        "title" => $title,
                                        "publishDate" => $publishDate,
                                        "id" => $doc['id']
                                        );
            }
            // sorting:
            foreach ($linked_entries as $key => $row) { 
              $sort[$key] = $row['publishDate'];
              $sort2[$key] = $row['author'];
            }
            array_multisort($sort, SORT_DESC, $sort2, SORT_ASC, $linked_entries); 
            
            return $linked_entries;
            
          }
        }
        return null;
    }

    protected function getMPGSeriesUpLink()
    {
        $link = null;
        if ($field = $this->marcRecord->getField("830")) {
            if ($subfield = $field->getSubfield("a")) {
                $link = $subfield->getData();
            }
        }
        return $link;
    }

    protected function getMPGPreviousLater($f)
    {
        $prev = array();
        if ($fields = $this->marcRecord->getFields($f)) {
            foreach ($fields as $field) {
                if ($subfield = $field->getSubfield("a")) {
                    $a = $subfield->getData();
                }
                elseif ($subfield = $field->getSubfield("t")) {
                    $a = $subfield->getData();
                }
                if ($subfield = $field->getSubfield("i")) {
                    $i = $subfield->getData();
                }
                if ($subfield = $field->getSubfield("w")) {
                    $w = $subfield->getData();
                }
                $prev[] = array("a" => $a, "i" => $i, "w" => $w);
            }
        }
        if (empty($prev[0]["a"])) $prev = null;
        return $prev;
    }

    protected function getMPGADAM()
    {
        $adams = array();

        $adamStrings = getExtraConfigArray('adam');

        if ($fields = $this->marcRecord->getFields("991")) {
            foreach ($fields as $field) {
                $adam = array();
                if ($subfield = $field->getSubfield("3")) {
                    $adam['text'] = $subfield->getData();
                }
                if ($subfield = $field->getSubfield("u")) {
                    $adam['url'] = $subfield->getData();
                }
                $adam['label'] = null;
                foreach ($adamStrings['regexp'] as $regexp => $label) {
                    if (preg_match('/' . strtolower($regexp) . '/', strtolower($adam['text']))) {
                        $adam['label'] = $label;
                    }
                }
                if ($adam['label'] != null) {
                    $adams[] = $adam;
                }
            }
        }
        return $adams;
    }

    protected function getPublicationDates()
    {
        if($this->fields['collection'][0] != "EZB") {
            return isset($this->fields['publishDate']) ?
                $this->fields['publishDate'] : array();
        }
        return null;
    }

    protected function getGBVSource()
    {
        if (isset($this->fields['sourceStr'])) {
            return $this->fields['sourceStr'];
        } 
        else {
            if ($this->_getFirstFieldValue('773', array('t'))) {
                return $this->_getFirstFieldValue('773', array('i')) .  " " . $this->_getFirstFieldValue('773', array('t')) . " (" . $this->_getFirstFieldValue('773', array('d')). "), " . $this->_getFirstFieldValue('773', array('g'));
            } elseif ($this->_getFirstFieldValue('773', array('w')) && $this->_getFirstFieldValue('773', array('g'))) {
                return "unknown title";
            }            
        } 
        return '';
    }

    protected function getGBVLink($f)
    {
        $links = array();
        if ($fields = $this->marcRecord->getFields($f)) {
            foreach ($fields as $field) {
                if ($link = $field->getSubfield("w")) {
                    $id = $this->getUniqueID();
                    $link = preg_replace("/^\(.*\)/", "", $link->getData());
                    if (substr($id, 0, 3) == "NLZ") $link = "NLZ" . $link;
                    $links[] = $link;
                }
            }
        }
        return $links;
    }

    /* RDG-specific */
    protected function getMPGSubjectHeadingsRSWK()
    {
        // These are the fields that may contain subject headings:
      $fields = array('600', '650');

        // This is all the collected data:
        $retval = array();

        // Try each MARC field one at a time:
        foreach ($fields as $field) {
                   // Do we have any results for the current field?  If not, try the next.
 
            $results = $this->marcRecord->getFields($field);
            if (!$results) {
                continue;
            }

            // If we got here, we found results -- let's loop through them.
            foreach ($results as $result) {
                // Start an array for holding the chunks of the current heading:
                $current = array();
                // check for RSWK in Subfield 2 (Mapping RDG)
                $sub = $result->getSubfield("2");
                if (empty($sub)) { continue; }
                else {
                  $sub = $sub->getData();
                  if ($sub != 'RSWK') {  
		    continue;
                  }    
                // Get all the chunks and collect them together:   
                $subfields = $result->getSubfields();
                if ($subfields) {
                    foreach ($subfields as $subfield) {
			  if (!is_numeric($subfield->getCode())) {
                            $current[] = $subfield->getData();
                          }
                    }
                    // If we found at least one chunk, add a heading to our result:
                    if (!empty($current)) {
                        $retval[] = $current;
                    }
                }
		}
	    }
        }

        // Send back everything we collected:
        return $retval;
    }

    protected function getMPGSubjectHeadingsSTW()
    {
        // These are the fields that may contain subject headings:
      $fields = array('600', '650');

        // This is all the collected data:
        $retval = array();

        // Try each MARC field one at a time:
        foreach ($fields as $field) {
                   // Do we have any results for the current field?  If not, try the next.
 
            $results = $this->marcRecord->getFields($field);
            if (!$results) {
                continue;
            }

            // If we got here, we found results -- let's loop through them.
            foreach ($results as $result) {
                // Start an array for holding the chunks of the current heading:
                $current = array();
                // check for STW in Subfield 2 (Mapping RDG)
                $sub = $result->getSubfield("2");
                if (empty($sub)) { continue; }
                else {
                  $sub = $sub->getData();
                  if ($sub != 'STW') {  
		    continue;
                  }  
                // Get all the chunks and collect them together:   
                $subfields = $result->getSubfields();
                if ($subfields) {
                    foreach ($subfields as $subfield) {
		      // get numeric subfields too (get id, reuse subjects mechanism to link back to ZBW/STW, configure via core.tpl)
	                     $current[] = $subfield->getData();
                            }
                    // If we found at least one chunk, add a heading to our result:
                    if (!empty($current)) {
                        $retval[] = $current;
                    }
                }
		}
	    }
        }

        // Send back everything we collected:
        return $retval;
    }

    protected function getMPGSubjectHeadingsSH()
    {
        // These are the fields that may contain subject headings:
      $fields = array('600', '650');

        // This is all the collected data:
        $retval = array();

        // Try each MARC field one at a time:
        foreach ($fields as $field) {
                   // Do we have any results for the current field?  If not, try the next.
 
            $results = $this->marcRecord->getFields($field);
            if (!$results) {
                continue;
            }

            // If we got here, we found results -- let's loop through them.
            foreach ($results as $result) {
                // Start an array for holding the chunks of the current heading:
                $current = array();
                // check for Subject Headings: Subfield 2 = empty = SH (Mapping RDG)
                $sub = $result->getSubfield("2");
                if (!empty($sub)) { continue; }
                else {
                // Get all the chunks and collect them together:   
                $subfields = $result->getSubfields();
                if ($subfields) {
                    foreach ($subfields as $subfield) {
			  if (!is_numeric($subfield->getCode())) {
                            $current[] = $subfield->getData();
                          }
                    }
                    // If we found at least one chunk, add a heading to our result:
                    if (!empty($current)) {
                        $retval[] = $current;
                    }
                }
		}
	    }
        }

        // Send back everything we collected:
        return $retval;
    }

    protected function getMPGClassificationShort()
    { /* get only if local data to avoid problems with other data */
      if ($aleph_id = $this->marcRecord->getField("001")) {
        preg_match('/RG.+/', $aleph_id, $matches);
        if ($matches) {
        return $this->_getFieldArray('084');
        }
      }
    }

    protected function getMPGClassificationLong()
    {
      return $this->_getFieldArray('699');
    }

    protected function getMPGClassificationJEL()
    {
      return $this->_getFieldArray('080');
    }

    protected function getMPGAbrufzeichen()
    {
      //  return $this->_getFieldArray('993');
      $ar993 = $this->_getFieldArray('993'); // MAB 078
      $ar996 = $this->_getFieldArray('996'); // MAB 076
      $az = array_merge($ar993,$ar996);

      // add "newbook" to array for recent acquisitions:
      $curdate = date('ym');
      $pastdate = date('ym', strtotime("-3 months"));
      foreach ($az as $z) {
        if (preg_match("/^\d{4}$/",$z)) {
          if ($z >= $pastdate) {
            $az[] = "newbook";
          }
        }
      }
      return $az;
    }

    public function getMPGOpenUrl() {

        global $configArray;

        $coinsID = isset($configArray['OpenURL']['rfr_id']) ?
            $configArray['OpenURL']['rfr_id'] :
            $configArray['COinS']['identifier'];
        if (empty($coinsID)) {
            $coinsID = 'vufind.svn.sourceforge.net';
        }

        $openURLvalfmt = getExtraConfigArray('openURLvalfmt');
        $openURLrftgenre = getExtraConfigArray('openURLrftgenre');
        $x = false;
        $val = null;

        $formats = $this->getFormats();

        $params = array(
            'ctx_ver' => 'Z39.88-2004',
            'ctx_enc' => 'info:ofi/enc:UTF-8',
            'rfr_id' => "info:sid/{$coinsID}",
        );
        $a = false;
        $b = false;
        foreach ($formats as $format) {
            if(!empty($openURLvalfmt[$format])) {
                $params['rft_val_fmt'] = $openURLvalfmt[$format];
                $a = true;
            }
            if(!empty($openURLrftgenre[$format])) {
                $params['rft.genre'] = $openURLrftgenre[$format];
                $b = true;
            }
            if ($a && $b) break;
        }
        if (empty($params['rft_val_fmt'])) $params['rtf_val_fmt'] = "info:ofi/fmt:kev:mtx:book";
        if (empty($params['rft.genre'])) $params['rft.genre'] = "unknown";

        if ($params['rft_val_fmt'] == "check") {
            if ($field = $this->marcRecord->getField("773")) {
                if ($subfield = $field->getSubfield("7")) {
                    $val = $subfield->getData();
                }
                if ($subfield = $field->getSubfield("x")) {
                    $x = true;
                }
            }
            $leader = $this->marcRecord->getLeader();
            if ($val{2} == "s" || $x == true || $leader{7} == "b") {
                $params['rft_val_fmt'] = "info:ofi/fmt:kev:mtx:journal";
                $params['rft.genre'] = "article";
            }
            else {
                $params['rft_val_fmt'] = "info:ofi/fmt:kev:mtx:book";
                $params['rft.genre'] = "bookitem";
            }
        }

        switch ($params['rft_val_fmt']) {
        case 'info:ofi/fmt:kev:mtx:journal':
            if ($field = $this->marcRecord->getField("773")) {
                $params['rft.atitle'] = $this->_getFirstFieldValue('245', array('a'));
                $params['rft.jtitle'] = $this->_getFirstFieldValue('773', array('t'));
            }
            else {
                $params['rft.jtitle'] = $this->_getFirstFieldValue('245', array('a'));
            }
            if ($this->_getFirstFieldValue('773', array('p'))) {
                $params['rft.stitle'] = $this->_getFirstFieldValue('773', array('p'));
            }
            else if ($this->_getFirstFieldValue('210', array('a'))) {
                $params['rft.stitle'] = $this->_getFirstFieldValue('210', array('a'));
            }
            $aut = $this->getMPGPrimaryAuthor();
            if ($aut) {
                $params['rft.aulast'] = strstr($aut, ',', true);
            }
            $params['rft.aucorp'] = $this->_getFirstFieldValue('110', array('a'));
            if ($this->_getFirstFieldValue('260', array('c'))) {
                $params['rft.date'] = $this->_getFirstFieldValue('260', array('c'));
            }
            else if ($this->_getFirstFieldValue('952', array('j'))) {
                $params['rft.date'] = $this->_getFirstFieldValue('952', array('j'));
            }
            else if ($this->marcRecord->getField("008")) {
                $f008 = $this->marcRecord->getField("008")->getData();
                if (strlen($f008) > 9) $params['rft.date'] = substr($f008,6,4);
                if (!preg_match("/[0-9][0-9][0-9][0-9]/", $params['rft.date'])) $params['rft.date'] = null;
            }
            $params['rft.volume'] = $this->_getFirstFieldValue('952', array('d'));
            $params['rft.issue'] = $this->_getFirstFieldValue('952', array('e'));
            if ($this->_getFirstFieldValue('952', array('h'))) {
                $params['rft.pages'] = $this->_getFirstFieldValue('952', array('h'));
            }
            else if ($this->_getFirstFieldValue('773', array('g'))) {
                $params['rft.pages'] = $this->_getFirstFieldValue('773', array('g'));
            }
            if ($params['rft.pages']) $params['rft.spage'] = strstr($params['rft.pages'], '-', true);
            if ($this->_getFirstFieldValue('773', array('x'))) {
                $params['rft.issn'] = $this->_getFirstFieldValue('773', array('x'));
            }
            else if ($this->_getFirstFieldValue('022', array('a'))) {
                $params['rft.issn'] = $this->_getFirstFieldValue('022', array('a'));
            }
            if ($this->_getFirstFieldValue('773', array('z'))) {
                $params['rft.isbn'] = $this->_getFirstFieldValue('773', array('z'));
            }
            else if ($this->_getFirstFieldValue('020', array('a'))) {
                $params['rft.isbn'] = $this->_getFirstFieldValue('020', array('a'));
            }
            if ($this->_getFirstFieldValue('773', array('g'))) $params['rft_dat'] = "<source>" . $this->_getFirstFieldValue('773', array('g')) . "</source>";
            $params['rft_id'][] = $configArray['Site']['url'] . "/Record/" . $this->getUniqueID();
            if ($this->_getFirstFieldValue('024', array('2')) == "doi") {
                $params['rft_id'][] = "info:doi/" . $this->_getFirstFieldValue('024', array('a'));
            }
            else if ($urls = $this->marcRecord->getFields('856')) {
                foreach ($urls as $url) {
                    $u = $url->getSubfield('u');
                    if ($u) {
                        if(preg_match("/http:\/\/dx.doi.org\.{0,1}\/(.*?)(\?|$)/", $u->getData(), $doi)) {
                            $params['rft_id'][] = "info:doi/" . $doi[1];
                         }
                    }
                }
            }
            $params['rft.au'] = array();
            if ($this->_getFirstFieldValue('100', array('a'))) {
                $params['rft.au'][] = $this->_getFirstFieldValue('100', array('a'));
            }       
            if ($this->getMPGSecondaryAuthors()) $params['rft.au'] = array_merge($params['rft.au'], $this->getMPGSecondaryAuthors());
            break;
        case 'info:ofi/fmt:kev:mtx:book':
        default:
            if ($field = $this->marcRecord->getField("773")) {
                $params['rft.atitle'] = $this->_getFirstFieldValue('245', array('a'));
                $params['rft.btitle'] = $this->_getFirstFieldValue('773', array('t'));
            }
            else {
                $params['rft.btitle'] = $this->_getFirstFieldValue('245', array('a'));
                $params['rft.tpages'] = $this->_getFirstFieldValue('300', array('a'));
            }
            $aut = $this->getMPGPrimaryAuthor();
            if ($aut) {
                $params['rft.aulast'] = strstr($aut, ',', true);
            }
            $params['rft.aucorp'] = $this->_getFirstFieldValue('110', array('a'));
            if ($this->_getFirstFieldValue('260', array('c'))) {
                $params['rft.date'] = $this->_getFirstFieldValue('260', array('c'));
            }
            else if ($this->_getFirstFieldValue('952', array('j'))) {
                $params['rft.date'] = $this->_getFirstFieldValue('952', array('j'));
            }
            else if ($this->marcRecord->getField("008")) {
                $f008 = $this->marcRecord->getField("008")->getData();
                if (strlen($f008) > 9) $params['rft.date'] = substr($f008,6,4);
                if (!preg_match("/[0-9][0-9][0-9][0-9]/", $params['rft.date'])) $params['rft.date'] = null;
            }
            if ($this->_getFirstFieldValue('952', array('h'))) {
                $params['rft.pages'] = $this->_getFirstFieldValue('952', array('h'));
            }
            else if ($this->_getFirstFieldValue('773', array('g'))) {
                $params['rft.pages'] = $this->_getFirstFieldValue('773', array('g'));
            }
            if ($params['rft.pages']) $params['rft.spage'] = strstr($params['rft.pages'], '-', true);

            if ($this->_getFirstFieldValue('773', array('x'))) {
                $params['rft.issn'] = $this->_getFirstFieldValue('773', array('x'));
            }
            else if ($this->_getFirstFieldValue('490', array('x'))) {
                $params['rft.issn'] = $this->_getFirstFieldValue('022', array('x'));
            }
            else if ($this->_getFirstFieldValue('022', array('a'))) {
                $params['rft.issn'] = $this->_getFirstFieldValue('022', array('a'));
            }
            if ($this->_getFirstFieldValue('773', array('z'))) {
                $params['rft.isbn'] = $this->_getFirstFieldValue('773', array('z'));
            }
            else if ($this->_getFirstFieldValue('020', array('a'))) {
                $params['rft.isbn'] = $this->_getFirstFieldValue('020', array('a'));
            }
            $params['rft.series'] = $this->_getFirstFieldValue('490', array('a'));
            if ($this->_getFirstFieldValue('773', array('b'))) {
                $params['rft.edition'] = $this->_getFirstFieldValue('773', array('b'));
            }
            else if ($this->_getFirstFieldValue('403', array('a'))) {
                $params['rft.exition'] = $this->_getFirstFieldValue('403', array('a'));
            }
            if ($this->_getFirstFieldValue('773', array('g'))) $params['rft_dat'][] = "<773>" . $this->_getFirstFieldValue('773', array('g')) . "</773>";
            if ($this->_getFirstFieldValue('502', array('a'))) $params['rft_dat'][] = "<502>" . $this->_getFirstFieldValue('502', array('a')) . "</502>";
            $params['rft_id'][] = $configArray['Site']['url'] . "/Record/" . $this->getUniqueID();
            if ($this->_getFirstFieldValue('024', array('2')) == "doi") {
                $params['rft_id'][] = "info:doi/" . $this->_getFirstFieldValue('024', array('a'));
            }
            else if ($urls = $this->marcRecord->getFields('856')) {
                foreach ($urls as $url) {
                    $u = $url->getSubfield('u');
                    if ($u) {
                        if(preg_match("/http:\/\/dx.doi.org\.{0,1}\/(.*?)(\?|$)/", $u->getData(), $doi)) {
                            $params['rft_id'][] = "info:doi/" . $doi[1];
                        }
                    }
                }
            }
            $params['rft.au'] = array();
            if ($this->_getFirstFieldValue('100', array('a'))) {
                $params['rft.au'][] = $this->_getFirstFieldValue('100', array('a'));
            }       
            if ($this->getMPGSecondaryAuthors()) $params['rft.au'] = array_merge($params['rft.au'], $this->getMPGSecondaryAuthors());

            break;
        }

        // Assemble the URL:
        $parts = array();
        foreach ($params as $key => $value) {
            if (is_array($params[$key])) {
                foreach ($params[$key] as $value) {
                    if (isset($value)) $parts[] = $key . '=' . urlencode($value);
                }
            }
            else {
                if (isset($value)) $parts[] = $key . '=' . urlencode($value);
            }
        }

        return implode('&', $parts);
    }

    protected function getDOI()
    {
        $doi = null;
        if ($this->_getFirstFieldValue('024', array('2')) == "doi") {
            $doi = $this->_getFirstFieldValue('024', array('a'));
        }
        else if ($urls = $this->marcRecord->getFields('856')) {
            foreach ($urls as $url) {
                $u = $url->getSubfield('u');
                if ($u) {
                    preg_match("/http:\/\/dx.doi.org\/(.*)/", $u->getData(), $doi);
                    $doi = $doi[1];
                }
            }
        }
        return $doi;
     }

}

?>
