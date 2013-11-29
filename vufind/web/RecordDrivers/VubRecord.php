<?php
/**
 * VUB Record Driver
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010. 
 * Copyright (C) MPG 2013.
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
 * VUB Record Driver
 *
 * This class is designed to handle VUB records.  Much of its functionality
 * is inherited from the default index-based driver.
 *
 * @category VuFind
 * @package  RecordDrivers
 * @author   Daniel Zimmel <zimmel@coll.mpg.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/other_than_marc Wiki
 */
class VubRecord extends IndexRecord
{

   public function getCoreMetadata()
    {
        global $configArray;
        global $interface;

        // Assign required variables (some of these are also used by templates for
        // tabs, since every tab can assume that the core data is already assigned):
        $this->assignTagList();
        $interface->assign('isbn', $this->getCleanISBN());  // needed for covers
        $interface->assign('recordFormat', $this->getFormats());
        $interface->assign('recordLanguage', $this->getLanguages());

        // These variables are only used by the core template, and they are prefixed
        // with "core" to prevent conflicts with other variable names.
        $interface->assign('coreShortTitle', $this->getShortTitle());
        $interface->assign('coreSubtitle', $this->getSubtitle());
        $interface->assign('coreTitleStatement', $this->getTitleStatement());
        $interface->assign('coreTitleSection', $this->getTitleSection());
        $interface->assign('coreNextTitles', $this->getNewerTitles());
        $interface->assign('corePrevTitles', $this->getPreviousTitles());
        $interface->assign('corePublications', $this->getMPGPlacesOfPublication() ? $this->getMPGPlacesOfPublication() : $this->getPublicationDetails());
        $interface->assign('coreEdition', $this->getMPGEdition() ? $this->getMPGEdition() : $this->getEdition());
        $interface->assign('coreSeries', $this->getMPGSeries() ? $this->getMPGSeries(): $this->getSeries());
        $interface->assign('coreSubjects', $this->getAllSubjectHeadings());
        $interface->assign('coreRecordLinks', $this->getAllRecordLinks());
        $interface->assign('coreThumbMedium', $this->getThumbnail('medium'));
        $interface->assign('coreThumbLarge', $this->getThumbnail('large'));
        // RDG:
        $interface->assign('abrufzeichen', $this->getMPGAbrufzeichen());

        // Only display OpenURL link if the option is turned on and we have
        // an ISSN.  We may eventually want to make this rule more flexible,
        // but for now the ISSN restriction is designed to be consistent with
        // the way we display items on the search results list.
        $hasOpenURL = ($this->openURLActive('record')); // && $this->getCleanISSN());
        if ($hasOpenURL) {
            $interface->assign('coreOpenURL', $this->getMPGOpenURL());
        }

        // Only load URLs if we have no OpenURL or we are configured to allow
        // URLs and OpenURLs to coexist:
        if (!isset($configArray['OpenURL']['replace_other_urls'])
            || !$configArray['OpenURL']['replace_other_urls'] || !$hasOpenURL
        ) {
            $interface->assign('coreURLs', $this->getURLs());
        }

        // The secondary author array may contain a corporate or primary author;
        // let's be sure we filter out duplicate values.
        $mainAuthor = $this->getPrimaryAuthor();
        if ($MPGPrimaryAuthor = $this->getMPGPrimaryAuthor()) $mainAuthor = $MPGPrimaryAuthor;
        $corpAuthor = $this->getCorporateAuthor();
        if ($MPGCorporate = $this->getMPGCorporate()) $corpAuthor = $MPGCorporate;
        $secondaryAuthors = $this->getSecondaryAuthors();
        if ($MPGSecondaryAuthors = $this->getMPGSecondaryAuthors()) $secondaryAuthors = $MPGSecondaryAuthors;
        $duplicates = array();
        if (!empty($mainAuthor)) {
            $duplicates[] = $mainAuthor;
        }
        if (!empty($corpAuthor)) {
            $duplicates[] = $corpAuthor;
        } 
        if (!empty($duplicates)) {
            $secondaryAuthors = array_diff($secondaryAuthors, $duplicates);
        }
        $interface->assign('coreMainAuthor', $mainAuthor);
        $interface->assign('coreCorporateAuthor', $corpAuthor);
        $interface->assign('coreContributors', $secondaryAuthors);

        // Assign only the first piece of summary data for the core; we'll get the
        // rest as part of the extended data.
        $summary = $this->getSummary();
        $summary = count($summary) > 0 ? $summary : null;
        $interface->assign('coreSummary', $summary);

        // for MPG
//        $interface->assign('coreProduct', $this->getProduct());
        $interface->assign('f9843a', $this->get9843a());
        $interface->assign('f9843b', $this->get9843b());
        $interface->assign('f9843c', $this->get9843c());
        $interface->assign('f9842n', $this->get9842n());
        $interface->assign('f9842a', $this->get9842a());
        $interface->assign('f9842b', $this->get9842b());
        $interface->assign('f9842c', $this->get9842c());
        $interface->assign('MPGtitle', $this->getMPGtitle());
        $primaryTopAuthor = $this->getMPGtopPrimaryAuthor();
        $interface->assign('coreTopMainAuthor', $primaryTopAuthor);
        $TopAuthorsAll = $this->getMPGtopSecondaryAuthors();
        $duplicates = array();
        if (!empty($primaryTopAuthor)) {
            $duplicates[] = $primaryTopAuthor;
        }
        if (!empty($TopAuthorsAll)) {
            $secondaryTopAuthors = array_diff($TopAuthorsAll, $duplicates);
        }
        $interface->assign('MPGTopAuthorsAll', $TopAuthorsAll);
        $interface->assign('MPGsecondaryTopAuthors', $secondaryTopAuthors);
        $interface->assign('MPGSecondaryCorps', $this->getMPGSecondaryCorps());
        $interface->assign('MPGUpLink', $this->getMPGUpLink());
        $interface->assign('MPGDownLink', $this->getMPGdownLink());
        $interface->assign('MPGSeriesUpLink', $this->getMPGSeriesUpLink());
        $interface->assign('MPGPrevious', $this->getMPGPreviousLater("780"));
        $interface->assign('MPGNewer', $this->getMPGPreviousLater("785"));
        $interface->assign('MPGParallel', $this->getMPGPreviousLater("775"));
        $interface->assign('MPGHoldings', $this->getMPGHoldings());
        $interface->assign('MPGADAM', $this->getMPGADAM());
        $interface->assign('MPGSource', $this->getMPGSource());
        $interface->assign('coreGBVSource', $this->getGBVSource());
        $interface->assign('coreGBVJournalLink', $this->getGBVLink("773"));
        $interface->assign('coreCollections', $this->getCollection());

        // RDG: Seitenzahl anzeigen
        $interface->assign('corePhysical', $this->getPhysicalDescriptions());
	$interface->assign('coreMPGSubjectsRSWK', $this->getMPGSubjectHeadingsRSWK());
	$interface->assign('coreMPGSubjectsSTW', $this->getMPGSubjectHeadingsSTW());
	$interface->assign('coreMPGSubjectsSH', $this->getMPGSubjectHeadingsSH());

        // DOI für Altmetric
        $interface->assign('coreDOI', $this->getDOI());

        // Send back the template name:
        return 'RecordDrivers/Vub/core.tpl';
    }

