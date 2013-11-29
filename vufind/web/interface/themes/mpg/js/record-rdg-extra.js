<?php
Header ("Content-type: text/javascript");
$configArray = parse_ini_file('../../../../conf/config.ini', true);
?>
/* Extra functions for Record view */
/* Daniel Zimmel <zimmel@coll.mpg.de */

/******************************* define functions */

/* get Aleph Borrower */
function getALEPHBorrower(id)
{
/* currently only checks for first item! */
    $(".checkAJAX:first").append('<span id="ajaxLoading">&nbsp;<img src="<?php echo $configArray['Site']['path'];?>/interface/themes/mpg/images/rdg/ajax.gif"/></span>');
 
    /* debugging: trigger an error */
    /* id = id.substr(19,25); */
    idaleph = id.substr(17,25);   
    var url = path + "/AJAX/RDG?method=getALEPHBorrower&doc_number=" + encodeURIComponent(idaleph);
 
    $.ajax({
	type: "GET",
	url: url,
	dataType: "text",
	success: function(res) {
	    $(".checkAJAX:first").replaceWith('ID '+res+' <a href="http://intern.coll.mpg.de/library/page/aleph-user#'+res+
					'" target="top">(Details/Name)</a>');
	},
	error: function() { $(".checkAJAX:first").replaceWith('Sorry, there was an error. Please <a href="mailto:biblio@coll.mpg.de">tell us!</a>') }

    });

}

/* fetch SFX response on pageload */
/* this is a complete rewrite of getResolverLinks (ajax_common.js) in jQuery */
function getResolverLinksDirect(openURL, id, strings)
{
    // get active only on Holdings Tab

    var isHoldings = $('#tabnav').find('li[class="active"] > a[href*="Holdings"]').attr("href");
    if (isHoldings) {	// get active only when there is no fulltext link in our data already (relies on existence of class in holdings.tpl!)
	if ($('.showFulltextLinkRecord').length == 0) {
	    // loading spinner
	    $('.recordsubcontent:first').prepend('<div id="SFXdirect" class="showFulltextLinkRecord">'+
						 '<span class="check" style="font-size:x-small">checking for full text</span>'+
						 '<img class="check" src="<?php echo $configArray['Site']['path'];?>/images/loading.gif" /></div>');
	    
	    var url = path + "/AJAX/JSON?method=getResolverLinks"
		+ "&from=direct&openurl=" + encodeURIComponent(openURL);
	    
	    $.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		success: function(response) {
		    // Check current language:
			<?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'de') { ?>
                         var sfxButtonText = "Volltext"
                        <?php } else { ?>
                         var sfxButtonText = "Get full text";
                        <?php } ?>
			// doublecheck: if no cookie is set, but browser decides on German 
			if ($('#mylang option:selected').val() == "de") {
			    var sfxButtonText = "Volltext";
			}
    		    if (response.status == 'OK' && response.data) {
			// inject hidden JSON response (we are lazy and use the default response, which unfortunately has some HTML markup)
			$('#SFXdirect').append('<div id="SFXinjection" style="display:none">'+response.data+'</div>');
		    	$('#SFXdirect .check').remove();
			// read from hidden injection, find first full text link only and append a full text button:
			var hasFulltextLinks = $('#SFXinjection').find('div[class="openurls"]:first > ul > li > a:first');
			if ($(hasFulltextLinks).length) {
			    // $('#SFXinjection').find('div[class="openurls"]:first > ul > li > a:first').prependTo('#SFXdirect');
			    $('#SFXdirect').append(hasFulltextLinks);
			    $('#SFXdirect a').text(sfxButtonText);
			    $('#SFXdirect a').attr('title','Full text via SFX');
			} else {
			    // show order button with some fancy jQuery effect!
			    $("#orderBoxLiteraturagent").toggle("clip");
			}
    		    } else if (response.data) {
    			strings.error = response.data;
    			this.error();
		    } else {
    			this.error();
    		    }
    		
	},
	error: function() { 
	    /* do nothing, silent fail */
	}

    });
	}
    }
}

/* if we do not have a TOC, try to fetch one via JSONP from the SeeAlso-Webservice */
function getSeeAlsoToC() {
    var isbn = $('#hiddenisbn').text();
    var url = "http://beacon.findbuch.de/articles/isbn-toc?format=seealso&id="+isbn;
 
    if (isbn) {
    $.ajax({
	type: "GET",
	url: url,
	dataType: "jsonp",
	success: function(res) {
	 // Check current language:
          <?php if (isset($_COOKIE['language']) && $_COOKIE['language'] == 'de') { ?>
            var TocText = "Inhaltsverzeichnis"
          <?php } else { ?>
            var TocText = "Table of contents";
          <?php } ?>
	 // doublecheck: if no cookie is set, but browser decides on German 
	   if ($('#mylang option:selected').val() == "de") {
	    var TocText = "Inhaltsverzeichnis";
	   }
	    var tocURL = res[3][0];
	    var tocSource = res[2][0];
	    if (tocURL) {
		$('div.recordsubcontent:first').prepend('<div class="showTocLinkRecord"><a href="'+tocURL+'" title="'+tocSource+'">'+TocText+'</a><br/></div>');
	    }
	},
	error: function() { 
	    /* do nothing, silent fail */
	}
	
    });    
    }
}

    

function toggleCoreSummary() {
    $('#coreSummaryMoreShow').toggle();
    $('#coreSummaryMoreHide').toggle();
    $('#coreSummaryMoreText').toggle();
    $('#coreSummaryDots').toggle();
}

/* showDownLinks, default: show only 50 */
function toggleDownLinks() {
    var next = $('.downLinkRow:hidden').slice(0,50);
    var nextLength = $(next).length;
    $(next).css("display","table-row");
    var indexTotal = $('.downLinkRow').length-1;
    if (indexTotal > 10) { var warnLimit = "(max. 1000)"; } else { var warnLimit = ''; }
    var indexVisible = $('.downLinkRow:visible').length-1;
    var someLeft = $('.downLinkRow:hidden').length;
    $('#showMoreDownLinks span').html('displaying '+indexVisible+' of '+indexTotal+' '+warnLimit);
    $('#showMoreDownLinks a:last').css("display","inline");
    $('#showMoreDownLinks').insertAfter('.downLinkRow:visible:last');
    if (someLeft == 0) {
	$('#showMoreDownLinks a').remove();
    }
}


/*******************************  call functions */
$(document).ready(function() {
    /* get Aleph Borrower */
    /* currently only appends to first item! */
    var id = $(".checkAJAX:first").attr("title");
    $(".checkAJAX:first").attr("style","display:inline");
    $(".checkAJAX:first").click(function() {
	getALEPHBorrower(id);
    });
    
    /* fetch SFX response on pageload */
    var mySFXURL = $('#mySFXURL').attr('title');
    /* do not use console.log, or else IE will fail silently
    /*console.log(mySFXURL);*/
    getResolverLinksDirect(mySFXURL,1, { error: '{translate text="An error has occurred"}' });

    /* show/hide description functionality*/
   $(".coreSummaryToggle").click(function() {
       toggleCoreSummary();
    });

    /* showDownLinks */
    $("#showMoreDownLinks a").click(function() {
	event.preventDefault ? event.preventDefault() : event.returnValue = false;
	toggleDownLinks();
    });

    /* try to fetch a ToC if we don't have one */
    var thereIsAToc = $('div.showTocLinkRecord').length;
    if (!thereIsAToc) {
	getSeeAlsoToC();
    }
});

