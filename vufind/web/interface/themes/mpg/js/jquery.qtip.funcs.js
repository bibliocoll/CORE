/* Definitionen für dynamische Tooltips mit qtip2.js  */
/* Daniel Zimmel 2012 <zimmel@coll.mpg.de>            */
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
    /* für Suchbox/Shards: dynamisch Info-Buttons und Tooltips generieren */
    /**********************************************************************/
    $('label[for="LibraryCatalog"]').append("&nbsp;<img src=\"<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/question-button.png\"/>");
    $('label[for="OLC"]').append("&nbsp;<img src=\"<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/question-button.png\"/>");
    $("label[for='LibraryCatalog']").qtip({
	content: {
		<?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'de') { ?>
	    text: 'Bibliothekskatalog: enthält tausende Bücher und Aufsätze, verfügbar in der Institutsbibliothek. Er beinhaltet Bestandsinformationen, gewährt direkten Zugriff auf einen großen Bestand an E-Books, und Sie können unsere lokale Systematik durchstöbern'
							  <?php } else { ?>
	    text: 'Library Catalog: includes thousands of books and articles available in the institute\'s library. It provides access to holdings information, direct access to a large set of e-books, and you can search our local classification'
									 <?php } ?>
	}
    });
    $("label[for='OLC']").qtip({
	content: {
		<?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'de') { ?>
            text: 'Online Contents: eine große Sammlung von Aufsätzen und Inhaltsverzeichnissen für eine Reihe wichtiger Zeitschriften. OLC enthält Aufsätze und Rezensionen von 1993 bis heute'  
							  <?php } else { ?>
	    text: 'Online Contents: a huge collection of articles and tables of contents for a lot of important journals. OLC contains articles and reviews from 1993 until today.'
									 <?php } ?>
	}
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
	$("#dbetaText").toggle("bounce", {times: 3}, "slow");
	$("#dbetaText").removeAttr("id");
    };
    /* zeige Text nach 3 Sekunden automatisch: */
    setTimeout(function() {
	runEffect()}, 3000);

});