    public function getHoldings($patron = false)
    {
        global $interface;
        global $configArray;

        if ("driver" == CatalogConnection::getHoldsMode()) {
            $interface->assign('driverMode', true);
            if (!UserAccount::isLoggedIn()) {
                $interface->assign('showLoginMsg', true);
            }
        }  
        
        // Only display OpenURL link if the option is turned on and we have
        // an ISSN.  We may eventually want to make this rule more flexible,
        // but for now the ISSN restriction is designed to be consistent with
        // the way we display items on the search results list.
        $hasOpenURL = ($this->openURLActive('holdings') && $this->getCleanISSN());
        if ($hasOpenURL) {
            $interface->assign('holdingsOpenURL', $this->getMPGOpenURL());
        }

        // Display regular URLs unless OpenURL is present and configured to
        // replace them:
        if (!isset($configArray['OpenURL']['replace_other_urls'])
            || !$configArray['OpenURL']['replace_other_urls'] || !$hasOpenURL        ) {
            $interface->assign('holdingURLs', $this->getURLs());
        }
        $interface->assign('holdingLCCN', $this->getLCCN());
        $interface->assign('holdingArrOCLC', $this->getOCLC());

        // Load real-time data if available:
        $interface->assign('holdings', $this->getRealTimeHoldings($patron));
        $interface->assign('history', $this->getRealTimeHistory());

        // RDG
        $interface->assign('availability', $this->getVubAvailability());

        return 'RecordDrivers/Vub/holdings.tpl';
    }

    public function getExtendedMetadata()
    {
        global $interface;

        // Assign various values for display by the template; we'll prefix
        // everything with "extended" to avoid clashes with values assigned
        // elsewhere.
        //        $interface->assign('extendedDescription', $this->getDescription());
        $interface->assign('extendedSummary', $this->getSummary());
        $interface->assign('extendedAccess', $this->getAccessRestrictions());
        $interface->assign('extendedRelated', $this->getRelationshipNotes());
        $interface->assign('extendedNotes', $this->getGeneralNotes());
        $interface->assign('extendedDateSpan', $this->getDateSpan());
        $interface->assign('extendedISBNs', $this->getISBNs());
        $interface->assign('extendedISSNs', $this->getISSNs());
        $interface->assign('extendedPhysical', $this->getPhysicalDescriptions());
        $interface->assign('extendedFrequency', $this->getPublicationFrequency());
        $interface->assign('extendedPlayTime', $this->getPlayingTimes());
        $interface->assign('extendedSystem', $this->getSystemDetails());
        $interface->assign('extendedAudience', $this->getTargetAudienceNotes());
        $interface->assign('extendedAwards', $this->getAwards());
        $interface->assign('extendedCredits', $this->getProductionCredits());
        $interface->assign('extendedBibliography', $this->getBibliographyNotes());
        $interface->assign('extendedFindingAids', $this->getFindingAids());

        return 'RecordDrivers/Vub/extended.tpl';
    }

    /* Summary + Description wird analog verwendet */
    protected function getSummary()
    {
        return isset($this->fields['description']) ?
            $this->fields['description'] : '';
    }

    protected function getVubAvailability()
    {
        return isset($this->fields['availability_str']) ?
            $this->fields['availability_str'] : 'lieferbar';
    }

    /* since we have images in the VUB-Data, get the field thumbnail! */
    protected function getThumbnail()
    {
        return isset($this->fields['thumbnail']) ?
          $this->fields['thumbnail'] : '';
    }

}

?>
