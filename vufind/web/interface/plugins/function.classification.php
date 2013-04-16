<?php
/**
 * The sole purpose of this function is the live transformation of our classification from xml to html.
 * It looks quite silly but it is the lazy way to migrate our already existing code to VuFind.
 * TODO: It would be much better to rewrite the code and separate the layout into the Smarty template.
 * TODO: clean up for VuFind, better language integration, ...
 *
 * @category VuFind
 * @package  Smarty_Plugins
 * @author   Daniel Zimmel <zimmel@coll.mpg.de>
 * @author   Martin Pollet <pollet@coll.mpg.de>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     no link
 */

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.classification.php
 * Type:     function
 * Name:     classification
 * Purpose:  Displays xml files from a specific classification (MPI Bonn)

 * -------------------------------------------------------------
 *
 * @param array  $params  Incoming parameter array
 * @param object &$smarty Smarty object
 */



function smarty_function_classification($params, &$smarty)
{
// Style/Ansicht: nur als Test! (siehe unten css)
if (empty($_GET['ansicht'])) {
	$ansicht = "default"; //default
} else {
	$ansicht = $_GET['ansicht'];
}

// get language from Smarty-Template:
if ($params['language'] == "en") { 
   $language = $params['language'];
  } else if ($params['language'] == "de") {
    $language = $params['language'];
  } else { $language = "en";}
					     

$doc = new DOMDocument(); 

// Auswahl der gewuenschten Group:
 
if (empty($_GET['group'])) {
	$group = "BAP"; //default
} else {
	$group = $_GET['group'];
}

$kategorien=array();
 
// Funktion f. $path und $language

//function make_entry ($key,$url,$ger,$eng) {
//   global $kategorien;
//   $kategorien[$key]= array( 'url' => $url,
//			     'eng' => utf8_encode($eng),
//			     'ger' => utf8_encode($ger));
// }

$kategorien['BAP'] = array( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/bap.xml',
	      	       	    'de' => 'Bibliographischer Apparat',
		       	    'en' => 'Bibliographical Apparatus');
$kategorien['ENG'] = array( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/eng.merge.xml',
	      	       	    'de' => 'Energie',
		       	    'en' => 'Energy');
$kategorien['FIN'] = array ( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/fin.merge.xml',
		  	  'de' => 'Finanzen',
			  'en' => 'Finance');
$kategorien['GES'] = array ( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/ges.xml',
		  	  'de' => 'Geschichte',
			  'en' => 'History');
$kategorien['JUR'] = array ( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/jur.merge.xml',
		  	  'de' => 'Rechtswissenschaften',
			  'en' => 'Legal Science');
$kategorien['MPI'] = array ( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/mpi.merge.xml',
		  	  'de' => 'Methoden, Paradigmen, Institutionen',
			  'en' => 'Methods, Paradigms, Institutions');
$kategorien['PHI'] = array ( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/phi.xml',
		  	  'de' => 'Philosophie',
			  'en' => 'Philosophy');
$kategorien['POL'] = array ( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/pol.merge.xml',
		  	  'de' => 'Politikwissenschaften',
			  'en' => 'Political Science');
$kategorien['PSY'] = array ( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/psy.xml',
		  	  'de' => 'Psychologie',
			  'en' => 'Psychology');
$kategorien['SOZ'] = array ( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/soz.xml',
		  	  'de' => 'Soziologie',
			  'en' => 'Sociology');
$kategorien['STM'] = array ( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/stm.xml',
		  	  'de' => 'Wissenschaft, Technik, Medizin',
			  'en' => 'Science, Technics, Medicine');
$kategorien['TEL'] = array ( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/tel.merge.xml',
		  	  'de' => 'Telekommunikation',
			  'en' => 'Telecommunication');
$kategorien['UMW'] = array ( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/umw.merge.xml',
		  	  'de' => 'Umwelt',
			  'en' => 'Environment');
$kategorien['VER'] = array ( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/ver.merge.xml',
		  	  'de' => 'Verkehr',
			  'en' => 'Transport');
$kategorien['WIR'] = array ( 'url' => 'http://www.coll.mpg.de/bib/systematik/xml/wir.merge.xml',
		  	  'de' => 'Ökonomie',
			  'en' => 'Economics');


$xmlpath= $kategorien[$group]['url'];
$doc->load($xmlpath);

$xpath = new DOMXPath( $doc ); 

$links = $xpath->query( "//record" );

// fehlende XML-Felder abfangen und Xpath-Anfrage:
function myget ($query,$xpath) {
  $result=array();

  foreach ($xpath->query($query) as $item) {
    if (!empty($item->nodeValue)) { $result[]=trim($item->nodeValue);} 
  }
	
  switch (sizeof($result)) {
	case 0: return ""; break; // falls Feld fehlt
	case 1: return $result[0]; break; // einfaches Feld (String)
	default: return $result; // mehrfach vorkommende Felder (Array), -> siehe/siehe-auch
  }
}


$systematik = array();
 
foreach ( $links as $item ) {

	$newDom = new DOMDocument;
	$newDom->appendChild($newDom->importNode($item,true));
 

	$xpath = new DOMXPath( $newDom ); 

/* get the path for $searchlink from config.ini*/
global $configArray;
$vufindpath = $configArray['Site']['path'];


if ($language == "de") {
	$notation = myget("//varfield[@id='800']/subfield[@label='a']",$xpath);
	$searchlink = $vufindpath . '/Search/Results?lookfor=%22'.$notation.'%22&type=classification_local_short_txt_mv&view=list';
	$notationsbenennung = myget("//varfield[@id='803' and (@i1=' ' or @i1='a')]/subfield[@label='a']",$xpath);
	$siehe = myget("//varfield[@id='820' and @i1='a']/subfield[@label='a']",$xpath);
	$sieheauch = myget("//varfield[@id='821' and @i1='a']/subfield[@label='a']",$xpath);
	$hinweise = myget("//varfield[@id='808' and @i1='a']/subfield[@label='a']",$xpath);
} else if ($language == "en") {
	$notation = myget("//varfield[@id='800']/subfield[@label='a']",$xpath);
	$searchlink = $vufindpath . '/Search/Results?lookfor=%22'.$notation.'%22&type=classification_local_short_txt_mv&view=list';
	$notationsbenennung = myget("//varfield[@id='803' and @i1='e']/subfield[@label='a']",$xpath);
	$siehe = myget("//varfield[@id='820' and @i1='e']/subfield[@label='a']",$xpath);
	$sieheauch = myget("//varfield[@id='821' and @i1='e']/subfield[@label='a']",$xpath);
	$hinweise = myget("//varfield[@id='808' and @i1='e']/subfield[@label='a']",$xpath);
}


	// Notation ohne Leerzeichen (fuer Anchors):
	$notation_strip = str_replace(' ', '', $notation);

 // Kategorien abfragen (RDG)

	// Kategorie waehlen, vgl. function make_entry:
$category = $group." / ".$kategorien[$group][$language];


$systematik[] = array(
		      'notation_strip' => $notation_strip,
		      'notation' => $notation,
		      'notationsbenennung' => $notationsbenennung,
		      //	'id' => $id,
		      'searchlink' => $searchlink,
		      'hinweise' => $hinweise,
		      'siehe' => $siehe,
		      'sieheauch' => $sieheauch,
		      'category' => $category,
		      
		      );

	//xml-Datei ist bereits richtig sortiert, daher auskommentiert.
	//asort($systematik);
} 

echo '<div class="heading">'."\n";
echo $category."\n";
echo '</div>'."\n";

// Navigation

function highlight($sys,$group) {
if ($sys == $group) {
echo '<span class="highlight">';
} else {
echo '<span>';}
}

echo '<div id="navigation">'."\n";
foreach ($kategorien as $key=>$data) {
  if ($language == "de") {
    echo highlight($key,$group).'<span class="navigation"><a title='.$data['de'].' href="?group='.$key.'">'.$key.'</a></span></span>'."\n";
  } else { // default = Englisch
    echo highlight($key,$group).'<span class="navigation"><a title='.$data['en'].' href="?group='.$key.'">'.$key.'</a></span></span>'."\n";
  }
}
echo '</div>'."\n";

// Suche X-Server einbinden
//include 'include-search.php';

// Hauptverweisungen einbinden
//if ($language == "ger") {
//include 'include-hauptverweisung.php';
//} else if ($language == "eng") {
//	include 'include-hauptverweisung.ENG.php';
//}

echo '<div class="mainbox">'."\n";

$count = 1;
  
  foreach( $systematik as $item )  {

		//ab hier Pattern Matching für Formatierung

		// 100er-Groupn farblich abheben

		$comp_notation = $item["notation"];

		//STYLE-DIV
		/* if (preg_match("/[A-Z].+\s100$/", $comp_notation)) { */
		/* 	$style = "strong"; */
    /*   		} else { */
    /*   $style = "default"; */
		/* } */
		$style = "default";

		//Ueberschriften machen (basierend auf 100er-Groupn)

		$comp_heading = $item["notation"].":".$item["notationsbenennung"];  /* eingefuegter Trenner: ":" */

		if (preg_match("/[A-Z].+\s100:.+\/(.+)\//", $comp_heading, $matches_heading)) { /* eingefuegter Trenner: ":" */
			$heading = $matches_heading[1];
      		} else {
      $heading = false;
		}

		//Hierarchie erkennen / Anzahl der Slashes ("two" = Hierarchie eins etc.)

		$comp_notationsbenennung = $item["notationsbenennung"];

		if (preg_match("/([A-Z].+)\s\/\s(.+)\s\/\s(.+)\s\/\s(.+)\s\/\s(.+)/", $comp_notationsbenennung, $matches_four)) {
			$hierarchy = "four";
			$notationsbenennung = $matches_four[5];
		} else if (preg_match("/([A-Z].+)\s\/\s(.+)\s\/\s(.+)\s\/\s(.+)/", $comp_notationsbenennung, $matches_three)) {
			$hierarchy = "three";
			$notationsbenennung = $matches_three[4];
		} else if (preg_match("/([A-Z].+)\s\/\s(.+)\s\/\s(.+)/", $comp_notationsbenennung, $matches_two)){
			$hierarchy = "two";
			$notationsbenennung = $matches_two[3];
			//$notationsbenennung = $comp_notationsbenennung; 
		} else {
			$hierarchy = "two";
			$notationsbenennung = $comp_notationsbenennung; 
		}


		// ab hier Siehe/Siehe-Auch-Verweise definieren, Links bauen:

		// 1.: siehe

		// falls mehrere Verweise (Array):
		if (is_array($item["siehe"])) { 
			$vw_siehe=''; // leer starten, da weiter unten mit ".=" verkettet
		foreach ($item["siehe"] as $itm) {

			// normaler Fall, Notation vorhanden:
			if (preg_match("/([A-Z]{3})\s[a-z]\s[0-9]+/",$itm,$match)) {
			$target_strip = $match[1];
			$target = str_replace(' ', '', $match[0]);
			$vw_siehe .= '<br/><a class="siehe" href="?group='.$target_strip.'#'.$target.'">('.$itm.')</a>';
			} 
			// falls Notation nicht komplett vorhanden (z.B. nur Obergroup):
			else { $vw_siehe .= '<br><span class="siehe">'.$itm.'</span>';
			}
		}
		}
		// falls keine Verweisung gefunden (wegen Zeilenumbruechen):
		else if (empty($item["siehe"])) {
			$vw_siehe = "";
		}
		// falls einfacher Verweis:
		else {
			// normaler Fall, Notation vorhanden:
			if (preg_match("/([A-Z]{3})\s[a-z]\s[0-9]+/",$item["siehe"],$match)) {
			$target_strip = $match[1];
			$target = str_replace(' ', '', $match[0]);
			$vw_siehe = '<br/><a class="siehe" href="?group='.$target_strip.'#'.$target.'">('.$item["siehe"].')</a>';
			} 
			// falls Notation nicht komplett vorhanden (z.B. nur Obergroup):
			else { $vw_siehe = '<br><span class="siehe">'.$item["siehe"].'</span>';
			}
		}


		// 2.: siehe-auch

		// falls mehrere Verweise (Array):
		if (is_array($item["sieheauch"])) { 
			$vw_sieheauch=''; // leer starten, da weiter unten mit ".=" verkettet
		foreach ($item["sieheauch"] as $itm) {

			// normaler Fall, Notation vorhanden:
			if (preg_match("/([A-Z]{3})\s[a-z]\s[0-9]+/",$itm,$match)) {
			$target_strip = $match[1];
			$target = str_replace(' ', '', $match[0]);
			$vw_sieheauch .= '<br/><a class="sieheauch" href="?group='.$target_strip.'#'.$target.'">('.$itm.')</a>';
			} 
			// falls Notation nicht komplett vorhanden (z.B. nur Obergroup):
			else {$vw_sieheauch .= '<br><span class="siehe">'.$itm.'</span>';
			}
		}
		} 
		// falls keine Verweisung gefunden (wegen Zeilenumbruechen):
		else if (empty($item["sieheauch"])) {
			$vw_sieheauch = "";
		}
		// falls einfacher Verweis:
		else {
			// normaler Fall, Notation vorhanden:
			if (preg_match("/([A-Z]{3})\s[a-z]\s[0-9]+/",$item["sieheauch"],$match)) {
			$target_strip = $match[1];
			$target = str_replace(' ', '', $match[0]);
			$vw_sieheauch = '<br/><a class="sieheauch" href="?group='.$target_strip.'#'.$target.'">('.$item["sieheauch"].')</a>';
			}
			// falls Notation nicht komplett vorhanden (z.B. nur Obergroup):
			else { $vw_sieheauch = '<br><span class="siehe">'.$item["sieheauch"].'</span>';
			}
		}


		//Headings
		if (!empty($heading)) {
			echo '<div class="notationheading">'."\n";
			echo $heading."\n";
			echo '</div>'."\n";
		}

		echo '<div class="'.$style.'">'."\n";

		echo '<div class="notationbox '.$hierarchy.'">'."\n";

		// verlinkte Version:
		echo '<span class="notation"><a class="notation" href="'.$item["searchlink"].'" name="'.$item["notation_strip"].'">'.$item["notation"].'</a></span>: ';

		// unverlinkte Version:
		//echo '<span class="notation"><a name="'.$item["notation_strip"].'">'.$item["notation"].'</a></span>: ';
		echo $notationsbenennung;

		// Hinweise 808: (erstmal auskommentiert (i-Buttons)
/* if (!empty($item['hinweise'])) { */
/* 	echo '<img src="img/i-color.png" width="17" height="17" title="'.$item["hinweise"].'" name="'.$item["hinweise"].'"/>&nbsp;'; */
/* } else { */
/*   echo ''; */
/* } */

		echo '<span class="sieheauch">'.$vw_sieheauch. '</span>';
		echo '<span class="siehe">'.$vw_siehe. '</span>';
		//echo $item['hinweise'];
		//echo $item["notation"].': '.$item["notationsbenennung"].'&nbsp;';

		/* if (is_numeric($item['no_records'])) {
echo "(dazu <a target=\"top\" href=\"http://aleph1.mpg.de/F/?func=find-b&local_base=rdg01&find_code=NTA&request=".$item["notation"]."\">" .$item['no_records']. " Titel</a> in Aleph gefunden)";
} else {
echo "(<em>-</em>)"; 
} */
		echo '</div>'."\n";
		echo '<div class="trefferbox">'."\n";
		$notation=$item['notation'];



		echo '</div>'."\n";
		echo '<br clear="all">'."\n";
		echo '</div>'."\n\n";
		
		$count++;
		  }
echo '</div>'."\n";

// return "SMARTY VUFIND testausgabe systematik-function"; 

	}
?>

