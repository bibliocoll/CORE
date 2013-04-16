<?php
/**
 * Classification Browse
 *
 * @category VuFind
 * @package  Controller_Record
 * @author   Daniel Zimmel <zimmel@coll.mpg.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 */
require_once 'Action.php';
class Classification extends Action
{
    function launch()
    {
        global $interface;
        global $configArray;
 
        $interface->setPageTitle('Classification');
        $interface->setTemplate('classification.tpl');
 
        // Do Something Here

// folgendes kann man in der tpl-Datei via  {$blub} ausgeben:
//$blub = "Dies ist eine Test-Ueberschrift"; // hier wirds definiert
//$interface->assign('blub',$blub); // hier wirds assignt

        $interface->display('layout.tpl');
    }
}
?>
