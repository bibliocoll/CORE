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
	    // alert(res);
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
    if (isHoldings) {
    // loading spinner
    $('.recordsubcontent:first').prepend('<div id="SFXdirect" class="showFulltextLinkRecord">'+
					 '<span class="check" style="font-size:x-small">checking for full text</span>'+
					 '<img class="check" src="<?php echo $configArray['Site']['path'];?>/images/loading.gif" /></div>');
    
    var url = path + "/AJAX/JSON?method=getResolverLinks"
        + "&openurl=" + encodeURIComponent(openURL);
    
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
    		    if (response.status == 'OK' && response.data) {
			// inject hidden JSON response (we are lazy and use the default response, which unfortunately has some HTML markup)
			$('#SFXdirect').append('<div id="SFXinjection" style="display:none">'+response.data+'</div>');
		    	$('#SFXdirect .check').remove();
			// read from hidden injection, find first full text link only and append a full text button:
			$('#SFXinjection').find('div[class="openurls"]:first > ul > li > a:first').prependTo('#SFXdirect');
			$('#SFXdirect a').text(sfxButtonText);
			$('#SFXdirect a').attr('title','Full text via SFX');
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
    

function toggleCoreSummary() {
    $('#coreSummaryMoreShow').toggle();
    $('#coreSummaryMoreHide').toggle();
    $('#coreSummaryMoreText').toggle();
    $('#coreSummaryDots').toggle();
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

});
