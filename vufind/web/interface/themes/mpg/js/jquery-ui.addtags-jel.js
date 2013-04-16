<?php
Header ("Content-type: text/javascript");
?>
/* Add tag: ZBW Web Services API (http://zbw.eu/beta/econ-ws/) */
/* zimmel@coll.mpg.de */
$(document).ready(function() {
jQuery("#tagselect").change(function() {
/* zurücksetzen per Default bei jedem Aufruf (um Cache-Werte zu vermeiden) */
jQuery("#tag").autocomplete({disabled:true});

/* Option 1: JEL Codes Suggest */
jQuery('select option[title="jel"]:selected').each(function(){
// Autocomplete wird erst hier aktiviert:
jQuery("#tag").autocomplete({disabled:false});

             jQuery( "#tag" ).autocomplete({
                     source: function(request,response) {
                             jQuery.ajax({
                                    url: "http://zbw.eu/beta/stw-ws/suggest",
                                    dataType: "jsonp",
                                    minLength: 1,
                                    data: {
                                            query: request.term, // = inputfield
                                            dataset: "jel",
                                            lang: "en"
                                    },
                                    success: function(data) {
                                             response(jQuery.map(data.results.bindings, function(item) {
                                                     //var m = item.term.value.indexOf('-');// Wert nur auf eigtl. Code kuerzen,s.u.
                                                     return { label: item.prefLabel.value, value: item.prefLabel.value.substr(0,3) }
                                                    }));
                                            }
                                    });
                        }
	     });
});

/* Option 2: STW Subject Headings Suggest */
jQuery('select option[title="stw"]:selected').each(function(){
// Autocomplete wird erst hier aktiviert:
jQuery("#tag").autocomplete({disabled:false});

             jQuery( "#tag" ).autocomplete({
                     source: function(request,response) {
                             jQuery.ajax({
                                    url: "http://zbw.eu/beta/stw-ws/suggest",
                                    dataType: "jsonp",
                                    minLength: 1,
                                    data: {
                                            query: request.term, // = inputfield
                                            dataset: "stw",
                                            lang: "en"
                                    },
                                    success: function(data) {
                                             response(jQuery.map(data.results.bindings, function(item) {
                                                     return { label: item.prefLabel.value, value: '"'+item.prefLabel.value+'"' }
                                                    }));
                                            }
                                    });
                        }
	     });
});

    }).trigger('change');
});
