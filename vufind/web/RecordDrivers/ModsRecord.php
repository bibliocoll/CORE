<?php
/**
 * MODS Record Driver
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
 * @author   Daniel Zimmel <zimmel@coll.mpg.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/other_than_marc Wiki
 */
require_once 'RecordDrivers/IndexRecord.php';

/**
 * MODS Record Driver
 *
 * This class is designed to handle MODS records.  Much of its functionality
 * is inherited from the default index-based driver.
 *
 * @category VuFind
 * @package  RecordDrivers
 * @author   Daniel Zimmel <zimmel@coll.mpg.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/other_than_marc Wiki
 */
class ModsRecord extends IndexRecord
{

    /* for SFX support, we will have to translate the function from the MarcRecord.php
    /* public function getMPGOpenURL() */
    /* { */
    /*     return null; */
    /* } */

  /* EXAMPLES */

/*   protected $modsRecord; */

/*     public function __construct($record) */
/*     { */
/*         // Call the parent's constructor... */
/*         parent::__construct($record); */

/*         $xml = trim($record['fullrecord']); */
/*         $mods = simplexml_load_string($xml); */

/*         $this->modsRecord = $mods; */
/*         //        if (!$this->marcRecord) { */
/*         //  PEAR::raiseError(new PEAR_Error('Cannot Process MARC Record')); */
/*         //} */
/*     } */

/*     protected function getShortTitle() */
/*     { */
/*         $title = isset($this->modsRecord->titleInfo->title) ? trim($this->modsRecord->titleInfo->title) : false; */
/*         return empty($title) ? "false" : $title; */
/*     } */

/*    protected function getPrimaryAuthor() */
/*    { // nur ein Dummy-Beispiel: ausgeben des zweiten Attributs. Sinnvollerweise macht man eine Prüfung: if attribute type="given" -> ausgeben, s. Datenstruktur MODS) */
 
/*       $title = isset($this->modsRecord->name->namePart[1]->attributes()->type) ? trim($this->modsRecord->name->namePart[1]->attributes()->type) : false; */
/*         return empty($title) ? "false" : $title; */
/*     } */

/* } */

?>
