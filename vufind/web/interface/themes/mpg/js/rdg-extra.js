<?php
Header ("Content-type: text/javascript");
$configArray = parse_ini_file('../../../../conf/config.ini', true);
?>
$(document).ready(function() {

// Startpage: toggle MPI publications list
$("#toggle_start_list_publications").toggle(function(event) {
     event.preventDefault ? event.preventDefault() : event.returnValue = false;
     $("#start_list_publications").toggle("fade");
//	$(this).html('hide... <span class="textarrow">&#x25B8;</span>');	
     $(this).remove();
}, function(event) {
      event.preventDefault ? event.preventDefault() : event.returnValue = false;
     $("#start_list_publications").toggle("fade");
        $(this).html('choose your list... <span class="textarrow">&#x25BE;</span>');
	});  

// Startpage: toggle recommendations lists
$("#toggle_start_list_recommendations").toggle(function(event) {
     event.preventDefault ? event.preventDefault() : event.returnValue = false;
     $("#start_list_recommendations").toggle("fade");
//	$(this).html('hide... <span class="textarrow">&#x25B8;</span>');	
     $(this).remove();
}, function(event) {
	 event.preventDefault ? event.preventDefault() : event.returnValue = false;
     $("#start_list_recommendations").toggle("fade");
	$(this).html('choose your list... <span class="textarrow">&#x25BE;</span>');
	});  

// Startpage: toggle ratio list
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
