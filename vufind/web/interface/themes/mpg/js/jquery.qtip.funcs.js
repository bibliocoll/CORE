/* Definitionen für dynamische Tooltips mit qtip2.js  */
/* RDG */
$(document).ready(function() {
<?php
Header ("Content-type: text/javascript");
$configArray = parse_ini_file('../../../../conf/config.ini', true);
?>    

    /*****************************************************************************/
    /* für JEL-Codes in "Details": Tooltips generieren und mit API-Inhalt füllen */
    /*****************************************************************************/
    // each, um Elemente ansprechen zu können (für "data"-Parameter)
    $(".classificationJEL > img").each(function() {
	$(this).qtip({
	    content: { 
		text: 'Loading...',
		ajax: {
		    // Bsp., falls jsonp (ohne PHP-Proxy-Funktion)
		    // url: 'http://zbw.eu/beta/stw-ws/suggest',
		    url: "<?php echo $configArray['Site']['path'];?>/AJAX/JSON?method=getJEL",
		    type: 'GET',
		    data: { 
			code: $(this).prev("a").attr("title")
			// dataset: "jel", // nur jsonp
			// lang: "en" // nur jsonp
		    },
		    dataType: 'json', // oder jsonp ohne PHP-Proxy
		    success: function (data, status) {

			// die "richtige" Klasse (weil erste):
			var code = data.results.bindings[0].term.value;
			// f. mehrere Suggest-Ergebnisse:
			var a = data.results.bindings;
			if (a.length > 1) {
			    var suggest = "<br/><br/><strong>see also:</strong><br/>";
			    for (var i = 1; i < a.length; ++i) { // i = 1 = ab zweitem Eintrag
				var suggest = suggest + "<br/>" + a[i].term.value;
			    }
			} else { 
			    var suggest = '';
			}
		
			var content = code + suggest;
			this.set('content.text',content);
		    }
		}
	    }
	});
    });

    /**********************************************************************/
    /* Facetten: bei Quellen einen Tooltip einbauen mit Erläuterungen     */
    /**********************************************************************/
/*    
    $('label[for="LibraryCatalog"]').append("&nbsp;<img src=\"<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/question-button.png\"/>");
    $('label[for="OLC"]').append("&nbsp;<img src=\"<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/question-button.png\"/>");
*/
    $(".sidegroup:first").find('dd:contains("Local Library Catalog"),dd:contains("Bibliotheksbestand")').qtip({
	content: {
		<?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'de') { ?>
	    text: 'Bibliothekskatalog: enthält tausende Bücher und Aufsätze, verfügbar in der Institutsbibliothek. Er beinhaltet Bestandsinformationen, gewährt direkten Zugriff auf einen großen Bestand an E-Books, und Sie können unsere lokale Systematik durchstöbern'
							  <?php } else { ?>
	    text: 'Library Catalog: includes thousands of books and articles available in the institute\'s library. It provides access to holdings information, direct access to a large set of e-books, and you can search our local classification'
									 <?php } ?>
	}, position: { my: 'bottom left', at: 'top center' }
    });

    $(".sidegroup:first").find('dd:contains("OLC")').qtip({
	content: {
		<?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'de') { ?>
            text: 'Online Contents: eine große Sammlung von Aufsätzen und Inhaltsverzeichnissen für eine Reihe wichtiger Zeitschriften. OLC enthält Aufsätze und Rezensionen von 1993 bis heute'  
							  <?php } else { ?>
	    text: 'Online Contents: a huge collection of articles and tables of contents for a lot of important journals. OLC contains articles and reviews from 1993 until today.'
									 <?php } ?>
	}, position: { my: 'bottom left', at: 'top center' }
    });

    $(".sidegroup:first").find('dd:contains("NL")').qtip({
	content: {
		<?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'de') { ?>
            text: 'Nationallizenzen: Im Rahmen von Nationallizenzen durch die Deutsche Forschungsgemeinschaft (DFG) bereitgestellte Ressourcen (Zeitschriften, Aufsätze...) verschiedener Disziplinen zur Nutzung in Deutschland'  
							  <?php } else { ?>
	    text: 'Nationally licenced content: Access to resources financed by The Deutsche Forschungsgemeinschaft (DFG) from anywhere in Germany to a large digital body of texts and subject databases from various fields'
									 <?php } ?>
	}, position: { my: 'bottom left', at: 'top center' }
    });

    $(".sidegroup:first").find('dd:contains("DOAJ")').qtip({
	content: {
		<?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'de') { ?>
            text: 'DOAJ: Directory of Open Access Journals: enthält Aufsätze und Zeitschriften, welche frei zugänglich publiziert worden sind (open access)'  
							  <?php } else { ?>
	    text: 'DOAJ: Directory of Open Access Journals: contains articles and journals that have been published freely accessible (open access)'
									 <?php } ?>
	}, position: { my: 'bottom left', at: 'top center' }
    });

    $(".sidegroup:first").find('dd:contains("Max-Planck E-Buch"),dd:contains("Max-Planck E-Book")').qtip({
	content: {
		<?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'de') { ?>
            text: 'Zentral in der Max-Planck-Gesellschaft lizenzierte E-Books'  
							  <?php } else { ?>
	    text: 'Site licensed e-books in the Max Planck Society'
									 <?php } ?>
	}, position: { my: 'bottom left', at: 'top center' }
    });

   $(".sidegroup:first").find('dd:contains("Zeitschriftenbibliothek"),dd:contains("Electronic Journal Library")').qtip({
	content: {
		<?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'de') { ?>
            text: 'Zusätzlich zu den lokal vorgehaltenen Zeitschriften enthält die Elektronische Zeitschriftenbibliothek alle zentral in der Max-Planck-Gesellschaft lizenzierten Zeitschriften'  
							  <?php } else { ?>
	    text: 'In addition to the locally licensed journals, the Electronic Journal Library contains all site licensed journals in the Max Planck Society'
									 <?php } ?>
	}, position: { my: 'bottom left', at: 'top center' }
    });

   $(".sidegroup:first").find('dd:contains("VUB")').qtip({
	content: {
		<?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'de') { ?>
            text: 'Ausgewählte Neuerscheinungen, bei Interesse für die Bibliothek bestellbar'  
							  <?php } else { ?>
	    text: 'Selected recently published titles that you can order via the library'
									 <?php } ?>
	}, position: { my: 'bottom left', at: 'top center' }
    });

    /*********************************************************************/
    /* für alle entsprechenden Klassen als einfachen Tooltip aktivieren  */
    /*********************************************************************/
    $(".tooltip[title]").qtip();






    /*****************************************************************************/
    /* ein temporärer fancy Beta-Hinweis-Effekt auf Startseite, hier nur der     */
    /* Einfachheit halber ausgelagert, damit wir keine neue js-Datei einbinden   */
    /* müssen für so einen Quatsch                                               */
    /*****************************************************************************/

    function runEffect() {
	var selectedEffect = "scale";
	var options = {};
	$("#dbetaText").toggle("bounce", {times: 3}, "fast");
	$("#dbetaText").removeAttr("id");
    };
    /* zeige Text nach 3 Sekunden automatisch: */
    setTimeout(function() {
	runEffect()}, 3000);

});
