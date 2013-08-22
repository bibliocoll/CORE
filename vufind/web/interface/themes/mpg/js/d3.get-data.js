<?php
Header ("Content-type: text/javascript");
$configArray = parse_ini_file('../../../../conf/config.ini', true);
?>
/* RDG 2013 */
/* here you can define which data will be processed with d3.ay-pie-chart.js */
/* because there is no ready-to-use JSON we do some ugly screenscraping with jQuery */
var get_facet_data = function(limit)
   {
       var data	= [];
       
       for(var i = 1; i < 20; i++) // use 'limit' if you want more than 20 (do not forget to add colors in css)
       {
           var m = i-1;
           var name = 
	       data.push({
		   index: i,
		   name: $('table.facetsTop tr:not("#visualizationheader") td:eq('+m+')').text(),
		   value: $('table.facetsTop tr:not("#visualizationheader") td:eq('+m+') > .facetCount').text().match(/\d+/)
	       });
       }
       
       return data;
   }

$(document).ready(function() {

    /* #showFacetVisualization is available in TopFacets.tpl */

    $('#showFacetVisualization').click(function() {

	// toggle images
	var origsrc = $(this).children('img').attr('src');
	var src = '';
	if (origsrc == '<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/piechart-thumb.png') {
            src = '<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/piechart-thumb-inactive.png'; }
	if (origsrc == '<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/piechart-thumb-inactive.png') {
            src = '<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/piechart-thumb.png'; }
	$(this).children('img').attr('src', src);

	// toggle text ('more' and 'less')
	var origtext = $(this).children('span').toggle();
	
	$('#narrowGroupHidden_topic_facet').toggle();
	
	// only fill on first click!
	if (!$('#visualizationbox').length) {
	    var facetsct = $('table.facetsTop td').length;
	    if (facetsct > 20) { var facetsct = '20'; }
	    $('#narrowGroupHidden_topic_facet').append('<tr id="visualizationheader"><td colspan="3">Top '+facetsct+' Subject Headings</td></tr>');
	    $('#narrowGroupHidden_topic_facet').append('<tr id="visualizationbox"><td colspan="3">'+
               '<div class="container"><div class="chart"><svg class="pie-a"></svg></div></div></td></tr>');
	
	    $(function(){
		var facetsct = $('table.facetsTop tr:not("#visualizationheader") td').length;
		ay.pie_chart('pie-a', get_facet_data(facetsct), {percentage: false});
	    });
	}	
    });

});