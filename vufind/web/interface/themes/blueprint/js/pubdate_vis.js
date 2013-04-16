function loadVis(searchParams, baseURL) {
    // options for the graph, TODO: make configurable
    var options = {
        series: {
            bars: {
                show: true,
                align: "center",
                fill: true,
                fillColor: "rgb(0,0,0)"
            }
        },
        colors: ["rgba(255,0,0,255)"],
        legend: { noColumns: 2 },
        xaxis: { tickDecimals: 0 },
        yaxis: { min: 0, ticks: [] },
        selection: { mode: "x" },
        grid: { backgroundColor: null /*"#ffffff"*/ }
    };

    // AJAX call
    $.getJSON(baseURL + '/AJAX/JSON_Vis?method=getVisData&' + searchParams, function (data) {
        if(data.status == 'OK') {
            $.each(data['data'], function(key, val) {
                // plot graph
                var placeholder = $("#datevis" + key + "x");
                var plot = $.plot(placeholder, [val], options);
                // mark pre-selected area
                plot.setSelection({ x1: val['min'] , x2: val['max']});
                // selection handler
                placeholder.bind("plotselected", function (event, ranges) {
                    from = Math.floor(ranges.xaxis.from);
                    to = Math.ceil(ranges.xaxis.to);
                    location.href=val['removalURL'] + '&daterange[]=' + key + '&' + key + 'to=' + to + '&' + key + 'from=' + from;
                });
            });
        }
    });
}





