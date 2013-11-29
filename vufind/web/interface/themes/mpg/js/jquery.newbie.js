<?php
Header ("Content-type: text/javascript");
$configArray = parse_ini_file('../../../../conf/config.ini', true);
?>
/* Newbie Mode */
/* Daniel Zimmel <zimmel@coll.mpg.de> */
/* depends heavily on DOM, might easily break if you change any templates */
/* language files: jquery.newbie.lang.de.js, jquery.newbie.lang.en.js */

/******************************* define functions */


/* some fancy effect, depends on jQuery-UI */
function runEffect(myId) {
    $("#"+myId).toggle("fold", {}, 800);
    $("#"+myId).removeAttr("id");
};

function closeTip() {
    $(".close-tip").click(function() {
	event.preventDefault(); // avoid scrolling to top
	$(this).parent(".Newbie").toggle();
    });
}

function NewbieHomepage() {
    $('body').prepend('<div class="Newbie" id="NewbieHomepage">'+lang.Banner+
		      '<form method="post" name="newbieForm" action="">'+
		      '<input type="hidden" name="newbieMode" value="off"></input>'+
		      '<input id="LeaveNewbieMode" type="submit" value="'+lang.BannerLeave+'" />'+
		      '</form>'+
		      '</div>');
    $('#LeaveNewbieMode').click(function(){
	$('#newbieModeOn').remove();
    });
    //runEffect("NewbieHomepage");
}

function NewbieLiteraturagent() {
    /* Homepage only! */
    if ($('.searchHome').length) {
	$('.searchheader:eq(0)').append('<div class="Newbie" id="NewbieLiteraturagent">'+
			     '<span class="nub top left"></span>'+lang.Literaturagent+
			     '<a href="#" class="close-tip">x</a></div></td></tr>');
	
	var pos = $('#clipbox1').position();
	newTop = pos.top;
	newLeft = pos.left;

	$('#NewbieLiteraturagent').css({
	    position:'absolute',
	    top:newTop+60,
	    left:newLeft+260,
	    zIndex:101
	});

	 runEffect("NewbieLiteraturagent");
    }
}


function NewbieNewAcq() {
    /* Homepage only! */
    if ($('.searchHome').length) {
	$('#ft').find('a:contains("Acquisitions"),a:contains("Neuerwerbungen")').parent().parent().append('<div class="Newbie" id="NewbieNewAcq">'+
							'<span class="nub bottom"></span>'+lang.NewAcq+
							'<a href="#" class="close-tip">x</a></div>');

	var pos = $('#ft').find('a:contains("Acquisitions"),a:contains("Neuerwerbungen")').position();
	newTop = pos.top;
	newLeft = pos.left;

	$('#NewbieNewAcq').css({
	    position:'absolute',
	    top:newTop-40,
	    left:newLeft+10,
	    zIndex:101
	});
	
	runEffect("NewbieNewAcq");
    }
}

function NewbieFacetten() {
    if ($(".narrowList").length){
    $('.sidegroup:first').prepend('<div class="Newbie" id="NewbieFacetten">'+
				  '<span class="nub bottom"></span>'+lang.Facetten+
				  '<a href="#" class="close-tip">x</a></div>');

	$('#NewbieFacetten').parents('.yui-b').css({
	    marginTop:'50px'
	});

    runEffect("NewbieFacetten");

    }
}

function NewbieZeitleiste() {
    $('#datevispublishDatex').parent().append('<div class="Newbie" id="NewbieZeitleiste">'+
					      '<span class="nub top"></span>'+lang.Zeitleiste+
					      '<a href="#" class="close-tip">x</a></div>');
    runEffect("NewbieZeitleiste");

}

function NewbieFindText() {
    $('a[id*="openUrlLink"]:eq(1)').parent().append('<div class="Newbie" id="NewbieFindText">'+
						    '<span class="nub top"></span>'+lang.OpenUrl+
						    '<a href="#" class="close-tip">x</a></div>');

    runEffect("NewbieFindText");

}

function NewbieLogIn() {
    if ($('#loginOptions:visible').length) {
	$('.searchheader:eq(0)').append('<div class="Newbie" id="NewbieLogIn">'+
					'<span class="nub top right"></span>'+lang.LogIn+
					'<a href="#" class="close-tip">x</a></div>');
	
	var pos = $('#loginOptions').position();
	newTop = pos.top;
	newLeft = pos.left;
	
	$('#NewbieLogIn').css({
	    position:'absolute',
	    top:newTop+40,
	    left:newLeft-160,
	    zIndex:101
	});

	runEffect("NewbieLogIn");

    }
}

