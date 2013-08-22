<?php
Header ("Content-type: text/javascript");
?>
function loadVis(searchParams, baseURL) {
    // options for the graph, TODO: make configurable
    var options = {
        bars: {
           show: true,
           align: "center",
           fill: true,
           fillColor: "#aecaeb" /* rgb(0,0,0) aecaeb 126497 */
        },
        colors: ["#126497"], /* rgba(255,0,0,255) */
        legend: { noColumns: 2 },
        xaxis: { tickDecimals: 0 },
        yaxis: { min: 0, ticks: [] },
        selection: { mode: "x" },
        grid: { backgroundColor: null /*"#ffffff"*/ } 
    };

    // AJAX URL

    var url = baseURL + '/AJAX/JSON_Vis?method=getVisData&' + searchParams;

    var callback =
    {
    success: function (transaction) {
        var data = eval('(' + transaction.responseText + ')');
        if(data.status == 'OK') {
            var values = data.data;
            for (var key in values) {
                // plot graph
                var placeholder = YAHOO.util.Dom.get('datevis' + key + 'x');
                var plot = {key: YAHOO.widget.Flot(placeholder, [values[key]], options)};
                // mark pre-selected area
                plot.key.setSelection({ xaxis: { from: values[key].min , to: values[key].max }});
                // selection handler
                plot.key.subscribe('plotselected', function (ranges) {
                    from = Math.floor(ranges.xaxis.from);
                    to = Math.ceil(ranges.xaxis.to);
                    location.href=values[key]['removalURL'] + '&daterange[]=' + key + '&' + key + 'to=' + to + '&' + key + 'from=' + from;
                });
            };
        }
    }
    }

    YAHOO.util.Connect.asyncRequest('GET', url, callback, null);
}





