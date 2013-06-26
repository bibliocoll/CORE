<?php
Header ("Content-type: text/javascript");
$configArray = parse_ini_file('../../../../conf/config.ini', true);
?>
/* Extra functions for Search module */
/* Daniel Zimmel <zimmel@coll.mpg.de> */

/* define functions */
function getALEPHBorrowerResults(elem)
{
    /* get record number from current element (relies on results list DOM) */
    var id=$(elem).parent(".status").attr("id");

    $(elem).append('<span id="ajaxLoading">&nbsp;<img src="<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/ajax.gif"/></span>');
 
    /* debugging: trigger an error */
    /* id = id.substr(19,25); */
    idaleph = id.substr(23,31);   
    var url = path + "/AJAX/RDG?method=getALEPHBorrower&doc_number=" + encodeURIComponent(idaleph);
 
    $.ajax({
	type: "GET",
	url: url,
	dataType: "text",
	success: function(res) {
	    // alert(res);
	    $(elem).replaceWith('ID '+res+' <a href="http://intern.coll.mpg.de/library/page/aleph-user#'+res+
					'" target="top">(Details/Name)</a>');
	},
	error: function() { $(elem).replaceWith('Sorry, there was an error. Please <a href="mailto:biblio@coll.mpg.de">tell us!</a>') }

    });

}



function getVlibSourcesNoResults(myset)
{
    $(".error:first").after('<a href="http://vlib.mpg.de/">'+
			    '<img src="<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/vlib.gif"/></a>'+
                            '<p id="ajaxInsert"><span id="ajaxLoading"><br/>'+
                            '<img src="<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/ajax-line.gif"/></span></p>');
 
    var url = path + "/AJAX/RDG?method=getVlibSources&myset=" + myset;
 
    $.ajax({
	type: "GET",
	url: url,
	dataType: "xml",
	success: function(res) {
	    // alert(res);
	    $("#ajaxLoading").replaceWith('Continue your search in other databases:<br/><br/>');

	    $(res).find('item').each(function() {
		var title = $(this).find('title').text();
		var link = $(this).find('link').text();
		var description = $(this).find('description').text().replace(/"/g,"");
		$("#ajaxInsert").append('<br/><span><img src="<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/question-button.png" class="tooltip" title="'+description+'"/>'+
                                        '<a href="'+link+'" target="top">'+title+'</a></span>');
	    });
	},
	error: function() { $("#ajaxLoading").replaceWith('Sorry, there was an error. Please <a href="mailto:biblio@coll.mpg.de">tell us!</a>') }

    });

}


function getVlibSourcesResults(myset)
{
    $("#vLibBox").after('<p id="ajaxInsert"><span id="ajaxLoading"><br/>'+
                        '<img src="<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/ajax-large.gif"/></span></p>');

     var url = path + "/AJAX/RDG?method=getVlibSources&myset=" +myset;
 
    $.ajax({
	type: "GET",
	url: url,
	dataType: "xml",
	success: function(res) {
	    // additional error handling:
	    var l = $(res).length;
	    if (l == 0) {
		$("#ajaxLoading").replaceWith('Sorry, there was an error. Please <a href="mailto:biblio@coll.mpg.de">tell us!</a>');
		return;
	    }
	    $("#ajaxLoading").replaceWith('<br/>');
	    $(res).find('item').each(function() {
		var title = $(this).find('title').text();
		var link = $(this).find('link').text();
		var description = $(this).find('description').text().replace(/"/g,"");
		$("#ajaxInsert").append('<br/><span><img src="<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/question-button.png" class="tooltip" title="'+description+'"/>'+
                                        '<a href="'+link+'" target="top">'+title+'</a></span>');
	    });
	},
	error: function() { $("#ajaxLoading").replaceWith('Sorry, there was an error. Please <a href="mailto:biblio@coll.mpg.de">tell us!</a>') }
    });
}


/* Deduplication (per single results page) */
/* depends heavily on DOM, and will most likely break apart */
function GroupDuplicateEntries() {
    var found = {};
    $(".title").each(function() {
	// clean up for valid HTML; for mbW try maybe .split('.')[0]
	// beware of funny characters, they might vanish from the results (.hide) if you do not match good enough!
        var title = $(this).text().replace(/[\s:,&"'%?$§@]/g,'-');
        var title = title.replace(/[<>]/g,'').toLowerCase();
	var author = $(this).parent().next().children('a').text().split(',')[0];
	var title = title+author; // make it more robust
	$(this).addClass(title); // add our new class
	// find duplicate entries with our new class
	if (found[title]){
	    var firstDup = $("."+title+":first").parents(".result");
	    $(this).parents(".result").hide(); // hide original entry
	    $(this).children(".yui-u").next().hide(); // hide SFX etc.
	    $(this).closest(".resultitem").appendTo(firstDup).addClass("dup"); // group
	    // add styling:
	    $(this).closest(".result").addClass("dup");
	    $(firstDup).children(".resultitem").prepend('<div class="duptext dup'+title+'">Other editions:</div>');
	    $(".dup"+title+":gt(0)").remove();
	// no duplicates:
	} else {
	    found[title] = true;
	}
    });
}

/* repeat a facet and display on top for emphasis */
function featuredFacet() {
    var featuredFacet = $('.sidegroup:first').find('dd:contains("Local Library Catalog"),dd:contains("Bibliotheksbestand")').html();
    if (!$('ul[class="filters"]').length && featuredFacet != null) {
    $(".sidegroup:first").find('dl:first').before('<div id="featuredFacet">'+featuredFacet+'</div>');
    }
}

/* call functions */
$(document).ready(function() {

// no results screen 
   if ($(".error:first").length) {
	getVlibSourcesNoResults("COLL");
    }

// results screen
    if ($("#vLibBox").length) {
	$("#vLibBox button").click(function() {
	    $("#ajaxInsert").remove(); // reset
	    var myset = $(this).attr("title");
	    getVlibSourcesResults(myset);
	});
    }

// group duplicates
    GroupDuplicateEntries();
// featured facet
    featuredFacet();

});
