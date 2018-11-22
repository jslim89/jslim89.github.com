---
layout: post
title: "jQuery Flot - Create a stackable bar graph and line graph combination"
date: 2014-07-07 21:34:47 +0800
comments: true
tags: 
- javascript
- flot
- graph
---

Plot a stack bar graph with line graph combination.

<div id="flot-1ine" style="height:210px">
    <div id="flot-1ine-loading">Loading...</div>
</div>

Look at the graph above, is that cool?

## 1. Include all necessary JavaScript files.

```html
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.min.js"></script>
<script src="http://cdn.jsdelivr.net/jquery.flot.tooltip/0.7.1/jquery.flot.tooltip.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.resize.min.js"></script>
<script src="jquery.flot.grow.js"></script> <!-- you can get from http://jumflot.jumware.com/examples/Experimental/grow.html -->
<script src="http://cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.stack.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-dateFormat/1.0/jquery.dateFormat.min.js"></script>
```

Notice that I'm using CDN for those library, of course you may host it yourself.

## 2. Add a placeholder for the graph

```html
<div id="flot-graph" style="height:210px"></div>
```

Simple? Because all the data are populated from javascript.

## 3. Settings for the graph

The **Settings** here is refer to color, label, effect, etc. Let's do it one by one

```js
var flot_options = {
    series: {
        grow: { // grow animation
            active: true, // activate the plugin
            steps: 50 // number of seperate steps will be shown from beginning to the end
        },
        stack: true, // is stackable?
        shadowSize: 2 // is the default size of shadows in pixels. Set it to 0 to remove shadows
    },
    grid: {
        hoverable: true, // once hover, it will highlight
        clickable: true,
        tickColor: '#f0f0f0', // the color in the background (i.e. cell border color)
        borderWidth: 0, // the border for the graph container
        color: '#333333' // the color of the label (on the right-hand-side)
    },
    xaxis:{
        tickDecimals: 0, // the number of decimals to display
        tickSize: 1, // the size of interval between 2 ticks
        tickFormatter: function(val, axis) {
            // you can format the label text to display
            return val;
        }
    },
    yaxis: { // same as x-axis
        ticks: 5,
        min: 0,
        tickDecimals: 0,
        tickFormatter: function(val, axis) {
            return val;
        }
    },
    tooltip: true, // whether to show tooltip
    tooltipOpts: {
        content: function(label, x, y) {
            // format the content of tooltip
            return parseInt(y) + ' ' + label;
        },
        defaultTheme: false,
        shifts: { // the position from the cursor
            x: 0,
            y: 20
        }
    }
};
```

Now we already done the general settings for the graph. Let's configure bar graph and line graph differently

```js
var line_options = {
    show: true, // whether to show the line between ticks
    lineWidth: 2, // the thickness of the line
    fill: true, // whether the shape should be filled, in this case will be the color below the line
    fillColor: { // the color for the fill
        colors: [{ // this is to set the gradient
            opacity: 0.0
        }, {
            opacity: 0.7
        }]
    }
};

var bar_options = {
    show: true,
    align: 'center', // alignment for the bar
    lineWidth: 0, // the border width of the bar
    fill: true, // fill with color?
    barWidth: 0.6 // the width of the bars in units of the axis
};
```

## 4. Populate data

```js
$.ajax({
    dataType: 'json',
    url: '/path/to/graph/data.php',
    success: populateGraph
});

function populateGraph(data) {
    var graph_data = [];

    graph_data.push({
        label: 'iOS', // the the label (show in the right side)
        bars: bar_options, // set the bar config defined just now
        color: '#d063e6', // set a different color for different bar
        data: data.ios // populate data to this bar
    });

    // same as above
    graph_data.push({label: 'Android', bars: bar_options, color: '#a4c739', data: data.android});

    graph_data.push({
        label: 'User',
        lines: line_options, // set the line graph config
        stack: false, // this is line graph, and not stackable, thus set to false
        color: '#00aeef',
        points: {radius: 5, show: true}, // the `circle` size for the line graph
        data: data.user
    });

    // set the overall data to the main graph with general settings
    $('#flot-graph').length && $.plot($('#flot-graph'), graph_data, flot_options);
}
```

Note that the format for the data should be in this form `[x, y]` _(i.e. `data.user` above)_

```json
[
    [0, 28],
    [1, 17],
    [2, 21],
    [3, 30],
    [4, 22],
    [5, 9],
    [6, 18],
    [7, 27]
]
```

You're done!!!

<script src="http://cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.min.js"></script>
<script src="http://cdn.jsdelivr.net/jquery.flot.tooltip/0.7.1/jquery.flot.tooltip.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.resize.min.js"></script>
<script src="http://jslim89.github.com/assets/posts/2014-07-07-jquery-flot-create-a-stackable-bar-graph-and-line-graph-combination/jquery.flot.grow.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.stack.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-dateFormat/1.0/jquery.dateFormat.min.js"></script>
<script>
$(function() {
    $.ajax({
        dataType: 'json',
        url: 'https://dl.dropboxusercontent.com/u/33436253/data/technical-blog/2014-07-07-jquery-flot-create-a-stackable-bar-graph-and-line-graph-combination.json',
        success: populateGraph
    });

    var flot_options = {
        series: {
            grow: {
                active: true,
                steps: 50
            },
            stack: true,
            shadowSize: 2
        },
        grid: {
            hoverable: true,
            clickable: true,
            tickColor: '#f0f0f0',
            borderWidth: 0,
            color: '#333333'
        },
        xaxis:{
            tickDecimals: 0,
            tickSize: 1,
            tickFormatter: function(val, axis) {
                return val;
            }
        },
        yaxis: {
            ticks: 5,
            min: 0,
            tickDecimals: 0,
            tickFormatter: function(val, axis) {
                return val;
            }
        },
        tooltip: true,
        tooltipOpts: {
            content: function(label, x, y) {
                return parseInt(y) + ' ' + label;
            },
            defaultTheme: false,
            shifts: {
                x: 0,
                y: 20
            }
        }
    };

    var line_options = {
        show: true,
        lineWidth: 2,
        fill: true,
        fillColor: {
            colors: [{
                opacity: 0.0
            }, {
                opacity: 0.9
            }]
        }
    };

    var bar_options = {
        show: true,
        align: 'center',
        lineWidth: 0,
        fill: true,
        barWidth: 0.6
    };

    function populateGraph(data) {
        var graph_data = [];

        graph_data.push({label: 'iOS', bars: bar_options, color: '#d063e6', data: data.ios});
        graph_data.push({label: 'Android', bars: bar_options, color: '#a4c739', data: data.android});
        graph_data.push({label: 'User', lines: line_options, stack: false, color: '#00aeef', points: {radius: 5, show: true}, data: data.user});

        $("#flot-1ine").length && $.plot($("#flot-1ine"), graph_data, flot_options);
    }
});
</script>
