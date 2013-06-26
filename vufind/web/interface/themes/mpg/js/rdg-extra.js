<?php
Header ("Content-type: text/javascript");
$configArray = parse_ini_file('../../../../conf/config.ini', true);
?>
$(document).ready(function() {
$("#toggle_ratio_lists").toggle(function(event) {
     event.preventDefault ? event.preventDefault() : event.returnValue = false;
     $("#ratio_lists").toggle("blind", {}, 500);
	$(this).html('Ratio <span class="textarrow">&#x25B8;</span>');	
}, function(event) {
	 event.preventDefault ? event.preventDefault() : event.returnValue = false;
     $("#ratio_lists").toggle("blind", {}, 500);
	$(this).html('Ratio <span class="textarrow">&#x25BE;</span>');
	});  


// Autocomplete: do a direct search (we have to rename <input name="submit"> to 
// <input name="submitButton"> or something else for jQuery.submit() to work.
// caveat: works only with mouse (not keyboard) selection)

    $(".yui-ac-content li").click(function() {
	var querystring = $(this).text();
	$("#lookfor").attr("value",querystring);
	$("#searchForm").submit();
    });

});
