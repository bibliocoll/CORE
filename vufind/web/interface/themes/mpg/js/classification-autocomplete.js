<?php
Header ("Content-type: text/javascript");
$configArray = parse_ini_file('../../../../conf/config.ini', true);
?>
/* Focus setzen / Default loeschen onclick: */
jQuery(document).ready(function() {
    jQuery(".autocomplete:eq(0)").focus(function() {
	//$(this).val(''); // auskommentiert, IE Bug		 
    });						  
});
// Autocomplete:
// Hinweis: Suche beginnt nicht am Anfang, falls gewuenscht, ist eine Anpassung noetig:
// http://www.jensbits.com/2011/04/28/jquery-ui-autocomplete-search-from-beginning-of-string/

jQuery(".autocomplete:eq(0)").change(function () {
    // Dirty Trick to get the current language (I don't know how to make the Smarty Template variable available to the js)
    var myLanguage = jQuery("html").attr("lang");
    if (myLanguage == "de") {
	var file="<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/js/rdgnot.export-2012-ger.txt";
    } else {	
	var file="<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/js/rdgnot.export-2012-eng.txt";
    }
    jQuery.get(file, function(data) {
	var newTags = data.split("+");
	
        jQuery( ".autocomplete:eq(0)" ).autocomplete({
            source: newTags,
            minLength: 1,
	    /* fuer die Liste mit kompletten Benennungen muss noch ein callback starten (Benennungen sollen nicht ins input-Feld): */
            select: function(event,ui) {
                var m = ui.item.label.indexOf('-');
                var wert = ui.item.label.substr(0,m-1);
                // Wert fuellen
                jQuery(".autocomplete:eq(0)").val("\""+wert+"\"");
                // Form aktivieren
                jQuery("#autocompleteForm").attr("action","<?php echo $configArray['Site']['path'];?>/Search/Results");
                jQuery('#ClassificationSearchButton').replaceWith('<input id="ClassificationSearchButton"'+
                    'type="image" alt="OK" title="search"'+
                    'src="<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/go.png" width="25px" height="25px"'+
                    'border="0"'+
                    'style="display:inline;vertical-align:middle">');
                return false;
                /* ende Callback */
            }
        });
    });
}).trigger('change');
				     