function NewbieDetails() {
    if ($('#tabnav ul li a[href*="Description"]').length) {
	$('#tabnav ul li a[href*="Description"]').parents('#tabnav').before('<div class="Newbie" id="NewbieDetails">'+
									    '<span class="nub bottom"></span>'+lang.Details+
									    '<a href="#" class="close-tip">x</a></div>');
	
	var pos = $('#tabnav ul li a[href*="Description"]').position();
	newTop = pos.top;
	newLeft = pos.left;
	
	$('#NewbieDetails').css({
	    position:'absolute',
	    top:newTop-60,
	    left:newLeft-10
	});
	
	runEffect("NewbieDetails");
    }
}

function NewbieTags() {
    if ($('th:contains("Tags")').length) {
	$('th:contains("Tags")').parents("tr").prev("tr").after('<tr><th></th><td><div class="Newbie" id="NewbieTags">'+
									    '<span class="nub bottom right"></span>'+lang.AddTags+
									    '<a href="#" class="close-tip">x</a></div></td></tr>');
	
	$('#NewbieTags').css({
	    marginBottom:'20px'
	});
	
	runEffect("NewbieTags");
    }
}

function NewbieAddToFavorites() {
    if ($('#saveLink').length) {
	$('#saveLink').parents('ul').before('<div class="Newbie" id="NewbieAddToFavorites">'+
			     '<span class="nub bottom right"></span>'+lang.AddToFavorites+
			     '<a href="#" class="close-tip">x</a></div></td></tr>');
	
	var pos = $('#saveLink').position();
	newTop = pos.top;
	newLeft = pos.left;
	
	$('#NewbieAddToFavorites').css({
	    position:'absolute',
	    top:newTop-80,
	    left:newLeft-140,
	    zIndex:101
	});

	runEffect("NewbieAddToFavorites");
    }
}

function NewbieExportData() {
    if ($('.export:first').length) {
    $('.export:first').parents('ul').before('<div class="Newbie" id="NewbieExportData">'+
						    '<span class="nub top"></span>'+lang.ExportData+
						    '<a href="#" class="close-tip">x</a></div>');

    var pos = $('.export:first').position();
	newTop = pos.top;
	newLeft = pos.left;
	
	$('#NewbieExportData').css({
	    position:'absolute',
	    top:newTop+30,
	    left:newLeft,
	    zIndex:101
	});

    runEffect("NewbieExportData");
    }
}


function NewbieExplainSource() {
    if ($(".narrowList").length){
	$('.sidegroup:first').find('dl:last').before('<dl class="narrowList navmenu narrow_begin"><dd><div class="Newbie" id="NewbieExplainSource">'+
			 '<span class="nub top right"></span>'+lang.ExplainSource+
			 '<a href="#" class="close-tip">x</a></div></dd></dl>');

	$('#NewbieExplainSource').css({
	    position:'absolute',
	    zIndex:101,
	});

    runEffect("NewbieExplainSource");

    }

}


/*******************************  call functions */
$(document).ready(function() {
    /* insert a new button to switch to newbie mode (only on Search/Home!) */
    if ($('.searchHome').length) {
    $('.searchcontent:eq(0)').prepend('<form id="SwitchToNewbieMode" method="post" name="newbieForm" action="" style="float:left;">'+
				     '<input type="hidden" name="newbieMode" value="on"></input>'+
				     '<input class="SwitchToNewbieMode" type="submit" value="'+lang.SwitchToNewbieMode+'" style="display:none" />'+
				      '</form>');;
	/* fancy fade-in! */
	$('#SwitchToNewbieMode').children('input[type="submit"]').fadeIn('slow');
    }

    /* active mode? */
    if ($('#newbieModeOn').length) {
	$('#SwitchToNewbieMode').remove();
	NewbieHomepage();
	NewbieLiteraturagent();
	NewbieFacetten();
	NewbieZeitleiste();
	NewbieFindText();
	NewbieExplainSource();
	NewbieExportData();
	NewbieNewAcq();
	/* logged in: */
	if ($('#loginOptions:hidden').length) {
	    NewbieTags();
	    NewbieAddToFavorites();
	}
	/* not logged in: */
	if ($('#loginOptions:visible').length) {
	NewbieLogIn();
	NewbieDetails();
	}
	closeTip();
    }
});